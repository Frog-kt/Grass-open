<?php

//login処理を行う
session_start();
include_once("./php/functions.php");

$login_id = @$_POST['login_id'];
$login_pass = @$_POST['login_pass'];

if(!($login_id === '') && !($login_pass === '')){

    $auth = new auth($login_id, $login_pass);
    $bool = $auth->authentication($login_id, $login_pass);

    if($bool){
        //セッションIDを再生成
        session_regenerate_id(true);
        
        //ユーザー情報をセッション変数に格納する
        //データベースからユーザー情報を引っ張ってくる
        $dbh = DB::getHandle();
        $get_userdata = $dbh->prepare('SELECT `user_id`, `user_name` FROM `users` WHERE user_id = :user_id');
        $get_userdata->bindValue(':user_id', $login_id);
        $get_userdata->execute();
        $user_data = $get_userdata->fetch(PDO::FETCH_ASSOC);

        //セッションにIDとユーザー名を格納する
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['user_name'] = $user_data['user_name'];
    }
}else{
    $bool = false;
}

$awk = [];
$awk['auth'] = $bool;


header('Content-type: application/json');
echo json_encode($awk);