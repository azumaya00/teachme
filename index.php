<?php

//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('HOME');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if (!empty($_SESSION['user_id'])) {
    //ログイン時はDBからユーザー情報取得
    $dbFormData = getUser($_SESSION['user_id']);
}

//新着記事
//最新記事から
$currentMinNum = 0;
//カテゴリーは無し
$topicCategory = '';
//ソートは新しい順
$sort = 2;
//表示5件で
$listSpan = 5;

//新着記事リストを引っ張ってくる
$dbNewTopicData = getTopicList($currentMinNum, $topicCategory, $sort, $listSpan);

//人気記事はコメントの多い記事を持ってくる
$dbPopularTopicData = getPopularTopic();

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = 'HOME';
require('head.php'); ?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
  <article class="main-contents">
    <div class="pickup">
      <h2 class="pickup-left">新着記事</h2>
    </div>
    <div class="wrapper-topic newtopic">
      <ul class="topiclist">
        <?php
        foreach ($dbNewTopicData as $key => $val) {
            echo '<li class="topictitle"><a href="topicdetail.php?t_id='.$val['topic_id'].'"><i class="fas fa-caret-square-right"></i>'.$val['title'].'</a></li>';
        }
        ?>
      </ul>
      <a href="topiclist.php" class="more">もっと見る</a>
    </div>

    <div class="pickup">
      <h2 class="pickup-right">人気記事</h2>
    </div>
    <div class="wrapper-topic populertopic">
      <ul class="topiclist">
        <?php
        foreach ($dbPopularTopicData as $key => $val) {
            echo '<li class="topictitle"><a href="topicdetail.php?t_id='.$val['topic_id'].'"><i class="fas fa-caret-square-right"></i>'.$val['title'].'</a></li>';
        }
        ?>
      </ul>
    </div>




  </article>



  <?php require('sidebar.php'); ?>




</div>

<?php require('footer.php');
