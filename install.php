<?php
require_once __DIR__.'/config.php';
if((int)$db->query('SELECT COUNT(*) n FROM users')->fetch_assoc()['n']>0){header('Location: login.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$u=trim($_POST['username']??'');$n=trim($_POST['full_name']??'');$p=(string)($_POST['password']??'');
if(!preg_match('/^[A-Za-z0-9_.-]{3,60}$/',$u)||$n===''||strlen($p)<12)$error='Enter a valid username, full name, and password of at least 12 characters.';
else{$hash=password_hash($p,PASSWORD_DEFAULT);$s=$db->prepare("INSERT INTO users(username,password_hash,full_name,role) VALUES(?,?,?,'admin')");$s->bind_param('sss',$u,$hash,$n);$s->execute();audit($db,'INITIAL_ADMIN_CREATED','user',(string)$db->insert_id,'First administrator created');header('Location: login.php?installed=1');exit;}}
page_header('Initial setup');echo '<div class="card" style="max-width:600px;margin:auto"><h1>Administrator setup</h1><p>Create the first admin account. This page disables itself after an account exists.</p>';if($error)echo '<div class="notice error">'.h($error).'</div>';
echo '<form method="post"><input type="hidden" name="csrf" value="'.h(csrf_token()).'"><label>Full name</label><input name="full_name" required><label>Username</label><input name="username" required minlength="3" pattern="[A-Za-z0-9_.-]+"><label>Password (12+ characters)</label><input type="password" name="password" required minlength="12" autocomplete="new-password"><p><button>Create administrator</button></p></form></div>';page_footer();