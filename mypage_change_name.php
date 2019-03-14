<?php
session_start();
include_once('./php/functions.php');
$user_name = $_POST['new_disp_name'];
$user_pass = $_POST['check_password'];
$user_id = $_SESSION['user_id'];
$change_name = new user();
$change_name->changeUserDisplayName($user_id, $user_name, $user_pass);

header('Location: ./mypage.php');
exit();