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


//変数定義
$dbFormData = '';//ユーザ情報
$currentMinNum = '';//このページで最初に表示する記事
$topicCategory = '';//検索(カテゴリー)
$sort = '';//記事投稿日順
$listSpan = '';//1ページの記事数
$dbTopicData = '';//記事リスト
$dbCategoryData = '';//カテゴリー情報
$dbMyTopicData = '';//自分の投稿した記事
$dbMyCommentData = '';//自分の投稿した記事
$dbMyFavoriteData= '';//お気に入り記事


//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);

//投稿した記事一覧
//最新記事から
$currentMinNum = 0;
//カテゴリーは無し
$topicCategory = '';
//ソートは新しい順
$sort = 2;
//表示5件で
$listSpan = 5;

//自分の投稿した記事を取得
$dbMyTopicData = getMyTopicList($dbFormData['user_id'], $currentMinNum, $topicCategory, $sort, $listSpan);

//コメントした記事を取得
$dbMyCommentData = getMyCommentList($dbFormData['user_id'], $currentMinNum, $topicCategory, $sort, $listSpan);

//お気に入り記事を取得
$dbMyFavoriteData = getMyFavoriteList($dbFormData['user_id'], $currentMinNum, $topicCategory, $sort, $listSpan);



debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
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
        <?php foreach ($dbMyTopicData as $key =>$val) {
    ?>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i><a href="topicdetail.php?t_id=<?php echo $val['topic_id']; ?>"><?php echo $val['title']; ?></a></li>
        <?php
} ?>

      </ul>
      <a href="mytopiclist.php?mytype=1" class="more">もっと見る</a>
    </div>

    <div class="pickup">
      <h2 class="pickup-right">コメント記事</h2>
    </div>
    <div class="wrapper-topic mycomment">
      <ul class="topiclist">
        <?php foreach ($dbMyCommentData as $key =>$val) {
        ?>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i><a href="topicdetail.php?t_id=<?php echo $val['topic_id']; ?>"><?php echo $val['title']; ?></a></li>
        <?php
    } ?>
      </ul>
      <a href="mytopiclist.php?mytype=2" class="more">もっと見る</a>
    </div>

    <div class="pickup">
      <h2 class="pickup-left">お気に入り</h2>
    </div>
    <div class="wrapper-topic favortopic">
      <ul class="topiclist">
        <?php foreach ($dbMyFavoriteData as $key =>$val) {
        ?>
        <li class="topictitle"><i class="fas fa-caret-square-right"></i><a href="topicdetail.php?t_id=<?php echo $val['topic_id']; ?>"><?php echo $val['title']; ?></a></li>
        <?php
    } ?>
      </ul>
      <a href="mytopiclist.php?mytype=3" class="more">もっと見る</a>
    </div>




  </article>

  <?php require('sidebar.php'); ?>

</div>

<?php require('footer.php');
