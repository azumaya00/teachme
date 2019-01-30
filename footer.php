<footer>
  <div class="site-width">
    <h1 class="logo"><a href="index.html">Teach Me!</a></h1>
    <nav class="footer-nav">
      <ul>
        <li><a href="#">このサイトについて</a></li>
        <li><a href="#">個人情報保護方針</a></li>
        <li><a href="contact.php">お問い合わせ</a></li>
      </ul>
      <p>©Copyright2019 <a href="index.php">Teach Me!</a>.All Rights Reserved</p>
    </nav>
  </div>
</footer>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>
  $(function() {
    //画像ライブプレビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');

    //ドラッグしたときに枠線を太くする
    $dropArea.on('dragover', function(e) {
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '4px #ccc dotted');
    });
    //ドラッグが離れたときに枠線を元の太さにする
    $dropArea.on('dragleave', function(e) {
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '2px #ccc dotted');
    });

    //画像が選択されたら
    $fileInput.on('change', function(e) {
      var file = this.files[0];
      $img = $(this).siblings('.prev-img');
      $howto = $(this).siblings('.howto-text');
      $howto.css('display', 'none');

      fileReader = new FileReader();

      fileReader.onload = function(event) {
        $img.attr('src', event.target.result).show();
      };

      fileReader.readAsDataURL(file);
    });


    //成功メッセージ表示
    var $showMsg = $('#show-msg');
    var msg = $showMsg.text();

    if (msg.replace("/^[\s ]+|[\s ]+$\g", "").length) {
      $showMsg.slideToggle('slow');
      setTimeout(function() {
        $showMsg.slideToggle('slow')
      }, 1500);
    }

    //テキストカウンタ
    $('#textcount').keyup(function() {

      var counted = $(this).val().length;
      $('.count_text').text(counted);

      if (counted >= 2000) {
        $('.textcounter').css('color', '#ff9999');
      } else {
        $('.textcounter').css('color', '#666666');
      }

    });

    //詳細記事の画像拡大
    $('.open-imgmodal').click(function() {
      //画像URL取得
      var src = $(this).attr('src');
      //画像URLをモーダルモーダル部分に挿入
      $('#imgmodal-content').attr('src', src);
      $('#imgmodal-overlay').fadeIn();
      $('#imgmodal-content').fadeIn();
    });

    //モーダル解除
    $('#imgmodal-overlay').click(function() {
      $('#imgmodal-overlay').fadeOut();
      $('#imgmodal-content').fadeOut();
    });

    //カテゴリ・ソートを選択するとGET送信
    $('.submit-select').change(function() {
      $('#submit-form').submit();
    });









  });
</script>


</body>

</html>