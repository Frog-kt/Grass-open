<?php

//支払い確定後の処理
$temp_html = '';
require_once('./php/init.php');

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$seller_id = $_POST['product_id'];
$user_id = $_POST['product_exhibitor'];

var_dump($_POST);

//購入処理後
$add_oder_list = $dbh->prepare('INSERT INTO `grass_oder_list`(`product_id`, `product_name`, `product_price`, `seller_id`, `user_id`) VALUES(:product_id, :product_name, :product_price, :seller_id, :user_id)');
$add_oder_list->bindValue(':product_id', $product_id);
$add_oder_list->bindValue(':product_name', $product_name);
$add_oder_list->bindValue(':product_price', $product_price);
$add_oder_list->bindValue(':seller_id', $seller_id);
$add_oder_list->bindValue(':user_id', $user_id);

$add_oder_list->execute();


//購入された処品のフラグを変更する
$change_flag = $dbh->prepare('UPDATE grass_product_list SET flag = 1 WHERE product_id = :id');
$change_flag->bindValue(':id', $product_id);
$change_flag->execute();


//メインページにぶっ飛ばす。
header('Location: ./main.php');
exit();