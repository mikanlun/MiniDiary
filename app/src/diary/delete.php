<?php
    require_once "../functions/twig_bootstrap.php";
    require_once "../functions/function.php";
    require_once '../../config/validation.php';

    // ログイン認証
    checkLogin();

    if ((string)filter_input( INPUT_SERVER, "REQUEST_METHOD" ) == "GET") {
        // Diary 取得
        $diaryId = filter_input( INPUT_GET, "diaryId" );
        $dbh = connectDb();

        // Diary 取得
        $sql = "select title, path, image, comment, release_date from diaries where id = :diaryId limit 1";
        $stmt = $dbh->prepare($sql);
        $params = [
            ":diaryId" => $diaryId,
        ];

        $stmt->execute($params);
        $diary1 = $stmt->fetch(PDO::FETCH_ASSOC);

        $title = $diary1['title'];
        $imagePath = $diary1['path'];
        $oldImageName = $diary1['image'];
        $comment = $diary1['comment'];
        $releaseDate = $diary1['release_date'];


        $err_mesgs = [];
        try {
            $dir = "../../../assets/img/" . $imagePath;
            // 登録済みの画像の削除
            $oldPath = $dir . "/" . $oldImageName;
            if(file_exists($oldPath)){
                if (!unlink($oldPath)) {
                    throw new RuntimeException($errMsg['file']['unDeleted']);
                }
            } else {
                throw new RuntimeException($errMsg['file']['unExisted']);
            }

            // albumsから削除
            $sql = "delete from diaries where id = :diaryId";
            $stmt = $dbh->prepare($sql);
            $params = [
                ":diaryId" => $diaryId,
            ];

            $stmt->execute($params);

           // gallery Top
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/app/src/diary/index.php?changeDate=' . $_SESSION['nowDate'];
            header ('Location: ' . $url);
            exit();

        } catch (RuntimeException $e) {

            $err_mesgs[] = $e->getMessage();

        }


    } else {
         echo "不正なPOSTが行われました。";
         exit();
    }


    // CSRF対策
    setToken();

    // Topへ戻る
    $back_url = 'http://' . $_SERVER['HTTP_HOST'] . '/app/src/diary/index.php?changeDate=' . $_SESSION['nowDate'];

    // 画像削除
    $image_delete_url = '/src/albums/delete.php?diaryId=' . $diaryId;

    // テンプレートを使用
    $template = $twig->load("diary/edit.html");
    // レンダリング
    echo $template->render([
        "err_mesgs_cnt" => count($err_mesgs),
        "err_mesgs" => $err_mesgs,
        "diaryId" => $diaryId,
        "title" => $title,
        "imagePath" => $imagePath,
        "oldImageName" => $oldImageName,
        "comment" => $comment,
        "releaseDate" => $releaseDate,
        "userName" => $_SESSION["userName"],
        "back_url" => $back_url,
        "image_delete_url" => $image_delete_url,
        "login" => true,
        "token" => $_SESSION["token"],
    ]);
