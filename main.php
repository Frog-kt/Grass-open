<?php

//テンプレートのHTMLファイルパス
$temp_html = './main.html';

//いろいろ読み込み
include_once('./php/init.php');

$dbh = DB::getHandle(); 

@$html = @preg_replace('/{{products}}/', $product_data, $html);

//htmlの出力
echo($html);