<?php
require_once __DIR__.'/config.php';require_role(['admin']);$message='';$error='';
$tables=['users','employees','audit_logs'];
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$action=$_POST['action']??'';
if($action==='backup'){$data=['format'=>'hr-records-json-v1','created_at'=>date('c'),'tables'=>[]];foreach($tables as $t){$res=$db->query('SELECT * FROM `'.$t.'`');$data['tables'][$t]=$res->fetch_all(MYSQLI_ASSOC);}audit($db,'DATABASE_BACKUP_CREATED','database',null,'JSON backup downloaded');$json=json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);header('Content-Type: application/json; charset=utf-8');header('Content-Disposition: attachment; filename="hr_records_backup_'.date('Ymd_His').'.json"');echo $json;exit;}
if($action==='restore'){
 try{
  if(empty($_FILES['backup'])||$_FILES['backup']['error']!==UPLOAD_ERR_OK)throw new RuntimeException('Select a valid backup file.');
  if($_FILES['backup']['size']>20971520)throw new RuntimeException('Backup file must be 20 MB or smaller.');
  $raw=file_get_contents($_FILES['backup']['tmp_name']);$data=json_decode($raw,true,512,JSON_THROW_ON_ERROR);
  if(($data['format']??'')!=='hr-records-json-v1'||!isset($data['tables'])||!is_array($data['tables']))throw new RuntimeException('Unsupported backup format.');
  foreach($tables as $t)if(!isset($data['tables'][$t])||!is_array($data['tables'][$t]))throw new RuntimeException('Backup is missing required table data.');
  $columns=[
   'users'=>['id','username','password_hash','full_name','role','is_active','created_at'],
   'employees'=>['id','employee_no','first_name','middle_name','last_name','suffix','sex','birth_date','email','phone','address','department','position_title','employment_status','date_hired','salary','photo_path','document_path','notes','created_by','created_at','updated_at'],
   'audit_logs'=>['id','user_id','username','action','entity_type','entity_id','details','ip_address','created_at']
  ];
  foreach($tables as $t)foreach($data['tables'][$t] as $row){if(!is_array($row)||array_diff(array_keys($row),$columns[$t])||array_diff($columns[$t],array_keys($row)))throw new RuntimeException('Backup columns do not match expected schema.');}
  if(count($data['tables']['users'])<1)throw new RuntimeException('Restore rejected: backup contains no user accounts.');
  $db->begin_transaction();
  $db->query('SET FOREIGN_KEY_CHECKS=0');
  foreach(['audit_logs','employees','users'] as $t)$db->query('DELETE FROM `'.$t.'`');
  foreach($tables as $t){$cols=$columns[$t];$marks=implode(',',array_fill(0,count($cols),'?'));$names=implode(',',array_map(fn($c)=>'`'.$c.'`',$cols));$stmt=$db->prepare('INSERT INTO `'.$t.'` ('.$names.') VALUES ('.$marks.')');$types='';foreach($cols as $c)$types.=in_array($c,['id','is_active','created_by','user_id'],true)?'i':'s';
   foreach($data['tables'][$t] as $row){$values=[];foreach($cols as $c){$v=$row[$c];if($v===null)$values[]=null;elseif(in_array($c,['id','is_active','created_by','user_id'],true))$values[]=(int)$v;else $values[]=(string)$v;}$refs=[];foreach($values as $k=>&$value)$refs[$k]=&$value;unset($value);$stmt->bind_param($types,...$refs);$stmt->execute();}
  }
  $db->query('SET FOREIGN_KEY_CHECKS=1');$db->commit();session_regenerate_id(true);$message='Restore completed. Sign out and sign in again using an account from the restored backup.';
 }catch(Throwable $e){try{$db->rollback();$db->query('SET FOREIGN_KEY_CHECKS=1');}catch(Throwable $ignored){}$error=$e instanceof RuntimeException?$e->getMessage():'Restore failed. No changes should have been committed; verify the backup and database schema.';}
}
}
page_header('Backup and restore');echo '<h1>Backup and restore</h1>';if($message)echo '<div class="notice">'.h($message).'</div>';if($error)echo '<div class="notice error">'.h($error).'</div>';
echo '<div class="card"><h2>Download backup</h2><p>Downloads users, employee records, and audit logs as a JSON file. Employee photo/document files are stored separately; back up the uploads folder as well.</p><form method="post"><input type="hidden" name="csrf" value="'.h(csrf_token()).'"><input type="hidden" name="action" value="backup"><button>Download backup (.json)</button></form></div><div class="card"><h2>Restore from backup</h2><p><strong>Warning:</strong> restoring replaces all current user, employee, and audit-log data. Make a fresh backup first. Use only a trusted backup created by this application. Uploads must be restored separately.</p><form method="post" enctype="multipart/form-data" onsubmit="return confirm(\'This replaces all current users, employees, and audit logs. Continue only if you have a backup.\')"><input type="hidden" name="csrf" value="'.h(csrf_token()).'"><input type="hidden" name="action" value="restore"><label>Backup file (JSON, max 20 MB)</label><input type="file" name="backup" accept=".json,application/json" required><p><button class="danger">Restore backup</button></p></form></div>';
page_footer();