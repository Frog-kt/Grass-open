<?php
session_start();

//functions.phpの読み込み
include_once('./php/functions.php');

//ログインしていない場合には処理しない。
if (!check_login()) {
    header('Location: ./index.php');
    exit;
}

//ユーザーIDの取得
$user_id = $_SESSION['user_id'];

//フォームデータの取得
//文字入力を行う部分をエスケープ処理する。
$product_name = htmlspecialchars($_POST['product_name']);
$product_price = htmlspecialchars($_POST['product_price']);
$product_detail = htmlspecialchars($_POST['product_detail']);

//カテゴリ情報の取得
$cat1 = $_POST['category_1'];
$cat2 = $_POST['category_2'];
$cat3 = $_POST['category_3'];

//ID用の乱数を取得
$product_id = uniqid(rand());

//カテゴリのバリデーション
$cat1 = checkCategory($cat1);
$cat2 = checkCategory($cat2);
$cat3 = checkCategory($cat3);

//カテゴリが入力されているか調べて配列に入れる
$cat_array = '';
if ($cat1 != "" || $cat2 != "" || $cat3 != "") {

    //カテゴリの重複をチェック
    if ($cat1 == $cat2) {
        $cat2 = "";
    }
    if ($cat2 == $cat3) {
        $cat3 = "";
    }
    if ($cat1 == $cat3) {
        $cat3 = "";
    }

    //カテゴリ情報を配列に突っ込む
    $cat_array = array($cat1, $cat2, $cat3);

    //カラ配列を削除して添字を振りなおす
    $cat_array = array_filter($cat_array, "strlen");
    $cat_array = array_values($cat_array);
}

//画像保存用のディレクトリ作成
mkdir('./dir/' . $product_id, 0755, true);

//画像があればリサイズと名前の変更を行う
for ($i = 0; $i <= 4; $i++) {
    
    //画像のファイルパスを取得する
    $img_path = img_rename_resize_move($product_id, $i);

    //画像がある場合には配列に画像のファイルパスを入れ、なくなったらbreakする
    if (!$img_path == false) {
        $file_path_array[$i] = $img_path;
    } else {
        break;
    }
}

$top_img_path = $file_path_array[0];

//DBに商品情報を登録する処理
$dbh = DB::getHandle();

//フラグを初期化（販売中）にする
$flag = 0;

//grass_product_listにデータを登録
$add_product_list = $dbh->prepare('INSERT INTO `grass_product_list`(`product_id`,`name`, `top_img_path`, `price`, `flag`, `exhibitor`) VALUES (:product_id, :product_name, :top_img_path, :product_price, :flag, :user_id)');
$add_product_list->bindValue(':product_id', $product_id);
$add_product_list->bindValue(':product_name', $product_name);
$add_product_list->bindValue(':top_img_path', $top_img_path);
$add_product_list->bindValue(':product_price', $product_price);
$add_product_list->bindValue(':flag', $flag);
$add_product_list->bindValue(':user_id', $user_id);
$add_product_list->execute();

//登録したときのidを取得
$product_list_id = $dbh->lastInsertId();
//var_dump($dbh->lastInsertId());

//商品の閲覧情報とお気に入り情報を初期化して登録する処理
$like = 0;
$watch = 0;
$add_product_watch = $dbh->prepare('INSERT INTO `grass_product_watch`(`product_id`, `like`, `watch`) VALUES (:product_id, :like, :watch)');
$add_product_watch->bindValue(':product_id', $product_id);
$add_product_watch->bindValue(':like', $like);
$add_product_watch->bindValue(':watch', $watch);
$add_product_watch->execute();

//商品情報をDBに登録する処理
$add_product_detail = $dbh->prepare('INSERT INTO `grass_product_detail`(`product_id`, `product_detail`) VALUES (:product_id, :product_detail)');
$add_product_detail->bindValue(':product_id', $product_id);
$add_product_detail->bindValue(':product_detail', $product_detail);
$add_product_detail->execute();

//商品カテゴリをDBに登録する処理
$cat_array_count = @count($cat_array);
for ($i = 0; $i < $cat_array_count; $i++) {
    $cat_id = $cat_array[$i];

    //grass_product_category_listにデータを登録
    $add_product_category_list = $dbh->prepare('INSERT INTO `grass_product_category_list`(`product_id`, `category_id`) VALUES (:product_id, :cat_id)');
    $add_product_category_list->bindValue(':product_id', $product_id);
    $add_product_category_list->bindValue(':cat_id', $cat_id);
    $add_product_category_list->execute();
}

//商品の画像をDBに登録する処理
$file_path_array_count = count($file_path_array);
for ($i = 0; $file_path_array_count > $i; $i++) {

    //取得した画像パスを表示
    $add_product_img_path = $dbh->prepare('INSERT INTO `grass_product_img_path`(`product_id`, `product_list_id`, `img_path`) VALUES (:product_id, :list_id, :img_path)');
    $add_product_img_path->bindValue(':product_id', $product_id);
    $add_product_img_path->bindValue(':list_id', $product_list_id);
    $add_product_img_path->bindValue(':img_path', $file_path_array[$i]);
    $add_product_img_path->execute();
}
if($file_path_array_count <= 0){
    //カラ画像の代わり
    $add_product_img_path = $dbh->prepare('INSERT INTO `grass_product_img_path`(`product_id`, `img_path`) VALUES (:product_id, :img_path)');
    $add_product_img_path->bindValue(':product_id', $product_id);
    $add_product_img_path->bindValue(':img_path', $file_path_array[$i]);
    $add_product_img_path->execute();
}


