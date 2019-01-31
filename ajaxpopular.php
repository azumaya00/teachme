<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('いいね用Ajax');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POSTされたとき
if (isset($_POST['commentId'])) {
    debug('POST送信があります');
    //変数定義
    $t_id = $_POST['topicId'];
    $c_id = $_POST['commentId'];
    debug('コメントID: '.print_r($c_id, true));
    //DB接続
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM popular WHERE topic_id = :t_id AND comment_id = :c_id AND user_id = :u_id';
        $data = array(':t_id' => $t_id,':c_id' => $c_id, ':u_id' => $_SESSION['user_id']);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt -> rowCount();
        debug('登録数: '.print_r($result, true));

        //既に登録がある場合は削除する
        if (!empty($result)) {
            $sql = 'DELETE FROM popular WHERE topic_id = :t_id AND comment_id = :c_id AND user_id = :u_id';
            $data = array(':t_id' => $t_id, ':c_id' => $c_id, ':u_id' => $_SESSION['user_id']);
            $stmt = queryPost($dbh, $sql, $data);
            //現在のいいね数を返す
            echo popularCount($t_id, $c_id);
        } else {
            //登録が無い場合は挿入する
            $sql = 'INSERT INTO popular(topic_id, comment_id, user_id, create_date) VALUES(:t_id, :c_id, :u_id, :create_date)';
            $data = array(':t_id' => $t_id, ':c_id' => $c_id, ':u_id' => $_SESSION['user_id'], ':create_date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
            //現在のいいね数を返す
            echo popularCount($t_id, $c_id);
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
    }
}

debug('Ajax終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
