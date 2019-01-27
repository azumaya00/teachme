<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('退会ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//POST送信されているとき
if (!empty($_POST)) {
    //DB接続
    try {
        $dbh = dbConnect();
        $sql = 'UPDATE users SET delete_flg = 1 WHERE user_id = :us_id';
        $data = array('us_id' => $_SESSION['user_id']);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功時
        if ($stmt) {
            debug('クエリ成功');
            //セッション削除
            session_destroy();
            debug('セッション変数の中身'.print_r($_SESSION, true));
            debug('トップページへ遷移します');
            header("location:index.php");
        } else {
            debug('クエリ失敗');
            $err_msg['common'] = MSG01;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' .$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

<?php
$siteTitle = '退会';
require('head.php'); ?>

<?php require('header.php') ?>

<div id="contents" class="site-width">
  <section id="main">
    <h2 class="title">退会</h2>
    <div class="form-container">
      <div class="wrapper-form">
        <p>退会処理を行います。<br>
          一度退会しても同じメールアドレスで再登録することは可能ですが<br>
          これまでの投稿履歴は引き継げませんのでご注意下さい。</p>
        <br>
        <p>これまでのご利用ありがとうございました。</p>
      </div>
      <form action="" method="post">
        <div class="msgarea">
          <?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?>
        </div>
        <div class="btn-container__center">
          <input type="submit" value="退会する" class="btn btn__yellow btn-mid" name="submit">
        </div>
      </form>
    </div>

  </section>
</div>

<?php require('footer.php');
