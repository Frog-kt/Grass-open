<?php

$temp_html = './payment.html';
require_once('./php/init.php');

if(check_login()){
    $main_contents = file_get_contents('./payment_login.html');
}else {
    $main_contents = file_get_contents('./no_login_message.html');
    @$html = preg_replace('/{{contents}}/', $main_contents, $html);
    echo($html);
    exit;
}
@$html = preg_replace('/{{contents}}/', $main_contents, $html);

//GETデータを取得+hidden属性のinputに入れる
var_dump($_GET);

//商品情報を次のページに転送する
$html = preg_replace('/{{product_id}}/', $_GET['product_id'], $html);
$html = preg_replace('/{{product_name}}/', $_GET['product_name'], $html);
$html = preg_replace('/{{product_price}}/', $_GET['product_price'], $html);
$html = preg_replace('/{{product_exhibitor}}/', $_GET['product_exhibitor'], $html);


echo($html);