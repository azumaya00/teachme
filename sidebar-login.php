<aside class="sidebar">
  <div class="profile">
    <div class="img-profile">
      <?php
     if (!empty($dbFormData['userimg'])) {
         echo '<img src="'.$dbFormData['userimg'].'" alt="プロフィール画像">';
     } else {
         echo '<i class="fas fa-user-circle guest-icon"></i>';
     }
     ?>

    </div>
    <p><span>ようこそ<?php echo $dbFormData['username'] ?>さん</span><br><a
        href="profedit.php">プロフィール編集へ</a></p>

    <div class="btn btn__yellow btn-side"><a href="newtopic.php">新しい記事を作る！</a></div>
  </div>
  <div class="sidemenu">
    <ul>
      <li><a href="mypage.php">マイページへ</a></li>
      <li><a href="topiclist.php">記事一覧</a></li>
      <li><a href="passedit.php">パスワード変更</a></li>
      <li><a href="withdraw.php">退会はこちらから</a></li>
    </ul>
  </div>


</aside>