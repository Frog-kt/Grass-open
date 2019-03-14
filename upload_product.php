<?php

$temp_html = './upload_product.html';

require_once('./php/init.php');

//ログインしていない場合にはログインを促す
if(check_login()){
    $main_contents = file_get_contents('./upload_product_login.html');
}else {
    $main_contents = file_get_contents('./no_login_message.html');
}

@$html = preg_replace('/{{contents}}/', $main_contents, $html);

echo ($html);