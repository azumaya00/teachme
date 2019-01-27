<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('認証キー入力ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし

//認証キーが発行されてないときは認証キー発行ページへ
if (empty($_SESSION['auth_key'])) {
    header("Location:passforgotten.php");
}

//POST送信されたとき
if (!empty($_POST)) {
    debug('POST送信があります');
    debug('POST情報: '.print_r($_POST, true));
    //変数定義
    $auth_key = $_POST['authkey'];
    //バリデーション
    //未入力
    validRequired($auth_key, 'authkey');
    if (empty($err_msg)) {
        debug('未入力チェックok');
        //桁数チェック
        validLength($auth_key, 'authkey');
        //半角英数チェック
        validHalf($auth_key, 'authkey');
        
        if (empty($err_msg)) {
            debug('バリデーションOKです');
            //セッションと同じかチェック
            if ($auth_key !== $_SESSION['auth_key']) {
                $err_msg['common'] = MSG14;
            }
            //セッション有効期限が切れていないか
            if ($_SESSION['auth_key_limit'] < time()) {
                $err_msg['common'] = MSG15;
            }

            if (empty($err_msg)) {
                debug('認証okです');
                //再発行パスワード生成
                $pass = makeRandKey();
                //DB接続
                try {
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                    $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);
                    //クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    //クエリ成功のとき
                    if ($stmt) {
                        debug('クエリ成功です');

                        //メール送信
                        $from = 'riekubocchi@gmail.com';
                        $to = $_SESSION['auth_email'];
                        $subject = 'パスワードを再発行しました | TeachMe!';
                        $comment = <<<EOD
パスワードを再発行しました。
下記パスワードでログイン後、パスワード編集画面より
パスワードを変更して下さい。

ログインページ: http://localhost:8888/teachme/login.php
パスワード: {$pass}

パスワード再発行手続きを行った覚えが無い場合は
お問い合わせ下さい。

###################
TeachMe! Project
test@test.com
###################
EOD;

                        sendMail($from, $to, $subject, $comment);

                        //セッション削除
                        session_unset();
                        //成功メッセ
                        $_SESSION['msg_success'] = SUC04;
                        debug('セッションの中身: '.print_r($_SESSION, true));
                        //ログインページへ
                        header("Location:login.php");
                    } else {
                        debug('クエリ失敗');
                        $err_msg['common'] = MSG01;
                    }
                } catch (Exception $e) {
                    error_log('エラー発生: '.$e->getMessage());
                    $err_msg['common'] = MSG01;
                }
            }
        }
    }
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '認証キー入力';
require('head.php'); ?>

<?php require('header.php') ?>
<!-- 成功メッセージ表示タグここから -->
<p id="show-msg" class="msg-modal" style="display:none">
  <?php echo getSessionFlash('msg_success'); ?>
</p>
<!-- 成功メッセージ表示タグここまで -->

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
        <p>Eメールに添付されている認証キーを入力して下さい</p>
      </div>
      <form action="" method="post">
        <label class="<?php if (!empty($err_msg['authkey'])) {
    echo 'err';
} ?>">認証キー　<span><?php echo getErrMsg('authkey'); ?></span>
          <input type="text" name="authkey">
        </label>
        <div class="btn-container">
          <input type="submit" class="btn btn__yellow btn-mid" value="送信する"></div>

      </form>
    </div>
</div>
</section>


<?php require('footer.php');
