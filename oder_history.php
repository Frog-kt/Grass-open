<?php

$temp_html = './oder_history.html';

require_once('./php/init.php');

//ログインしていない場合にはログインを促す
if (check_login()) {
    $main_contents = file_get_contents('./oder_history_temp_login.html');
} else {
    $main_contents = file_get_contents('./no_login_message.html');
}

@$html = preg_replace('/{{contents}}/', $main_contents, $html);


$user_id = $_SESSION['user_id'];

//DBからデータを取得

//　商品をDBから取得する
$get_order_data = $dbh->prepare('select * from grass_oder_list where user_id = :user_id');
$get_order_data->bindValue(':user_id', $user_id);
$get_order_data->execute();

//　商品情報を配列に格納
$history_array = $get_order_data->fetchAll(PDO::FETCH_ASSOC);

//HTMLデータ
$history = "";

for ($i = 0; $i < count($history_array); $i++) {
    $history .= (
        "<tr><td>". ($i + 1) ."</td>".
        "<td>". $history_array[$i]['timestamp'] ."</td>".
        "<td>". $history_array[$i]['product_name'] ."</td>".
        "<td>". $history_array[$i]['product_price'] ."</td>".
        "<td>". $history_array[$i]['seller_id'] ."</td></tr>"
    );
}

$html = preg_replace('/{{oder_history}}/', $history, $html);

echo ($html);