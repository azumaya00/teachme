<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('新規投稿ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//カテゴリーを取得
$dbCategoryData = getCategory();
debug('カテゴリの中身: '.print_r($dbCategoryData, true));

//POST送信されていたとき
if (!empty($_POST)) {
    //変数定義
    $title = $_POST['topic_title'];
    $category = $_POST['category_id'];
    $topic_contents = $_POST['topic_contents'];
    $img01 = (!empty($_FILES['img01']['name'])) ? uploadImg($_FILES['img01'], 'img01') : '';
    $img02 = (!empty($_FILES['img02']['name'])) ? uploadImg($_FILES['img02'], 'img02') : '';

    //バリデーション
    //未入力
    validRequired($title, 'topic_title');
    validRequired($topic_contents, 'topic_contents');
    if (empty($err_msg)) {
        debug('未入力チェックOKです');
        //タイトル
        validMinLen($title, 'topic_title');
        validMaxLen($title, 'topic_title', 40);
        //カテゴリー
        validSelect($category, 'category_id');
        //本文
        validMinLen($topic_contents, 'topic_contents');
        validMaxLen($topic_contents, 'topic_contents', 2000);
        if (empty($err_msg)) {
            debug('バリデーションOKです');
            //DB接続
            try {
                $dbh = dbConnect();
                $sql = 'INSERT INTO topic (title, category_id, user_id, contents, img01, img02, create_date) VALUES (:title, :category_id, :user_id, :contents, :img01, :img02, :create_date )';
                $data = array(':title' => $title, ':category_id' => $category, ':user_id' => $_SESSION['user_id'], ':contents' => $topic_contents, ':img01' => $img01, ':img02' => $img02, ':create_date' => date('Y-m-d H:i:s'));

                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);
                //クエリ成功の場合
                if ($stmt) {
                    $_SESSION['msg_success'] = SUC05;
                    //topicidを持ってくる
                    $_GET['t_id'] = $dbh->lastInsertId();
                    $t_id = $_GET['t_id'];
                    debug('掲示板のID: '.print_r($t_id, true));
                    //マイページへ遷移
                    //記事詳細が出来たらそちらへ遷移
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
$siteTitle = '新規投稿';
require('head.php'); ?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
  <section id="main">
    <h2 class="title">新規投稿</h2>
    <div class="form-container">

      <div class="msgarea">
        <?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?>
      </div>

      <form action="" method="post" enctype="multipart/form-data">
        <label class="<?php if (!empty($err_msg['topic_title'])) {
    echo 'err';
} ?>">タイトル<span
            class="required">必須</span>　<span><?php echo getErrMsg('topic_title'); ?></span>
          <input type="text" name="topic_title" value="<?php echo getFormData('topic_title') ?>">
        </label>
        <label class="<?php if (!empty($err_msg['category_id'])) {
    echo 'err';
} ?>">カテゴリ<span
            class="required">必須</span>　<span><?php echo getErrMsg('category_id'); ?></span>
          <select name="category_id" class="selectbox">
            <option value="0" <?php if (getFormData('category_id') == 0) {
    echo 'selected' ;
} ?>>選択して下さい</option>
            <?php foreach ($dbCategoryData as $key => $val) {
    ?>
            <option value="<?php echo $val['category_id'] ?>"
              <?php if (getFormData('category_id') == $val['category_id']) {
        echo 'selected';
    } ?>><?php echo $val['category_name'] ?>
            </option>

            <?php
} ?>

          </select>
        </label>
        <label class="<?php if (!empty($err_msg['topic_contents'])) {
        echo 'err';
    } ?>">内容<span
            class="required">必須</span>　<span><?php echo getErrMsg('topic_contents'); ?></span>
          <textarea name="topic_contents" id="textcount" cols="30" rows="10"> <?php echo getFormData('topic_contents') ?></textarea>
        </label>
        <p class="textcounter"><span class="count_text">0</span>/2000</p>
        <label>画像(.jpg .png .gif のみ、2MBまで)
          <span></span>
          <div class="wrapper-imgDrop">
            <div class="area-drop area-imgDrop__rectangle">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="img01" class="input-file input-file__rectangle">
              <img src="<?php echo getFormData('img01'); ?>"
                class="prev-img prev-img__rectangle" <?php if (empty(getFormData('img01'))) {
        echo 'style="display:none"';
    } ?>>
              <p class="howto-text"><span>1</span><br>ここに画像を<br>ドラッグ＆ドロップ</p>
            </div>
            <div class="area-drop area-imgDrop__rectangle">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="img02" class="input-file input-file__rectangle">
              <img src="<?php echo getFormData('img02'); ?>"
                class="prev-img prev-img__rectangle" <?php if (empty(getFormData('img02'))) {
        echo 'style="display:none"';
    } ?>>
              <p class="howto-text"><span>2</span><br>ここに画像を<br>ドラッグ＆ドロップ</p>
            </div>
          </div>
        </label>
        <div class="btn-container">
          <input type="submit" class="btn btn__yellow btn-mid" value="投稿する">
        </div>
      </form>
    </div>
  </section>
</div>


<?php require('footer.php');
