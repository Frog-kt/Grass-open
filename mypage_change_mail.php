<?php
session_start();
include_once('./php/functions.php');
$user_mail = $_POST['new_mail'];
$user_pass = $_POST['check_password'];
$user_id = $_SESSION['user_id'];
$change_name = new user();
$change_name->changeUserMailaddress($user_id, $user_mail, $user_pass);

header('Location: ./mypage.php');
exit();