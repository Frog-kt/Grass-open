<?php

//データベースクラス
class DB
{
    private static $dbh;
    
    //他のクラスからnewできないようにする
    private function __construct()
    {
    }
    
    //DBハンドルの取得
    public static function getHandle()
    {

            //データベースハンドラ
        $db_user = "";
        $db_pass = "";
        $db_host = "";
        $db_name = "";
        $db_type = "mysql";
            
            //データソースネームを使って接続
        $dbh = new PDO("$db_type:hots=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);

        return $dbh;
    }
    //小クラスから呼べないようにする
    final function __close()
    {
        throw new \Exception('Clone is not allowed against' . get_class($this));
    }
}


//ログインチェック
function check_login()
{
    if (@$_SESSION['user_id'] == '' && @$_SESSION['user_name'] == '') {
        return false;
    } else {
        return true;
    }
}


//ユーザークラス
class user
{
    private $user_id;
    private $user_name;
    private $user_pass;
    private $user_mail;

    //ユーザー情報の取得
    public function getUserData($user_id)
    {
        $dbh = DB::getHandle();

        //ユーザーの ID,表示名,メールアドレスを取得する
        $get_userdata = $dbh->prepare('SELECT `user_id`, `user_name`, `user_mail` FROM `users` WHERE user_id = :user_id');
        $get_userdata->bindValue(':user_id', $user_id);
        $get_userdata->execute();
        $user_data = $get_userdata->fetch(PDO::FETCH_ASSOC);

        return $user_data;
    }

    //表示名の変更
    public function changeUserDisplayName($user_id, $user_name, $user_pass)
    {
        //ユーザー認証
        $auth = new auth($user_id, $user_pass);
        $bool = $auth->authentication($user_id, $user_pass);
        if ($bool) {
            $dbh = DB::getHandle();
        
            //表示名をアップデートする
            $update_user_name = $dbh->prepare('UPDATE users SET user_name = :user_name WHERE user_id = :user_id');
            $update_user_name->bindValue(':user_name', $user_name);
            $update_user_name->bindValue(':user_id', $user_id);
            $update_user_name->execute();
        }
    }

    //パスワードの変更処理
    public function changeUserPassword($user_id, $new_pass, $user_pass)
    {
        //ユーザー認証
        $auth = new auth($user_id, $user_pass);
        $bool = $auth->authentication($user_id, $user_pass);
        if ($bool) {
            $dbh = DB::getHandle();
            //データベースにハッシュ化したパスワードを入れる
            $new_pass = password_hash($new_pass, PASSWORD_DEFAULT, ['cost' => 12]);
            $update_user_data = $dbh->prepare('UPDATE users SET user_pass = :user_pass WHERE user_id = :user_id');
            $update_user_data->bindValue(':user_pass', $new_pass);
            $update_user_data->bindValue(':user_id', $user_id);
            $update_user_data->execute();
        }
    }

    //メールアドレスの変更
    public function changeUserMailaddress($user_id, $user_mail, $user_pass)
    {
        //ユーザー認証
        $auth = new auth($user_id, $user_pass);
        $bool = $auth->authentication($user_id, $user_pass);
        if ($bool) {
            $dbh = DB::getHandle();
            $update_mailaddress = $dbh->prepare('UPDATE users SET user_mail = :user_mail WHERE user_id = :user_id');
            $update_mailaddress->bindValue(':user_mail', $user_mail);
            $update_mailaddress->bindValue(':user_id', $user_id);
            $update_mailaddress->execute();
        }
    }
}


//認証全般を行うクラス
class auth
{
    private $user_id;
    private $user_pass;

    //ユーザー認証を行う処理
    public function authentication($user_id, $user_pass)
    {
        if ($this->getHashedPassword($user_id) === '') {
            
            //ユーザーが見つからない場合
            return false;
        } else {

            //ユーザーが見つかった場合
            $hash = $this->getHashedPassword($user_id);

            //パスワードの比較
            if (password_verify($user_pass, $hash)) {

                //認可処理
                return true;
            } else {

                //否認可処理
                return false;
            }
        }
    }

    //アカウント情報を探してハッシュを返す
    protected function getHashedPassword($user_id)
    {
        $dbh = DB::getHandle();
        $value = '';
        
        //データベースからユーザー名を検索して見つかったら1、見つからなかったら0になるSQL文を実行
        $countUserId = $dbh->prepare('SELECT count(*)AS counta FROM users WHERE user_id = :user_id');
        $countUserId->bindValue(':user_id', $user_id);
        $countUserId->execute();
        $counta = $countUserId->fetch(PDO::FETCH_ASSOC);

        //ユーザーが見つかった時にハッシュを取得する
        if ($counta['counta'] >= '1') {
            $getUserHash = $dbh->prepare('SELECT user_pass FROM users WHERE user_id = :user_id');
            $getUserHash->bindValue(':user_id', $user_id);
            $getUserHash->execute();
            $data = $getUserHash->fetch(PDO::FETCH_ASSOC);
            $value = $data['user_pass'];
        }

        //var_dump($value);
        return $value;
    }
}