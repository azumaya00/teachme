<?php

//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//POST送信されているとき
if (!empty($_POST)) {
    //変数定義
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //バリデーション
    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');

    if (empty($err_msg)) {
        debug('未入力チェックok');
        //Eメールチェック
        validEmail($email, 'email');
        validMaxLen($email, 'email', 255);

        //パスワードチェック
        validPass($pass, 'pass');

        if (empty($err_msg)) {
            debug('バリデーションOK');
            //DB接続
            try {
                $dbh = dbConnect();
                $sql = 'SELECT password,user_id FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email' => $email);

                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                debug('クエリ結果の中身: '.print_r($result, true));

                //パスワードと照合
                if (!empty($result) && password_verify($pass, array_shift($result))) {
                    debug('パスワードが一致しました');

                    //ログイン有効期限を1時間に
                    $sesLimit = 60*60;
                    //最終ログインを現在のタイムスタンプに
                    $_SESSION['login_date'] = time();

                    //ログイン保持の有無
                    if ($pass_save) {
                        debug('ログイン保持チェックあり');
                        $_SESSION['login_limit'] = $sesLimit * 24*30;
                    } else {
                        debug('ログイン保持チェックなし');
                        $_SESSION['login_limit'] = $sesLimit;
                    }
                    //ユーザーID格納
                    $_SESSION['user_id'] = $result['user_id'];
                    debug('セッション変数の中身'.print_r($_SESSION, true));
                    //マイページへ移動
                    debug('マイページへ遷移します');
                    header("location:mypage.php");
                } else {
                    debug('パスワード不一致');
                    $err_msg['common'] = MSG09;
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
$siteTitle = 'ログイン';
require('head.php'); ?>

<?php require('header.php'); ?>
<!-- 成功メッセージ表示タグここから -->
<p id="show-msg" class="msg-modal" style="display:none">
    <?php echo getSessionFlash('msg_success'); ?>
</p>
<!-- 成功メッセージ表示タグここまで -->

<div id="contents" class="site-width">
    <section id="main">
        <h2 class="title">ログイン</h2>
        <div class="form-container">
            <div class="msgarea">
                <?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?>
            </div>
            <form action="" method="post">
                <label class="<?php if (!empty($err_msg['email'])) {
    echo 'err';
} ?>">Eメールアドレス　<span><?php echo getErrMsg('email') ?></span>
                    <input type="text" name="email">
                </label>
                <label class="<?php if (!empty($err_msg['pass'])) {
    echo 'err';
} ?>">パスワード　<span><?php echo getErrMsg('pass') ?></span>
                    <input type="password" name="pass">
                </label>

                <label>
                    <input type="checkbox" name="pass_save">次回から自動でログインする
                </label>

                <div class="btn-container">
                    <input type="submit" class="btn btn__yellow btn-mid" value="ログイン">
                </div>
                <span class="notice">パスワードを忘れた方は<a href="passforgotten.php">こちら</a></span>




            </form>
        </div>


    </section>
</div>


<?php require('footer.php');
