<?php
if (empty($_SESSION['user_id'])) {
    //未ログイン用サイドバー表示
    require('sidebar-index.php');
} else {
    //ログイン中のサイドバー表示
    require('sidebar-login.php');
}
