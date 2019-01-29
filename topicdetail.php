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
            <a href="<?php echo sanitize($dbTopicData['img01']) ?>"
              class="modal"><img src="<?php echo sanitize($dbTopicData['img01']) ?>"
                alt="画像1" <?php if (empty(sanitize($dbTopicData['img01']))) {
         echo 'style="display:none"';
     } ?>></a>
            <a href="<?php echo sanitize($dbTopicData['img02']) ?>"
              class="modal"><img src="<?php echo sanitize($dbTopicData['img02']) ?>"
                alt="画像1" <?php if (empty(sanitize($dbTopicData['img02']))) {
         echo 'style="display:none"';
     } ?>></a>
          </div>

        </div>
      </div>

    </div>

    <section class="commentarea">

      <div class="wrapper-comment">
        <div class="poster commenter">
          <div class="poster-icon"><img src="./img/self.png" alt="アイコン"></div>
          <div class="poster-profile">
            <span>えのきさん</span><br>45歳
          </div>
          <div class="btn__yellow btn-small">いいね！</div>
          <br><span>5人</span>
        </div>
        <div class="wrapper-blank">
          <span class="date">2018年1月8日 8:17</span>
          <div class="comment">
            <p>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</p>
            <div class="imgarea">
              <img src="https://placehold.jp/200x150.png" alt="">
              <img src="https://placehold.jp/200x150.png" alt="">
            </div>
          </div>
        </div>

      </div>

      <div class="wrapper-comment">
        <div class="poster commenter">
          <div class="poster-icon"><img src="./img/selamathariraya.png" alt="アイコン"></div>
          <div class="poster-profile">
            <span>しめじさん</span><br>11歳
          </div>
          <div class="btn__yellow btn-small">いいね！</div>
          <br><span>5人</span>
        </div>
        <div class="wrapper-blank">
          <span class="date">2018年1月8日 9:39</span>
          <div class="comment">

            <p>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</p>
          </div>
        </div>

      </div>

    </section>



    <h2 class="title">コメントを投稿する</h2>
    <div class="form-comment">

      <div class="msgarea">
        <?php if (!empty($err_msg['common'])) {
         echo $err_msg['common'];
     } ?>
      </div>

      <form action="" method="post" enctype="multipart/form-data">
        <label>コメント<span class="required">必須</span>
          <span></span>
          <textarea name="comment" id="" cols="30" rows="10"></textarea>
        </label>
        <label>画像(.jpg .png .gif のみ、2MBまで)
          <span></span>
          <div class="wrapper-imgDrop">
            <div class="area-drop area-imgDrop__rectangle">
              <p><span>1</span><br>ここに画像を<br>ドラッグ＆ドロップ</p>
            </div>
            <div class="area-drop area-imgDrop__rectangle">
              <p><span>2</span><br>ここに画像を<br>ドラッグ＆ドロップ</p>
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
<img src="" id="modal-content">
<div id="modal-overlay"></div>
<!-- ここまでモーダル -->

<?php require('footer.php');
