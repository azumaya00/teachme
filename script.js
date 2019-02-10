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
    $('#textcount').on('keyup', function() {

      var counted = $(this).val().length;
      $('.count_text').text(counted);

      if (counted >= 2000) {
        $('.textcounter').css('color', '#ff9999');
      } else {
        $('.textcounter').css('color', '#666666');
      }

    });

    //詳細記事の画像拡大
    $('.open-imgmodal').on('click', function() {
      //画像URL取得
      var src = $(this).attr('src');
      //画像URLをモーダルモーダル部分に挿入
      $('#imgmodal-content').attr('src', src);
      $('#imgmodal-overlay').fadeIn();
      $('#imgmodal-content').fadeIn();
    });

    //モーダル解除
    $('#imgmodal-overlay').on('click', function() {
      $('#imgmodal-overlay').fadeOut();
      $('#imgmodal-content').fadeOut();
    });

    //カテゴリ・ソートを選択するとGET送信
    $('.submit-select').on('change', function() {
      $('#submit-form').submit();
    });

    //お気に入り登録・削除
    var $favorite;
    var favoriteTopicId;
    //変数定義
    $favorite = $('.js-click-favorite') || null;
    favoriteTopicId = $favorite.data('t_id') || null;

    if (favoriteTopicId !== null && favoriteTopicId !== undefined) {
      //お気に入りクリックで動作
      $favorite.on('click', function() {
        //変数定義
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajaxfavorite.php",
          data: {
            topicId: favoriteTopicId
          }
        }).done(function(data) {
          console.log('Ajax Success');
          //アクティブの付け外し
          $this.toggleClass('favorite-active')
        }).fail(function(msg) {
          console.log('Ajax.error');
        });

      });
    }


    //いいね登録・削除
    var $popular;
    var popularElement
    var popularTopicId;
    var popularCommentId;
    //変数定義
    $popular = $('.js-click-popular');

    //いいねクリックで動作
    $popular.on('click', function() {
      //変数定義
      var $this = $(this);
      popularElement = $this.data('popular');
      popularTopicId = popularElement.t_id;
      popularCommentId = popularElement.c_id;

      $.ajax({
        type: "POST",
        url: "ajaxpopular.php",
        data: {
          topicId: popularTopicId,
          commentId: popularCommentId
        }
      }).done(function(data) {
        console.log('Ajax Success');
        console.log(data);
        //アクティブの付け外し
        $this.toggleClass('popular-active')
        //いいね数の変動
        $this.siblings('span').html(data);
      }).fail(function(msg) {
        console.log('Ajax.error');
      });

    });







  });