<body>
  <div id="wrapper">
    <header>
      <div class="site-width">
        <h1 class="logo"><a href="index.php">Teach Me!</a></h1>
        <nav id="top-nav">
          <ul>
            <?php
           if (empty($_SESSION['user_id'])) {
               ?>
            <li><a href="signup.php" class="nav-btn btn__yellow">新規登録</a></li>
            <li><a href="login.php" class="nav-btn btn__green">ログイン</a></li>
            <?php
           } else {
               ?>
            <li><a href="mypage.php" class="nav-btn btn__yellow">マイページ</a></li>
            <li><a href="logout.php" class="nav-btn btn__green">ログアウト</a></li>
            <?php
           }
           ?>
          </ul>
        </nav>
      </div>
    </header>