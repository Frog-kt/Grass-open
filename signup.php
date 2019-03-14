<?php

//テンプレートのHTMLファイルパス
$temp_html = './signup.html';

//いろいろ読み込み
include_once('./php/init.php');

if(isset($_SESSION['user_id'])){
    header('Location: ./index.php');
    exit();
}

$user_id = '';
$user_mail = '';
$user_name = '';
$user_pass = '';

//値が入力されているかしらべる
$user_id = @$_POST["user_id"];
$user_mail = @$_POST["user_mail"];
$user_name = @$_POST["user_name"];
$user_pass = @$_POST["user_pass"];

//重複のチェック
$dbh = DB::getHandle();
//IDの重複チェック
$check_id = $dbh->prepare('SELECT count(*)AS checkFlag FROM users WHERE user_id = :user_name');
$check_id->bindValue(':user_name', $user_id);
$check_id->execute();
$ovrewrap_flag_id = $check_id->fetchAll(PDO::FETCH_ASSOC);
$flag_id = @$ovrewrap_flag_id[0][checkFlag];

//メールアドレスの重複チェック
$check_mail = $dbh->prepare('SELECT count(*)AS checkFlag FROM users WHERE user_mail = :user_mail');
$check_mail->bindValue(':user_mail', $user_mail);
$check_mail->execute();
$ovrewrap_flag_mail = $check_mail->fetchAll(PDO::FETCH_ASSOC);
$flag_mail = @$ovrewrap_flag_mail[0][checkFlag];

if ($flag_id == 0) {
    if ($flag_mail == 0) {
        //重複していない場合には登録処理を行う
        $h_pass = password_hash($user_pass, PASSWORD_DEFAULT, ['cost' => 12]);

        //アカウント情報を登録するSQL
        $addUserSQL = $dbh->prepare('INSERT INTO users(user_id, user_name, user_mail, user_pass) VALUES(:user_id, :disp_name, :user_mail, :h_pass)');
        $addUserSQL->bindValue(':user_id', $user_id);
        $addUserSQL->bindValue(':disp_name', $user_name);
        $addUserSQL->bindValue(':user_mail', $user_mail);
        $addUserSQL->bindValue(':h_pass', $h_pass);

        //SQLの実行
        $addUserSQL->execute();
    }
}

//htmlの出力
echo ($html);