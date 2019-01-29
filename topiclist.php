<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('記事一覧ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし
if (!empty($_SESSION['user_id'])) {
    //ログイン時はDBからユーザー情報取得
    $dbFormData = getUser($_SESSION['user_id']);
}

//現在のページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
//カテゴリーとソートは後でここに
//ページ数を直にいじった時はトップページへ
//if (!is_int($currentPageNum)) {
//    error_log('エラー発生: ページ指定に不正な値が入力されました');
//    header("Location:topiclist.php");
//}

//表示20件で
$listSpan = 20;
//新しい記事から表示
$sort = 2;
//このページで最初に表示する記事は何番目か
$currentMinNum = (($currentPageNum - 1) * $listSpan);
//総記事数と総ページ数を取得
$dbTopicCount = getTopicCount();
//カテゴリは最初は空
$topicCategory = '';
//記事リストを取得
$dbTopicData = getTopicList($currentMinNum, $topicCategory, $sort);
//カテゴリデータを取得
$dbCategoryData = getCategory();


debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = '投稿一覧';
require('head.php'); ?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
  <ariticle class="main-contents">
    <div class="main-menu">
      <h2 class="title">投稿一覧</h2>
      <form action="" method="post">
        <label>カテゴリー
          <select name="category" class="sortmenu">
            <option value="0">選択して下さい</option>
            <?php
foreach ($dbCategoryData as $key => $val) {
    echo '<option value="'.$val['category_id'].'">'.$val['category_name'].'</option>';
}
 ?>
          </select>
        </label>
        <label>ソート
          <select name="sort" class="sortmenu">
            <option value="0">選択して下さい</option>
            <option value="1">テスト1</option>
            <option value="2">テスト2</option>
          </select>
        </label>
      </form>

      <!-- ここからページネーション -->
      <?php pagenation($currentPageNum, $dbTopicCount['total_page']); ?>
      <!-- ここからページネーション -->


    </div>
    <!-- 記事一覧ここから -->
    <div class="wrapper-topic">
      <ul class="topiclist">
        <?php
        foreach ($dbTopicData as $key => $val) {
            echo '<li class="topictitle"><a href="topicdetail.php?t_id='.$val['topic_id'].'"><i class="fas fa-caret-square-right"></i>'.$val['title'].'</a></li>';
        }
        ?>
      </ul>
    </div>
    <!-- 記事一覧ここまで -->

    <!-- ここからページネーション -->
    <?php pagenation($currentPageNum, $dbTopicCount['total_page']); ?>
    <!-- ここからページネーション -->



  </ariticle>

  <?php require('sidebar.php'); ?>


</div>

<?php require('footer.php');
