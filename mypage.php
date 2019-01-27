<?php

//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);
 ?>

<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<?php require('header.php'); ?>

<!-- 成功メッセージ表示タグここから -->
<p id="show-msg" class="msg-modal" style="display:none">
<?php echo getSessionFlash('msg_success'); ?>
</p>
<!-- 成功メッセージ表示タグここまで -->

<div id="contents" class="site-width">
  <article class="main-contents">

    <div class="pickup">
      <h2 class="pickup-left">投稿した記事</h2>
    </div>
    <div class="wrapper-topic mytopic">
      <ul class="topiclist">
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
      </ul>
      <a href="#" class="more">もっと見る</a>
    </div>

    <div class="pickup">
      <h2 class="pickup-right">コメント記事</h2>
    </div>
    <div class="wrapper-topic mycomment">
      <ul class="topiclist">
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
      </ul>
      <a href="#" class="more">もっと見る</a>
    </div>

    <div class="pickup">
      <h2 class="pickup-left">お気に入り</h2>
    </div>
    <div class="wrapper-topic favortopic">
      <ul class="topiclist">
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i>何やらとにかく色々分かりません助けて下さい！！（合計３０文字）</li>
      </ul>
      <a href="#" class="more">もっと見る</a>
    </div>




  </article>

  <?php require('sidebar.php'); ?>

</div>

<?php require('footer.php');
