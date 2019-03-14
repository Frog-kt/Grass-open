<?php
//商品ページ
$temp_html = './product_page.html';
require_once('./php/init.php');

//ログインチェック
if (check_login()) {
    $main_contents = file_get_contents('./product_page_login.html');
} else {
    $main_contents = file_get_contents('./no_login_message.html');
    @$html = preg_replace('/{{contents}}/', $main_contents, $html);
    echo ($html);
    exit;
}
@$html = preg_replace('/{{contents}}/', $main_contents, $html);

//商品IDを取得
$product_id = $_GET['product_id'];

//取得したIDから商品を取得して表示する
$dbh == DB::getHandle();

//戻り値用の配列を準備
$return_array = array();

//商品情報の取得
$get_product_list_data = $dbh->prepare('SELECT `id`, `product_id`, `name`, `price`, `flag`, `exhibitor`, `timestamp` FROM `grass_product_list` WHERE product_id = :product_id');
$get_product_list_data->bindValue(':product_id', $product_id);
$get_product_list_data->execute();
$list_data_array = $get_product_list_data->fetch(PDO::FETCH_ASSOC);

//商品の画像の取得
$get_product_img_path = $dbh->prepare('SELECT `img_path` FROM `grass_product_img_path` WHERE product_list_id = :id');
$get_product_img_path->bindValue(':id', $list_data_array['id']);
$get_product_img_path->execute();
$img_path_array = $get_product_img_path->fetchAll(PDO::FETCH_ASSOC);

//カテゴリの取得
$category_list_array = get_category($product_id);

//商品情報の取得
$get_product_detail = $dbh->prepare('SELECT `product_detail` FROM `grass_product_detail` WHERE id = :id');
$get_product_detail->bindValue(':id', $list_data_array['id']);
$get_product_detail->execute();
$detail_array = $get_product_detail->fetch(PDO::FETCH_ASSOC);

//配列のキーを指定してデータを格納
$datas = array(
    'product_id' => @$list_data_array['product_id'],
    'name' => @$list_data_array['name'],
    'exhibitor' => @$list_data_array['exhibitor'],
    'price' => @$list_data_array['price'],
    'img1' => @$img_path_array['0']['img_path'],
    'img2' => @$img_path_array['1']['img_path'],
    'img3' => @$img_path_array['2']['img_path'],
    'img4' => @$img_path_array['3']['img_path'],
    'detail' => @$detail_array['product_detail'],
    'cat1' => @$category_list_array['0']['category_name'],
    'cat2' => @$category_list_array['1']['category_name'],
    'cat3' => @$category_list_array['2']['category_name']
);

//からの配列を削除
$product_array_data = array_filter($datas, "strlen");


//カテゴリ情報を取得する
function get_category($product_id){

    $dbh = DB::getHandle();
    //商品のカテゴリIDを取得
    //プリペアの組み立て
    $get_product_category_list = $dbh->prepare('SELECT `category_id` FROM `grass_product_category_list` WHERE product_id = :product_id');
    
    //値のバインド
    $get_product_category_list->bindValue(':product_id',$product_id);
    $get_product_category_list->execute();
    $category_list_array = $get_product_category_list->fetchAll(PDO::FETCH_ASSOC);


    $category_array = array();

    //カテゴリを取得して配列に格納
    $count = count($category_list_array);

    for($i = 0; $i < $count;$i++){
        $id = $category_list_array[$i]['category_id'];
        //カテゴリ名を取得
        $get_product_category_name = $dbh->prepare('SELECT `category_name` FROM `grass_product_category` WHERE id = :id');
        //値のバインド
        $get_product_category_name->bindValue(':id',$id);
        $get_product_category_name->execute();
        $category_name_list_array = $get_product_category_name->fetch(PDO::FETCH_ASSOC);
        $category_array[$i] = $category_name_list_array;
    }
    return $category_array;
}

//取得したデータをHTMLにぶちこむ
$html = preg_replace('/{{product_title}}/',@$product_array_data['name'],$html);
$html = preg_replace('/{{price}}/',@$product_array_data['price'].'円',$html);
$html = preg_replace('/{{img1}}/',@'<img src="'.(@$product_array_data['img1']).'" alt="">',$html);
$html = preg_replace('/{{img2}}/',@'<img src="'.(@$product_array_data['img2']).'" alt="">',$html);
$html = preg_replace('/{{img3}}/',@'<img src="'.(@$product_array_data['img3']).'" alt="">',$html);
$html = preg_replace('/{{img4}}/',@'<img src="'.(@$product_array_data['img4']).'" alt="">',$html);
$html = preg_replace('/{{user_detail}}/',@$product_array_data['detail'],$html);
$html = preg_replace('/{{category_name1}}/',@$product_array_data['cat1'],$html);
$html = preg_replace('/{{category_name2}}/',@$product_array_data['cat2'],$html);
$html = preg_replace('/{{category_name3}}/',@$product_array_data['cat3'],$html);

//投稿者情報を取得する
$html = preg_replace('/{{user_name}}/',$product_array_data['exhibitor'],$html);
$html = preg_replace('/{{transaction_count}}/','0',$html);
$html = preg_replace('/{{user_assessment}}/','★★★★☆',$html);
$html = preg_replace('/{{product_id}}/',$product_id,$html);

//商品情報をPOSTで送るためのデータ
$html = preg_replace('/{{product_id}}/', $product_array_data['product_id'], $html);
$html = preg_replace('/{{product_name}}/', $product_array_data['name'], $html);
$html = preg_replace('/{{product_price}}/', $product_array_data['price'], $html);
$html = preg_replace('/{{product_exhibitor}}/', $product_array_data['exhibitor'], $html);

echo $html;