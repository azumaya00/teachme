<?php

//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('新規登録');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POST送信されているとき
if (!empty($_POST)) {
    //変数
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //バリデーション
    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if (empty($err_msg)) {

  //Emailチェック
        validEmail($email, 'email');
        validMaxLen($email, 'email', 255);
        validEmailDup($email, 'email');

        //パスワードチェック
        validPass($pass, 'pass');

        if (empty($err_msg)) {
            //同値チェック
            validMatch($pass, $pass_re, 'pass_re');

            if (empty($err_msg)) {
                debug('バリデーションOKです');
                //DB接続
                try {
                    $dbh = dbConnect();
                    $sql = 'INSERT INTO users (email,password,create_date,login_time) VALUES (:email,:pass,:create_date,:login_time)';
                    $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
          ':create_date' => date('Y-m-d H:i:s'),
          ':login_time' => date('Y-m-d H:i:s'));

                    //クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    //クエリ成功時
                    if ($stmt) {
                        debug('データ挿入成功');
                        //ログイン有効期限を1時間に
                        $sesLimit = 60*60;

                        //セッションに内容を詰める
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        //ユーザーID格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();

                        debug('セッション変数の中身: '.print_r($_SESSION, true));

                        header("location:successsignup.php");
                    }
                } catch (Exception $e) {
                    error_log('エラー発生:' .$e->getMessage());
                    $err_msg['common'] = MSG01;
                }
            }
        }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '新規登録';
require('head.php');
 ?>



<?php require('header.php'); ?>


<div id="contents" class="site-width">
    <section id="main">
        <h2 class="title">新規登録</h2>
        <div class="form-container">
            <div class="msgarea">
                <?php if (!empty($err_msg['common'])) {
     echo $err_msg['common'];
 } ?>
            </div>

            <form action="" method="post" class="form">
                <label class="<?php if (!empty($err_msg['email'])) {
     echo 'err';
 } ?>">Eメールアドレス<span
                        class="required">必須</span>　<span><?php echo getErrMsg('email'); ?></span>
                    <input type="text" name="email" placeholder="Eメールアドレス" value="<?php if (!empty($_POST['email'])) {
     echo $_POST['email'];
 } ?>">
                </label>
                <label class="<?php if (!empty($err_msg['pass'])) {
     echo 'err';
 } ?>">パスワード(半角英数字6文字以上)<span
                        class="required">必須</span>　<span><?php echo getErrMsg('pass'); ?></span>
                    <input type="password" name="pass" placeholder="パスワード" value="<?php if (!empty($_POST['pass'])) {
     echo $_POST['pass'];
 } ?>">
                </label>
                <label class="<?php if (!empty($err_msg['pass_re'])) {
     echo 'err';
 } ?>">パスワード(再入力)<span
                        class="required">必須</span>　<span><?php echo getErrMsg('pass_re'); ?></span>
                    <input type="password" name="pass_re" placeholder="パスワード(再入力)" value="<?php if (!empty($_POST['pass_re'])) {
     echo $_POST['pass_re'];
 } ?>">
                </label>
                <div class="btn-container">
                    <input type="submit" class="btn btn__yellow btn-mid" value="新規登録する!">

                </div>

            </form>
        </div>
    </section>
</div>

<?php require('footer.php');
