<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('認証キー発行ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証は無し

//POST送信されていたとき
if (!empty($_POST)) {
    debug('POST送信があります');
    debug('POST情報: '.print_r($_POST, true));
    //変数定義
    $email = $_POST['email'];
    //バリデーション
    //未入力
    validRequired($email, 'email');
  
    if (empty($err_msg)) {
        debug('未入力チェックokです');
        //メール形式
        validEmail($email, 'email');
        //最大文字数
        validMaxLen($email, 'email', 255);

        if (empty($err_msg)) {
            debug('バリデーションokです');

            //DB接続
            try {
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email = :email ';
                $data = array(':email' => $email);
                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                //DBに登録がある場合
                if (!empty($result['count(*)'])) {
                    //セッションに成功メッセ
                    $_SESSION['msg_success'] = SUC03;
                    //認証キー作成
                    $auth_key = makeRandKey();
                    //メール送信
                    $from = 'test@gmail.com';
                    $to = $email;
                    $subject = 'パスワード再発行認証キーのお知らせ | TeachMe!';
                    $comment = <<<EOD
パスワード再発行に必要な認証キーを発行しました。
以下のページで認証キーを入力して下さい。

パスワード再発行認証キー入力ページ: http://localhost:8888/teachme/passreissue.php
認証キー: {$auth_key}
※認証キーの有効期限は30分です。

認証キーを再発行する場合は、下記ページより再度お手続き下さい。
http://localhost:8888/teachme/passforgotten.php

パスワード再発行手続きを行った覚えが無い場合は
お問い合わせ下さい。

###################
TeachMe! Project
test@test.com
###################
EOD;

                    //メール送信
                    sendMail($from, $to, $subject, $comment);
                    //セッションに情報を詰める
                    $_SESSION['auth_key'] = $auth_key;
                    $_SESSION['auth_email'] = $email;
                    //認証キーリミット30分で設定
                    $_SESSION['auth_key_limit'] = time() + (60*30);
                    debug('セッション変数の中身: '.print_r($_SESSION, true));

                    //認証キー入力ページへ遷移
                    header("Location:passreissue.php");
                } else {
                    debug('クエリに失敗もしくはDBにない情報の入力');
                    $err_msg = MSG01;
                }
            } catch (Exception $e) {
                error_log('エラー発生:' .$e->getMessage());
                $err_msg['common'] = MSG01;
            }
        }
    }
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'パスワード再発行手続き';
require('head.php'); ?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
    <section id="main">
        <h2 class="title">パスワード再発行</h2>
        <div class="form-container">
            <div class="msgarea">
                <?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?>
            </div>
            <div class="wrapper-form">
                <p>ご指定のEメールアドレスに、パスワード再発行用のURLと認証キーを送信します</p>
            </div>
            <form action="" method="post">
                <label class="<?php if (!empty($err_msg['email'])) {
    echo 'err';
} ?>">Eメールアドレス　<span><?php echo getErrMsg('email'); ?></span>
                    <input type="text" name="email">
                </label>
                <div class="btn-container">
                    <input type="submit" value="送信する" class="btn btn__yellow btn-mid">
                </div>
            </form>


        </div>
    </section>
</div>


<?php require('footer.php') ;
