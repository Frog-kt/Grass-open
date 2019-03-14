<?php
session_start();
include_once('./php/functions.php');
$new_pass1 = $_POST['new_pass1'];
$new_pass2 = $_POST['new_pass2'];
$new_pass = '';
if($new_pass1 === $new_pass2){
    $new_pass = $new_pass1;
}
$user_pass = $_POST['now_pass'];
$user_id = $_SESSION['user_id'];
$change_name = new user();
//$change_name->changeUserPassword('KasuoTakumi', 'Baxdlob1211quit097p#', 'fasd');
$change_name->changeUserPassword($user_id, $new_pass, $user_pass);

header('Location: ./mypage.php');
exit();