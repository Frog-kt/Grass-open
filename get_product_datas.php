<?php

//商品情報を取得してAjaxでクライアントに送る

//いろいろ読み込み
include_once('./php/functions.php');

$dbh = DB::getHandle();

//すでに取得済みの商品IDを受け取る
$acquired_product_id = @$_POST['acquired_product_id'];
//$acquired_product_id = '30';

//　初回取得の場合には新しいものを30件持ってくる
//　取得済みの場合にはその商品IDよりも古いものを30件取得する
if($acquired_product_id === '0'){
    
    //　商品をDBから取得する
    //＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
    //もっと早いSQLを！！
    //$get_product_data = $dbh->prepare('SELECT * FROM grass_product_list WHERE id BETWEEN (SELECT COUNT(*) FROM grass_product_list)-29 AND (SELECT COUNT(*) FROM grass_product_list);');//1000ms
    $get_product_data = $dbh->prepare('SELECT * FROM grass_product_list WHERE id BETWEEN (SELECT MAX(id) FROM grass_product_list)-29 AND (SELECT MAX(id) FROM grass_product_list) AND flag = 0;');//400ms
    $get_product_data->execute();

    //　商品情報を配列に格納
    $product_data_array = $get_product_data->fetchAll(PDO::FETCH_ASSOC);

}else {

    //　商品をDBから取得する
    $get_product_data = $dbh->prepare('SELECT * FROM grass_product_list WHERE id BETWEEN :under_id AND :acquired_id AND flag = 0');
    $get_product_data->bindValue(':under_id', $acquired_product_id-29);
    $get_product_data->bindValue(':acquired_id', $acquired_product_id);
    $get_product_data->execute();
    
    //　商品情報を配列に格納
    $product_data_array = $get_product_data->fetchAll(PDO::FETCH_ASSOC);
    
}

//　clientにデータを投げる
$data_array = [];
$data_array['product_data_array'] = $product_data_array;

//var_dump($data_array);

//　jsonエンコード
header('Content-type: application/json');
echo json_encode($data_array);