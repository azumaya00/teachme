<?php

//ログイン認証
if (!empty($_SESSION['login_date'])) {
    debug('ログインしています');

    if (($_SESSION['login_date'] + $_SESSION['login_limit'] < time())) {
        debug('ログイン有効期限切れです');
        //セッション削除してログイン画面へ
        session_destroy();
        header("location:login.php");
    } else {
        debug('ログイン有効期限内です');
        //最終ログインを現在時間に
        $_SESSION['login_date'] = time();

        //ログイン有効期解なのにログインページへ飛ぼうとしている
        //この場合マイページへ遷移させる
        if (basename($_SERVER['PHP_SELF']) === 'login.php') {
            header("Location:mypage.php");
        }
    }
} else {
    debug('未ログインユーザーです');
    //ログインページ以外へ飛ぼうとしているとき
    //ログインページへ遷移させる
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        debug('ログインページ以外への遷移です');
        header("Location:login.php");
    }
}
