<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('自分の記事一覧ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//変数定義
$dbFormData = '';//ユーザ情報
$currentPageNum = '';//現在のページ
$currentMinNum = '';//このページで最初に表示する記事
$topicCategory = '';//検索(カテゴリー)
$sort = '';//記事投稿日順
$listSpan = '';//1ページの記事数
$dbTopicCount = '';//総記事数と総ページ数
$dbTopicData = '';//記事リスト
$dbCategoryData = '';//カテゴリー情報

//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);


//現在のページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
//ページ数を直にいじった時はトップページへ
if (!is_int((int)$currentPageNum)) {
    error_log('エラー発生: ページ指定に不正な値が入力されました');
    header("Location:topiclist.php");
}

//表示20件で
$listSpan = 20;
//このページで最初に表示する記事は何番目か
$currentMinNum = (($currentPageNum - 1) * $listSpan);
//カテゴリ、デフォルトはなし
$topicCategory = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
//カテゴリ、デフォルトはなし
$topicMyType = (!empty($_GET['mytype'])) ? $_GET['mytype'] : '';
//日付、デフォルトは新しい記事から表示
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : 2;
//総記事数と総ページ数を取得
$dbTopicCount = getMyTopicCount($dbFormData['user_id'], $topicCategory);
debug('記事数: '.print_r($dbTopicCount, true));
//カテゴリデータを取得
$dbCategoryData = getCategory();

//記事リストを取得
if ($topicMyType == 1) {
    $dbTopicData = getMyTopicList($dbFormData['user_id'], $currentMinNum, $topicCategory, $sort);
} elseif ($topicMyType == 2) {
    $dbTopicData = getMyCommentList($dbFormData['user_id'], $currentMinNum, $topicCategory, $sort);
} elseif ($topicMyType == 3) {
    $dbTopicData = getMyFavoriteList($dbFormData['user_id'], $currentMinNum, $topicCategory, $sort);
}




debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = 'My投稿一覧';
require('head.php'); ?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
    <ariticle class="main-contents">
        <div class="main-menu">
            <h2 class="title">My投稿一覧</h2>

            <!-- 検索ここから -->
            <form action="" method="get" id="submit-form">
                <label>カテゴリー
                    <select name="c_id" class="sortmenu submit-select">
                        <option value="0">選択して下さい</option>
                        <?php
foreach ($dbCategoryData as $key => $val) {
    ?>
                        <option value="<?php echo $val['category_id'] ; ?>"
                            <?php if (getSubFormData('c_id', true) == $val['category_id']) {
        echo 'selected';
    } ?>><?php echo $val['category_name']; ?>
                        </option>;
                        <?php
}
 ?>
                    </select>
                </label>

                <label>ソート
                    <select name="mytype" class="sortmenu submit-select">

                        <option value="1" <?php if (getSubFormData('mytype', true) == 1) {
     echo 'selected';
 }?>>投稿した記事</option>
                        <option value="2" <?php if (getSubFormData('mytype', true) == 2) {
     echo 'selected';
 }?>>コメントした記事</option>
                        <option value="3" <?php if (getSubFormData('mytype', true) == 3) {
     echo 'selected';
 }?>>お気に入り記事</option>
                    </select>
                </label>

                <label>日付
                    <select name="sort" class="sortmenu submit-select">
                        <option value="0" <?php if (getSubFormData('sort', true) == 0) {
     echo 'selected';
 }?>>選択して下さい</option>
                        <option value="1" <?php if (getSubFormData('sort', true) == 1) {
     echo 'selected';
 }?>>古い順</option>
                        <option value="2" <?php if (getSubFormData('sort', true) == 2) {
     echo 'selected';
 }?>>新しい順</option>
                    </select>
                </label>


            </form>
            <!-- 検索ここまで -->

            <!-- ページネーションここから -->
            <?php pagenation($currentPageNum, $dbTopicCount['total_page']); ?>
            <!-- ページネーションここまで -->


        </div>
        <!-- 記事一覧ここから -->
        <div class="wrapper-topic">
            <ul class="topiclist">
                <?php
        foreach ($dbTopicData as $key => $val) {
            ?>
                <li class="topictitle"><a href="topicdetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&t_id='.$val['topic_id'] : '?t_id='.$val['topic_id']; ?>"><i
                            class="fas fa-caret-square-right"></i><?php echo $val['title']; ?></a></li>
                <?php
        }
        ?>
            </ul>
        </div>
        <!-- 記事一覧ここまで -->

        <!-- ページネーションここから -->
        <?php pagenation($currentPageNum, $dbTopicCount['total_page']); ?>
        <!-- ページネーションここまで -->



    </ariticle>

    <?php require('sidebar.php'); ?>


</div>

<?php require('footer.php');
