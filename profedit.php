<?php

//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);
//POSTされているとき
if (!empty($_POST)) {
    //変数定義
    $username = $_POST['username'];
    $email = $_POST['email'];
    $realname = $_POST['realname'];
    $bYear = $_POST['birthYear'];
    $bMonth = $_POST['birthMonth'];
    $bDay = $_POST['birthDay'];
    $birthday = $bYear.'-'.$bMonth.'-'.$bDay;
    $userimg = (!empty($_FILES['userimg']['name'])) ? uploadImg($_FILES['userimg'], 'userimg') : '';
    $userimg = (empty($userimg) && !empty($dbFormData['userimg'])) ? $dbFormData['userimg'] : $userimg;


    //DBとPOSTの情報が異なればバリデーション
    //ユーザー名
    if ($dbFormData['username'] !== $username) {
        validRequired($username, 'username');
        validMaxLen($username, 'username', 255);
    }
    //メールアドレス
    if ($dbFormData['email'] !== $email) {
        validRequired($email, 'email');
        validEmail($email, 'email');
        validMaxLen($email, 'email', 255);
        validEmailDup($email);
    }
    //氏名
    if ($dbFormData['realname'] !== $realname) {
        validMaxLen($realname, 'realname', 255);
    }
    //誕生日
    if ($dbFormData['birthday'] !== $birthday) {
        validDate($birthday, 'birthday');
    }
    //ここに画像関係あればいれる

    //エラーが無いとき
    if (empty($err_msg)) {
        debug('バリデーションOKです');
        //DB接続
        try {
            $dbh = dbConnect();
            $sql = 'UPDATE users SET username = :username, email = :email, realname = :realname, birthday = :birthday, userimg = :userimg WHERE user_id = :userid ';
            $data = array(':username' => $username, 'email' => $email, ':realname' => $realname, ':birthday' => $birthday, ':userimg' => $userimg, ':userid' => $dbFormData['user_id']);
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            //クエリ成功時
            if ($stmt) {
                debug('更新成功');
                $_SESSION['msg_success'] = SUC01;
                debug('マイページへ遷移します');
                header("Location:mypage.php");
            }
        } catch (Exception $e) {
            error_log('エラー発生: '.$e->getMessage());
            $err_msg['common'] = MSG01;
        }
    }
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

<?php
$siteTitle = 'プロフィール編集';
require('head.php'); ?>

<?php require('header.php'); ?>


<div id="contents" class="site-width">
    <section id="main">
        <h2 class="title">プロフィール編集</h2>
        <div class="form-container">
            <div class="msgarea">
                <?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="">プロフィール画像(.jpg .png .gif のみ、2MBまで)<span><?php echo getErrMsg('userimg'); ?></span>
                    <div class="area-drop area-imgDrop__round">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="userimg" class="input-file input-file__round">
                        <img src="<?php echo getFormData('userimg'); ?>"
                            class="prev-img prev-img__round" <?php if (empty(getFormData('userimg'))) {
    echo 'style="display:none"';
} ?>>

                        <p <?php if (!empty(getFormData('userimg'))) {
    echo 'style="display:none"';
} ?>
                            class="howto-text">ここに画像を<br>ドラッグ＆ドロップ</p>
                    </div>
                </label>
                <label class="<?php if (!empty($err_msg['username'])) {
    echo 'err';
} ?>">ユーザー名<span
                        class="required">必須</span>　<span><?php echo getErrMsg('username'); ?></span>
                    <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                </label>
                <label class="<?php if (!empty($err_msg['email'])) {
    echo 'err';
} ?>">Eメールアドレス<span
                        class="required">必須</span>　<span><?php echo getErrMsg('email'); ?></span>
                    <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                </label>
                <label class="<?php if (!empty($err_msg['realname'])) {
    echo 'err';
} ?>">氏名(非表示)　<span><?php echo getErrMsg('realname'); ?></span>
                    <input type="text" name="realname" value="<?php echo getFormData('realname'); ?>">
                </label>
                <label class="<?php if (!empty($err_msg['birthday'])) {
    echo 'err';
} ?>">生年月日(年/月/日)　<span><?php echo getErrMsg('birthday'); ?></span><br>
                    <select name="birthYear" class="selectbox b-day">
                        <?php for ($i = 1920; $i <= 2030; $i++) {
    $num = sprintf('%04d', $i);
    if ($num === getDateProperty('y')) {
        $attr = ' selected';
    } else {
        $attr = '';
    }
    echo "<option value=".$num.$attr.">".$num."</option>";
} ?>
                    </select>
                    <select name="birthMonth" class="selectbox b-day">
                        <?php for ($i = 1; $i <= 12; $i++) {
    $num = sprintf('%02d', $i);
    if ($num === getDateProperty('m')) {
        $attr = ' selected';
    } else {
        $attr = '';
    }
    echo "<option value=".$num.$attr.">".$num."</option>";
} ?>
                    </select>
                    <select name="birthDay" class="selectbox b-day">
                        <?php for ($i = 1; $i <= 31; $i++) {
    $num = sprintf('%02d', $i);
    if ($num === getDateProperty('d')) {
        $attr = ' selected';
    } else {
        $attr = '';
    }
    echo "<option value=".$num.$attr.">".$num."</option>";
} ?>
                    </select>
                </label>
                <div class="btn-container">
                    <input type="submit" class="btn btn__yellow btn-mid" value="送信する">
                </div>


            </form>



        </div>

    </section>
</div>

<?php require('footer.php');
