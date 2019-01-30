<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('記事詳細ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);

//記事ID取得
$t_id = (!empty($_GET['t_id'])) ? $_GET['t_id'] : '' ;
//記事詳細取得
$dbTopicData = getTopicDetail($t_id);
debug('記事内容: '.print_r($dbTopicData, true));
//コメント取得
$dbCommentData = getCommentList($t_id);
debug('コメント内容: '.print_r($dbCommentData, true));


//POSTされていたとき
if (!empty($_POST)) {
    debug('コメント送信があります');
    //変数定義
    $comment = nl2br(sanitize($_POST['comment']));
    if (!empty($_FILES['img01']['name'])) {
        $img01 = uploadImg($_FILES['img01'], 'img01');
    } else {
        $img01 = '';
    }


    $img02 = (!empty($_FILES['img02']['name'])) ? uploadImg($_FILES['img02'], 'img02') : '';

    //バリデーション
    //今回はコメントのみ
    validRequired($comment, 'comment');
    validMinLen($comment, 'comment');
    validMaxLen($comment, 'comment', 2000);

    if (empty($err_msg)) {
        debug('バリデーションOKです');
        //DB接続
        try {
            $dbh = dbConnect();
            $sql = 'INSERT INTO comment(topic_id, user_id, comment,img01,img02,create_date) VALUES(:topic_id, :user_id, :comment, :img01, :img02, :create_date )';
            $data = array(':topic_id' => $t_id, ':user_id' => $_SESSION['user_id'], ':comment' => $comment, ':img01' => $img01, ':img02' =>$img02, ':create_date' => date('Y-m-d H:i:s'));
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            if ($stmt) {
                debug('クエリ成功です');
                $_SESSION['msg_success'] = SUC06;
                debug('セッション変数の中身: '.print_r($_SESSION, true));
                //セッションが消えないように一度閉じる
                session_write_close();
                //この記事詳細へ遷移
                header("Location:topicdetail.php?t_id=".$t_id);
            }
        } catch (Exception $e) {
            error_log('エラー発生:' .$e->getMessage());
            $err_msg['common'] = MSG01;
        }
    }
}


debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = $dbTopicData['title'];
require('head.php'); ?>

<?php require('header.php'); ?>

<!-- 成功メッセージ表示タグここから -->
<p id="show-msg" class="msg-modal" style="display:none">
  <?php echo getSessionFlash('msg_success'); ?>
</p>
<!-- 成功メッセージ表示タグここまで -->



<div id="contents" class="site-width">
  <article class="main-contents">
    <h2 class="title"><?php echo sanitize($dbTopicData['title']); ?>
    </h2>

    <!-- ここから主記事 -->
    <div class="wrapper-topic wrapper-btm">

      <div class="poster main-poster">
        <div class="poster-icon">
          <?php
     if (!empty($dbTopicData['userimg'])) {
         echo '<img src="'.$dbTopicData['userimg'].'" alt="アイコン">';
     } else {
         echo '<i class="fas fa-user-circle guest-icon__small"></i>';
     }
     ?>
        </div>
        <div class="poster-profile">
          <span><?php echo sanitize($dbTopicData['username']); ?>さん</span><br><?php echo getAge(sanitize($dbTopicData['birthday'])); ?>歳
        </div>

      </div>
      <div class="topic-contents">
        <span class="date"><?php echo formatDate(sanitize($dbTopicData['create_date'])); ?>
          <i class="fas fa-heart faborite"></i></span>
        <div class="main-topic">
          <p><?php echo $dbTopicData['contents']; ?>
          </p>
          <div class="imgarea">
            <img src="<?php echo sanitize($dbTopicData['img01']) ?>"
              class="open-imgmodal" alt="画像1" <?php if (empty(sanitize($dbTopicData['img01']))) {
         echo 'style="display:none"';
     } ?>>
            <img src="<?php echo sanitize($dbTopicData['img02']) ?>"
              class="open-imgmodal" alt="画像2" <?php if (empty(sanitize($dbTopicData['img02']))) {
         echo 'style="display:none"';
     } ?>>
          </div>

        </div>
      </div>

    </div>
    <!-- ここまで主記事 -->

    <!-- ここからコメント -->
    <section class="commentarea">
      <?php foreach ($dbCommentData as $key => $val) {
         ?>

      <div class="wrapper-comment">
        <div class="poster commenter">
          <div class="poster-icon">
            <?php
     if (!empty($val['userimg'])) {
         echo '<img src="'.$val['userimg'].'" alt="アイコン">';
     } else {
         echo '<i class="fas fa-user-circle guest-icon__small"></i>';
     } ?>
          </div>
          <div class="poster-profile">
            <span><?php echo sanitize($val['username']); ?>さん</span><br><?php echo getAge(sanitize($val['birthday'])); ?>歳
          </div>
          <div class="btn__yellow btn-small">いいね！</div>
          <br><span>5人</span>
        </div>
        <div class="wrapper-blank">
          <span class="date"><?php echo formatDate(sanitize($val['create_date'])); ?></span>
          <div class="comment">
            <p><?php echo $val['comment']; ?>
            </p>
            <div class="imgarea">
              <img src="<?php echo sanitize($val['img01']) ?>"
                class="open-imgmodal" alt="画像1" <?php if (empty(sanitize($val['img01']))) {
         echo 'style="display:none"';
     } ?>>
              <img src="<?php echo sanitize($val['img02']) ?>"
                class="open-imgmodal" alt="画像2" <?php if (empty(sanitize($val['img02']))) {
         echo 'style="display:none"';
     } ?>>
            </div>
          </div>
        </div>

      </div>
      <?php
     } ?>



    </section>
    <!-- ここまでコメント -->


    <h2 class="title">コメントを投稿する</h2>
    <div class="form-comment">

      <div class="msgarea">
        <?php if (!empty($err_msg['common'])) {
         echo $err_msg['common'];
     } ?>
      </div>

      <form action="" method="post" enctype="multipart/form-data">
        <label class="<?php if (!empty($err_msg['comment'])) {
         echo 'err';
     } ?>">コメント<span
            class="required">必須</span>
          <span><?php echo getErrMsg('comment'); ?></span>
          <textarea name="comment" id="" cols="30" rows="10"><?php if (!empty($_POST['comment'])) {
         echo $_POST['comment'];
     } ?></textarea>
        </label>
        <label class="<?php if (!empty($err_msg['img01'])) {
         echo 'err';
     } ?>">画像(.jpg
          .png .gif のみ、2MBまで)
          <span><?php echo getErrMsg('img01'); echo getErrMsg('img02') ; ?></span>
          <div class="wrapper-imgDrop">
            <div class="area-drop area-imgDrop__rectangle">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="img01" class="input-file input-file__rectangle">
              <img src="<?php echo getCommentFormData('img01'); ?>"
                class="prev-img prev-img__rectangle" <?php if (empty(getCommentFormData('img01'))) {
         echo 'style="display:none"';
     } ?>>
              <p class="howto-text"><span>1</span><br>ここに画像を<br>ドラッグ＆ドロップ</p>
            </div>
            <div class="area-drop area-imgDrop__rectangle">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="img02" class="input-file input-file__rectangle">
              <img src="<?php echo getCommentFormData('img02'); ?>"
                class="prev-img prev-img__rectangle" <?php if (empty(getCommentFormData('img02'))) {
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






  </article>




  <?php require('sidebar.php'); ?>
</div>

<!-- ここからモーダル -->
<img src="" id="imgmodal-content">
<div id="imgmodal-overlay">
  <i class="fas fa-times window-close"></i>
</div>
<!-- ここまでモーダル -->

<?php require('footer.php');
