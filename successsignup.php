<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ユーザー登録完了');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

?>


<?php
$siteTitle = '登録が完了しました';
require('head.php');
?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
  <section id="main">
    <h2 class="title">ユーザー登録完了</h2>
    <div class="form-container">
      <div class="wrapper-form">
        <p>登録が完了しました！<br>プロフィール編集より、名前とアイコンを登録して下さい。</p>
      </div>
      <div class="btn-container__center">
        <div class="btn btn__yellow btn-mid"><a href="profedit.php">プロフィール編集へ</a></div>
      </div>
    </div>


  </section>
</div>

<?php require('footer.php');
