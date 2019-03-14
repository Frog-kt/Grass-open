<?php

session_start();

//functions.phpの読み込み
include_once('./php/functions.php');

//htmlテンプレートの読み込み
$html = @file_get_contents($temp_html);

$dbh = DB::getHandle();

//ログイン状況に応じて<header>タグの中身が変わるようにする
if(check_login()){
    
    //ログインしているとき
    $header_list = file_get_contents('./header_temp_true.html');
}else {

    //ログインしていないとき
    $header_list = file_get_contents('./header_temp_false.html');
}

@$html = preg_replace('/{{head_list}}/', $header_list, $html);