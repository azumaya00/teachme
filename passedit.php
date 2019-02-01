<?php

//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード変更ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報: '.print_r($dbFormData, true));

//POSTされているとき
if (!empty($_POST)) {
    //変数定義
    $oldPass = $_POST['oldPass'];
    $newPass = $_POST['newPass'];
    $newPass_re = $_POST['newPass_re'];

    //バリデーション
    //未入力
    validRequired($oldPass, 'oldPass');
    validRequired($newPass, 'newPass');
    validRequired($newPass_re, 'newPass_re');

    if (empty($err_msg)) {
        debug('未入力チェックok');

        //古いパスワードチェック
        validPass($oldPass, 'oldPass');
        //新しいパスワードチェック
        validPass($newPass, 'newPass');
        //新パスワード再入力と一致するか
        validMatch($newPass, $newPass_re, 'newPass_re');

        //古いパスワードとDBパスワードの一致チェック
        if (!password_verify($oldPass, $dbFormData['password'])) {
            $err_msg['oldPass'] = MSG11;
        }

        //新しいパスワードと古いパスワードの一致チェック
        if ($oldPass === $newPass) {
            $err_msg['newPass'] = MSG12;
        }

        if (empty($err_msg)) {
            debug('バリデーションOKです');

            //DB接続
            try {
                $dbh = dbConnect();
                $sql = 'UPDATE users SET password = :pass WHERE user_id = :id';
                $data = array(':pass' => password_hash($newPass, PASSWORD_DEFAULT), ':id' => $_SESSION['user_id']);

                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                //クエリ成功の時
                if ($stmt) {
                    //セッションに成功メッセ
                    $_SESSION['msg_success'] = SUC02;
                    //メール送信準備
                    $username = $dbFormData['username'];
                    $from = 'test@gmail.com';
                    $to = $dbFormData['email'];
                    $subject = 'パスワードを変更しました | TeachMe!';
                    $comment = <<<EOD
{$username}さん

パスワードを変更しました。
パスワード変更手続きを行った覚えが無い場合は
お問い合わせ下さい。

###################
TeachMe! Project
test@test.com
###################
EOD;

                    sendMail($from, $to, $subject, $comment);
                    //マイページへ遷移
                    header("Location:mypage.php");
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
$siteTitle = 'パスワード変更';
require('head.php');
?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
    <section id="main">
        <h2 class="title">パスワード変更</h2>
        <div class="form-container">
            <div class="msgarea">
                <?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?>
            </div>
            <form action="" method="post">
                <label class="<?php if (!empty($err_msg['oldPass'])) {
    echo 'err';
} ?>">現在のパスワード　<span><?php echo getErrMsg('oldPass'); ?></span>
                    <input type="password" name="oldPass" value="<?php if (!empty($_POST['oldPass'])) {
    echo $_POST['oldPass'];
} ?>">
                </label>
                <label class="<?php if (!empty($err_msg['newPass'])) {
    echo 'err';
} ?>">新しいパスワード　<span><?php echo getErrMsg('newPass'); ?></span>
                    <input type="password" name="newPass" value="<?php if (!empty($_POST['newPass'])) {
    echo $_POST['newPass'];
} ?>">
                </label>
                <label class="<?php if (!empty($err_msg['newPass_re'])) {
    echo 'err';
} ?>">新しいパスワード(再入力)　<span><?php echo getErrMsg('newPass_re'); ?></span>
                    <input type="password" name="newPass_re" value="<?php if (!empty($_POST['newPass_re'])) {
    echo $_POST['newPass_re'];
} ?>">
                </label>
                <div class="btn-container">
                    <input type="submit" class="btn btn__yellow btn-mid" value="変更する">
                </div>
        </div>

    </section>
</div>

<?php require('footer.php');
