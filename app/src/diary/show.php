<?php
    require_once "../functions/twig_bootstrap.php";
    require_once "../functions/function.php";

    // ログイン認証
    $login = checkLogin(true);

    $err_mesgs = [];
    if ((string)filter_input( INPUT_SERVER, "REQUEST_METHOD" ) == "GET") {
        // Diary 取得
        $diaryId = filter_input( INPUT_GET, "diaryId" );
        $dbh = connectDb();
        // アルバム情報取得
        $sql = "select user_id, title, path, image, comment, DATE_FORMAT(modified, '%H:%i') as modified_time from diaries where id = :diaryId limit 1";
        $stmt = $dbh->prepare($sql);
        $params = [
            ":diaryId" => $diaryId,
        ];

        $stmt->execute($params);
        $diary = $stmt->fetch(PDO::FETCH_ASSOC);

        $userId = $diary['user_id'];
        $title = $diary['title'];
        $imagePath = $diary['path'];
        $image = $diary['image'];
        $comment = $diary['comment'];
        $modified_time = $diary['modified_time'];

        // ユーザー情報取得
        $sql = "select name from users where id = :userId limit 1";
        $stmt = $dbh->prepare($sql);
        $params = [
            ":userId" => $userId,
        ];

        $stmt->execute($params);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $user['name'];

        // 認証済みか
        if ($login === true) {
            $userName = $_SESSION["userName"];
        } else {
            $userName = '';
        }

    } else {
        echo "不正なPOSTが行われました。";
        exit();
    }

    // Topへ戻る
    $back_url = 'http://' . $_SERVER['HTTP_HOST'] . '/app/src/diary/index.php?changeDate=' . $_SESSION['nowDate'];

    // テンプレートを使用
    $template = $twig->load("diary/show.html");
    // レンダリング
    echo $template->render([
        "nowDate" => $_SESSION['nowDate'],
        "userId" => $userId,
        "userId" => $userId,
        "diaryId" => $diaryId,
        "title" => $title,
        "imagePath" => $imagePath,
        "image" => $image,
        "comment" => $comment,
        "name" => $name,            // 作者
        "modified_time" => $modified_time,
        "userName" => $userName,    // ヘッダー用
        "back_url" => $back_url,
        "login" => $login,
    ]);

