<?php
$siteTitle = 'お問い合わせ';
require('head.php');
?>

<?php require('header.php'); ?>
<div id="contents" class="site-width">
  <section id="main">
    <h2 class="title">お問い合わせ</h2>
    <div class="form-container">
      <form action="" method="post">
        <label>お名前<span></span>
          <input type="text" name="clientname">
        </label>
        <label>Eメールアドレス<span></span>
          <input type="text" name="email">
        </label>
        <label>件名<span></span>
          <input type="text" name="subject">
        </label>
        <label>お問い合わせ内容<span></span>
          <textarea name="contents" id="contact" cols="30" rows="10"></textarea>
        </label>
        <label>
            <div class="btn-container">
          <input type="submit" class="btn btn-mid btn__yellow" value="送信する">
        </div>
        </label>
      </form>
    </div>
  </section>
</div>


<?php require('footer.php'); ?>
