<?php

$temp_html = './mypage.html';
require_once('./php/init.php');

//ログインしていない場合にはログインを促す
if(check_login()){
    $main_contents = file_get_contents('./mypage_temp_login.html');
}else {
    $main_contents = file_get_contents('./no_login_message.html');
    @$html = preg_replace('/{{contents}}/', $main_contents, $html);
    echo($html);
    exit;
}
@$html = preg_replace('/{{contents}}/', $main_contents, $html);

//ユーザーデータの取得
$get_user_data = new user($_SESSION['user_id']);
$user_data = $get_user_data->getUserData($_SESSION['user_id']);

//ユーザー情報の書き込み
$html = preg_replace('/{{user_id}}/', $user_data['user_id'], $html);
$html = preg_replace('/{{disp_name}}/', $user_data['user_name'], $html);
$html = preg_replace('/{{user_mail}}/', $user_data['user_mail'], $html);

echo($html);