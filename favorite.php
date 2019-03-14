<?php

//未実装

$temp_html = './favorite.html';

require_once('./php/init.php');

//ログインしていない場合にはログインを促す
if(check_login()){
    $main_contents = file_get_contents('./favorite_temp_login.html');
}else {
    $main_contents = file_get_contents('./no_login_message.html');
}

@$html = preg_replace('/{{contents}}/', $main_contents, $html);

echo($html);