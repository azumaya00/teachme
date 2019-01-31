<?php
//共通関数読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('お気に入り用Ajax');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POSTされたとき
if (isset($_POST['topicId'])) {
    debug('POST送信があります');
    //変数定義
    $t_id = $_POST['topicId'];
    //DB接続
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM favorite WHERE topic_id = :t_id AND user_id = :u_id';
        $data = array(':t_id' => $t_id, ':u_id' => $_SESSION['user_id']);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt -> rowCount();
        debug('登録数: '.print_r($result, true));

        //既に登録がある場合は削除する
        if (!empty($result)) {
            debug('登録ありなので削除します');
            $sql = 'DELETE FROM favorite WHERE topic_id = :t_id AND user_id = :u_id';
            $data = array(':t_id' => $t_id, ':u_id' => $_SESSION['user_id']);
            $stmt = queryPost($dbh, $sql, $data);
        } else {
            //登録が無い場合は挿入する
            debug('登録無しなので挿入します');
            $sql = 'INSERT INTO favorite(topic_id, user_id, create_date) VALUES(:t_id, :u_id, :create_date)';
            $data = array(':t_id' => $t_id, ':u_id' => $_SESSION['user_id'], ':create_date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
    }
}

debug('Ajax終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
