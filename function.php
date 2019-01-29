<?php

//=====================================
//ログ
//=====================================
ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

//=====================================
//デバッグ
//=====================================
//デバッグフラグ、実装時にはfalseにする
$debug_flg = true;
//デバッグ関数
function debug($str)
{
    global $debug_flg;
    if (!empty($debug_flg)) {
        error_log('デバッグ: '.$str);
    }
}

//=====================================
//セッション準備
//=====================================
//セッションファイルの置き場所
session_save_path("/var/tmp");
//ガーベージコレクションが削除するセッションの有効期限
ini_set('session.gc_maxlifetime', 60*60*24*30);
//クッキーの有効期限を30日に
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//セッション再生成
session_regenerate_id();

//=====================================
//デバッグログ開始
//=====================================
function debugLogStart()
{
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID: '.session_id());
    debug('セッション変数の中身: '.print_r($_SESSION, true));
    debug('現在日時タイムスタンプ: '.time());
    if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
        debug('ログイン期限日時タイムスタンプ: '.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//=====================================
//定数
//=====================================
//エラーメッセージ用定数
define('MSG01', 'エラーが発生しました。しばらく時間をおいてからやり直して下さい。');
define('MSG02', '入力必須です');
define('MSG03', 'Eメールの形式で入力して下さい');
define('MSG04', '6文字以上で入力して下さい');
define('MSG05', '文字以内で入力して下さい');
define('MSG06', '半角英数字で入力して下さい');
define('MSG07', 'パスワード(再入力)が合っていません');
define('MSG08', '登録済みのEメールです');
define('MSG09', 'Eメールもしくはパスワードが異なっています');
define('MSG10', '誕生日が不正確です');
define('MSG11', '現在のパスワードが合っていません');
define('MSG12', '現在のパスワードと同じものは利用出来ません');
define('MSG13', '文字で入力して下さい');
define('MSG14', '正しくありません');
define('MSG15', '有効期限が切れています');
define('SUC01', 'プロフィールを変更しました');
define('SUC02', 'パスワードを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', 'パスワードを変更しました');
define('SUC05', '記事を投稿しました');

//=====================================
//グローバル変数
//=====================================
//エラーメッセージ用配列
$err_msg = array();

//=====================================
//バリデーション
//=====================================
//未入力チェック
function validRequired($str, $key)
{
    if ($str === '') {
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

//Email形式
function validEmail($str, $key)
{
    if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/
", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

//Email重複
function validEmailDup($email)
{
    global $err_msg;
    try {
        $dbh = dbConnect();
        //Emailがマッチかつ削除フラグたってないものの個数を持ってくる
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //個数が0で無い場合エラーメッセを出す
        if (!empty($result['count(*)'])) {
            $err_msg['email'] = MSG08;
        }
    } catch (Exception $e) {
        error_log('エラー発生: '.$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

//最小文字数
function validMinLen($str, $key, $min = 6)
{
    if (mb_strlen($str) < $min) {
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

//最大文字数
function validMaxLen($str, $key, $max)
{
    if (mb_strlen($str) > $max) {
        global $err_msg;
        $err_msg[$key] = $max.MSG05;
    }
}

//半角英数字
function validHalf($str, $key)
{
    if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

//同値チェック
function validMatch($str1, $str2, $key)
{
    if ($str1 !== $str2) {
        global $err_msg;
        $err_msg[$key] = MSG07;
    }
}

//パスワードチェックまとめ
function validPass($str, $key)
{
    validHalf($str, $key);
    validMinLen($str, $key);
    validMaxLen($str, $key, 255);
}

//日付が正しいかチェック
function validDate($str, $key)
{
    list($Y, $m, $d) = explode('-', $str);
    if (checkdate($m, $d, $Y) !== true) {
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
}

//桁数チェック(8桁)
function validLength($str, $key, $length = 8)
{
    if (mb_strlen($str) !== $length) {
        global $err_msg;
        $err_msg[$key] = $length.MSG13;
    }
}

//selectboxのチェック
//数字で無ければエラーを返す
function validSelect($str, $key)
{
    if (!preg_match("/^[0-9]+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG14;
    }
}

//エラーメッセージ表示
function getErrMsg($key)
{
    global $err_msg;
    if (!empty($err_msg[$key])) {
        return $err_msg[$key];
    }
}

//=====================================
//データベース
//=====================================
//DB接続
function dbConnect()
{
    $dsn = 'mysql:dbname=teachme;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );

    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

//SQL実行
function queryPost($dbh, $sql, $data)
{
    $stmt = $dbh->prepare($sql);

    if (!$stmt->execute($data)) {
        debug('クエリに失敗しました');
        debug('失敗したSQL: '.print_r($stmt, true));
        $err_msg['common'] = MSG01;
        return 0;
    }
    debug('クエリ成功');
    return $stmt;
}

//ユーザー情報取得
function getUser($us_id)
{
    debug('ユーザー情報を取得します');
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE user_id = :us_id AND delete_flg = 0';
        $data = array(':us_id' => $us_id);

        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

//カテゴリー情報取得
function getCategory()
{
    debug('カテゴリー情報を取得します');
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            return $stmt->fetchAll();
        } else {
            return false;
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

//総記事数と総ページ数を取得
function getTopicCount($span = 20)
{
    debug('総記事数と総ページ数を取得します');
    try {
        $dbh = dbConnect();
        //件数を持ってくる
        $sql = 'SELECT topic_id FROM topic';
        //検索とソート関係のはここ
        $data = array();
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //総件数を変数に入れる
        $result['total'] = $stmt->rowCount();
        //総ページ数を変数に入れる
        $result['total_page'] = ceil($result['total']/$span);
        return $result;
        if (!$stmt) {
            return false;
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

//記事一覧取得
function getTopicList($currentMinNum=1, $topicCategory, $sort, $span = 20)
{
    debug('記事一覧を取得します');
    //DB接続
    try {
        $dbh = dbConnect();

        //idとタイトルを持ってくる
        $sql = 'SELECT topic_id, title FROM topic WHERE delete_flg = 0';
        //検索とソート関係のはここ
        if (!empty($sort)) {
            switch ($sort) {
                //記事古い順
                case 1:
                $sql .= ' ORDER BY topic_id ASC';
                break;
                //記事新しい順
                case 2:
                $sql .= ' ORDER BY topic_id DESC';
                break;
            }
        }
        //その後ろにSQL文を繋げる
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL文: '.$sql);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
            //全レコードを格納する
            $result = $stmt -> fetchAll();
            return $result;
        } else {
            return false;
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

//記事詳細を持ってくる
//後でコメントも追加すること
function getTopicDetail($t_id)
{
    debug('記事詳細を取得します');
    debug('まだコメント部分は作って無いよ');
    //DB接続
    try {
        $dbh = dbConnect();
        $sql = 'SELECT t.title, t.contents, t.img01, t.img02, t.create_date, u.username, u.birthday, u.userimg FROM topic AS t LEFT JOIN users AS u ON t.user_id = u.user_id WHERE t.topic_id = :t_id AND t.delete_flg = 0' ;
        $data = array(':t_id' => $t_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    } catch (Exception $e) {
        debug('エラー発生: '.$e->getMessage());
        $err_msg['common'] = MSG01;
    }
}

//=====================================
//メール送信
//=====================================
function sendMail($from, $to, $subject, $comment)
{
    if (!empty($to) && !empty($subject) && !empty($comment)) {
        //文字化け対策
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        //メール送信
        $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
        //送信結果判定
        if ($result) {
            debug('メールを送信しました');
        } else {
            debug('メールの送信に失敗しました');
        }
    }
}

//=====================================
//その他
//=====================================
//サニタイズ
function sanitize($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

//フォーム入力保持
function getFormData($str, $flg = false)
{
    //フラグが立っていたらGET送信
    //デフォルトはフラグ無し＝POST送信
    if ($flg) {
        $method = $_GET;
    } else {
        $method = $_POST;
    }
    //グローバル変数
    //内容は各ページで定義
    global $dbFormData;
    //DBにユーザー情報がある場合
    if (!empty($dbFormData)) {
        //入力フォームにエラーがある場合
        if (!empty($err_msg[$str])) {
            //POSTないしGETにデータがあればサニタイズ
            if (isset($method[$str])) {
                return sanitize($method[$str]);
            } else {
                //データが無い場合はDBの情報を表示
                return $dbFormData[$str];
            }
        } else {
            //POSTに情報がアリかつDBとPOSTの情報が違うときは
            //POSTの情報をサニタイズして返す
            if (isset($method[$str]) && $method[$str] !== $dbFormData[$str]) {
                return sanitize($method[$str]);
            } else {
                //POSTに情報が無い、もしくはDBと同じ情報が入っているときは
                //SBの情報をサニタイズして渡す
                return sanitize($dbFormData[$str]);
            }
        }
    } else {
        //DBにユーザー情報が無い場合
        //GETやPOSTに情報があればそれをサニタイズして表示
        if (isset($method[$str])) {
            return sanitize($method[$str]);
        }
    }
}

//日付の要素を持って来る
function getDateProperty($key)
{
    global $dbFormData;
    global $birthday;
    //変数定義
    $dbBirthday = $dbFormData['birthday'];

    //DBにユーザー情報がある場合
    if (!empty($dbBirthday)) {
        //入力フォームにエラーがある場合
        if (!empty($err_msg['birthday'])) {
            //POSTにデータがあればサニタイズ
            if (!empty($birthday)) {
                $Y = date('Y', strtotime($birthday));
                $m = date('m', strtotime($birthday));
                $d = date('d', strtotime($birthday));
                if ($key === 'y') {
                    return $Y;
                } elseif ($key === 'm') {
                    return $m;
                } elseif ($key === 'd') {
                    return $d;
                }
            } else {
                //データが無い場合はDBの情報を表示
                $Y = date('Y', strtotime($dbBirthday));
                $m = date('m', strtotime($dbBirthday));
                $d = date('d', strtotime($dbBirthday));
                if ($key === 'y') {
                    return $Y;
                } elseif ($key === 'm') {
                    return $m;
                } elseif ($key === 'd') {
                    return $d;
                }
            }
        } else {
            //POSTに情報が無い時はDBの情報を返す
            if (empty($birthday)) {
                $Y = date('Y', strtotime($dbBirthday));
                $m = date('m', strtotime($dbBirthday));
                $d = date('d', strtotime($dbBirthday));
                if ($key === 'y') {
                    return $Y;
                } elseif ($key === 'm') {
                    return $m;
                } elseif ($key === 'd') {
                    return $d;
                }
            } elseif ($birthday !== '1970-01-01' && $birthday !== $dbBirthday) {
                //POSTが初期値でなくDBとPOSTの情報が違うときは
                //POSTの情報を返す
                $Y = date('Y', strtotime($birthday));
                $m = date('m', strtotime($birthday));
                $d = date('d', strtotime($birthday));
                if ($key === 'y') {
                    return $Y;
                } elseif ($key === 'm') {
                    return $m;
                } elseif ($key === 'd') {
                    return $d;
                }
            } else {
                $Y = date('Y', strtotime($dbBirthday));
                $m = date('m', strtotime($dbBirthday));
                $d = date('d', strtotime($dbBirthday));
                if ($key === 'y') {
                    return $Y;
                } elseif ($key === 'm') {
                    return $m;
                } elseif ($key === 'd') {
                    return $d;
                }
            }
        }
    } else {
        //DBにユーザー情報が無い場合
        //POSTに情報があればそれを返す
        if (!empty($birthday)) {
            $Y = date('Y', strtotime($birthday));
            $m = date('m', strtotime($birthday));
            $d = date('d', strtotime($birthday));
            if ($key === 'y') {
                return $Y;
            } elseif ($key === 'm') {
                return $m;
            } elseif ($key === 'd') {
                return $d;
            }
        }
    }
}


//画像処理
function uploadImg($file, $key)
{
    debug('画像アップロード開始');
    debug('file情報: '.print_r($file, true));

    //何らかのエラーがある場合
    if (isset($file['error']) && is_int($file['error'])) {
        try {
            //バリデーション
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                break;
            //ファイルが無い
                case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('ファイルが選択されていません');
            //ファイルサイズが大きい
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('ファイルサイズが大きすぎます');
            //それ以外
                default:
                throw new RuntimeException('その他のエラーが発生しました');
            }
            //MIMEタイプチェック
            $type = @exif_imagetype($file['tmp_name']);
            if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                throw new RuntimeException('画像形式が未対応です');
            }

            //ファイルのパスを作る
            //ファイル名はSHA-1でハッシュ化しておく
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            //ファイルを移動出来なかった場合にエラー
            if (!move_uploaded_file($file['tmp_name'], $path)) {
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            //ファイルのパーミッションを変更
            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス: '.$path);
            return $path;
        } catch (RuntimeException $e) {
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

//セッションを1回だけ取得
function getSessionFlash($key)
{
    if (!empty($_SESSION[$key])) {
        $msgData = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $msgData;
    }
}

//8桁の認証キーを作成する
function makeRandKey($length = 8)
{
    //半角英数字を用意
    $chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // 空の変数を用意
    $str = '';
    //for文で生成
    for ($i = 0; $i < $length; ++$i) {
        $str .= $chars[mt_rand(0, 61)];
        //$str = $str.$chars〜と同じ意味になる
    }
    return $str;
}

//ページネーション
function pagenation($currentPageNum, $totalPageNum, $link = '', $pagenationNum = 5)
{
    //左端と右端の数を決める
    //1ページ目かつ5ページ以上ある
    if ($currentPageNum == 1 && $totalPageNum >= $pagenationNum) {
        $minPageNum = $currentPageNum;
        $maxPageNum = $pagenationNum;
    //2ページ目かつ5ページ以上
    } elseif ($currentPageNum == 2 && $totalPageNum >= $pagenationNum) {
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum + 3;
    //現在ページと総ページが同じかつ5ページ以上
    } elseif ($currentPageNum == $totalPageNum && $totalPageNum >= $pagenationNum) {
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
    //現在ページが総ページの一つ手前かつ5ページ以上
    } elseif ($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pagenationNum) {
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum + 1;
    //総ページが5ページ未満の時
    } elseif ($totalPageNum < $pagenationNum) {
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    //それ以外
    } else {
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }
    //ここから表示するページネーション
    echo '<div class="pagenation">';
    echo '<ul class="pagelist">';
    if ($currentPageNum != 1) {
        echo '<li class="page-item"><a href="?p=1">◀</a></li>';
    }
    for ($i = $minPageNum; $i <= $maxPageNum; $i++) {
        echo  '<li class="page-item';
        if ($currentPageNum == $i) {
            echo ' active';
        }
        echo '"><a href="?p='.$i.'">'.$i.'</a></li>';
    }


    if ($currentPageNum != $maxPageNum && $maxPageNum > 1) {
        echo '<li class="page-item"><a href="?p='.$maxPageNum.'">▶</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}

//誕生日から年齢を算出
function getAge($str)
{
    //現在日取得
    $now = date("Ymd");
    $birthdate = str_replace("-", "", $str);
    $age = floor(($now - $birthdate)/10000);
    return $age;
}

//日時フォーマット
function formatDate($str)
{
    $date = new Datetime($str);
    return $date->format('Y年m月d日 H:i:s');
}