//画像のリサイズと名前の変更を行う関数
function img_rename_resize_move($product_id, $name)
{

    //保存用のディレクトリ情報
    $file_path = './dir/' . $product_id . '/'; 

    //保存用のディレクトリ情報 ./dir/プロダクトID/送信されてきたときのファイル名.jpg 
    $file_data = './dir/' . $product_id . '/' . basename($_FILES['img_0']['name']); 

    //正常にファイルが取得できたか調べる
    if (@is_uploaded_file($_FILES['img_' . $name]['tmp_name'])) {

        //ファイルを指定したディレクトリに移動させる 
        if (@move_uploaded_file($_FILES['img_' . $name]['tmp_name'], $file_data)) { 

            //画像の名前をランダムに 
            $file_name = (md5(uniqid(rand(), 1)) . '.jpg'); 

            //ファイル名を変更する 
            rename($file_data, $file_path . $file_name);

            //ファイルへの相対パスを変数に入れる (./dir/147ca25a5e0/bcd1a1f25ac7.jpg)
            $origin_img_path = $file_path . $file_name;

            //画像情報の取得
            list($width, $height, $type) = @getimagesize($origin_img_path);

            //画像形式のチェック
            switch ($type) {

                //JPEG.JPGのとき
                case IMAGETYPE_JPEG:

                    //イメージIDとやらを作るらしい？
                    $origin_img_id = imagecreatefromjpeg($origin_img_path);
                    $data_array = resize_img($origin_img_id, $width, $height, $type);

                    //配列の情報を変数に入れる
                    list($new_img_id, $resize_width, $resize_hight) = @$data_array;

                    //画像の複製とサイズ変更
                    imagecopyresized($new_img_id, $origin_img_id, 0, 0, 0, 0, $resize_width, $resize_hight, $width, $height);

                    //画像をファイルに保存
                    imagejpeg($new_img_id, $origin_img_path);

                    //メモリの解放
                    imagedestroy($new_img_id);
                    imagedestroy($origin_img_id);
                    break;

                //PNGのとき
                case IMAGETYPE_PNG:

                    //イメージIDとやらを作るらしい？
                    $origin_img_id = imagecreatefrompng($origin_img_path);
                    $data_array = resize_img($origin_img_id, $width, $height, $type);

                    //配列の情報を変数に入れる
                    list($new_img_id, $resize_width, $resize_hight) = @$data_array;

                    //画像の複製とサイズ変更
                    imagecopyresized($new_img_id, $origin_img_id, 0, 0, 0, 0, $resize_width, $resize_hight, $width, $height);

                    //画像をファイルに保存
                    imagepng($new_img_id, $origin_img_path);

                    //メモリの解放
                    imagedestroy($new_img_id);
                    imagedestroy($origin_img_id);
                    break;


                //GIFのとき
                case IMAGETYPE_GIF:

                    //イメージIDとやらを作るらしい？
                    $origin_img_id = imagecreatefromgif($origin_img_path);
                    $data_array = resize_img($origin_img_id, $width, $height, $type);

                    //配列の情報を変数に入れる
                    list($new_img_id, $resize_width, $resize_hight) = @$data_array;

                    //画像の複製とサイズ変更
                    imagecopyresized($new_img_id, $origin_img_id, 0, 0, 0, 0, $resize_width, $resize_hight, $width, $height);

                    //画像をファイルに保存
                    imagegif($new_img_id, $origin_img_path);

                    //メモリの解放
                    imagedestroy($new_img_id);
                    imagedestroy($origin_img_id);
                    break;

                //どれにも合致しない場合
                default:
                    throw new RuntimeException("サポートしていない画像形式です: $type");
            }
            return $origin_img_path;
            exit();

        } else {

            exit();
        }

    } else {
        return false;
        exit();
    }
}


//取得した画像の値からサイズの計算とかをして画像のサイズを変更する関数
function resize_img($file_path, $origin_width, $origin_height, $type)
{

    //横幅800pxで縦幅を可変にした。
    $resize_width = 800;
    $resize_hight = round($origin_height / ($origin_width / $resize_width));

    //変更後のイメージIDを作るらしい？
    $new_img_id = imagecreatetruecolor($resize_width, $resize_hight);

    return array($new_img_id, $resize_width, $resize_hight);
}


//カテゴリの値が正常値かどうか調べる
function checkCategory($category)
{
    //カテゴリが指定範囲以外であったらそのカテゴリを空にする。
    if (!($category == '')) {
        //カテゴリが範囲外の場合にはカラを戻り値として渡す。
        if (!(($category % 10) >= 1)) {
            return '';
        }else{
            return $category;
        }
    }
}


//メインページにぶっ飛ばす。
header('Location: ./main.php');
exit();