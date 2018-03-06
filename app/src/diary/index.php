<?php

    require_once '../functions/twig_bootstrap.php';
    require_once '../functions/function.php';
    require_once '../../config/message.php';
    require_once '../../config/define.php';

    $login = false;
    $dbh = connectDb();
    $unregisterFlg = false;
    // ダイアリーの表示日付を設定
    if ((string)filter_input( INPUT_SERVER, "REQUEST_METHOD" ) == "GET") {
        $changeDate = (string)filter_input( INPUT_GET, 'changeDate' );
        if (!$changeDate) {
            $nowDate = date('Y-m-d');
        } else {
            // ダイアリーの表示日の変更
            $nowDate = $changeDate;
        }
        $searchUserId = 0;
    } else {
        // CSRF対策
        checkToken();
        $searchUserId = (string)filter_input( INPUT_POST, "searchUserId" );
        $nowDate = (string)filter_input( INPUT_POST, "searchDate" );

    }
    $_SESSION['nowDate'] = $nowDate;
    $nowWeek = $week[date('w', strtotime($nowDate))];
    $preDate = date("Y-m-d", strtotime("$nowDate-1 day"  ));
    $nextDate = date("Y-m-d", strtotime("$nowDate+1 day"  ));
    $searchUsers = [];

    // CSRF対策
    setToken();
    // 認証済みのユーザーか
    if (checkLogin(true) === true) {

        // 認証済みのユーザーのダイアリー 取得
        $login = true;
        $userName = $_SESSION['userName'];

        $sql = "select u.id as userId, u.name, d.id as diaryId, d.title, d.path, d.image, d.comment, d.modified, DATE_FORMAT(d.modified, '%Y-%m-%d %H:%i') as modified_time from  users u, diaries d where u.id = :userId and  u.id = d.user_id and d.release_date = :release_date order by d.modified desc";
        $stmt = $dbh->prepare($sql);
        $params = [
            ":userId" => $_SESSION['userId'],
            ":release_date" => $nowDate,
        ];

        $stmt->execute($params);
        $diaries = $stmt->fetchAll();

        $searchUser['id'] = $_SESSION['userId'];
        $searchUser['name'] = $userName;
        $searchUsers[] = $searchUser;

        // Render
        $template = $twig->load('diary/index.html');
        // 認証済みのユーザーのダイアリーは登録済みか
        if (!count($diaries)) {
            // 認証済みのユーザーのダイアリーは未登録
            $unregisterFlg = true;
            $unregisters = [];
            $unregisters['title'] = $msg['wwllcome']['title'];
            $unregisters['comment'] = $userName . $msg['wwllcome']['comment'];
            echo $template->render([
                'unregisterFlg' => $unregisterFlg,
                'unregisters' => $unregisters,
                'nowDate' => $nowDate,
                'nowWeek' => $nowWeek,
                'preDate' => $preDate,
                'nextDate' => $nextDate,
                'searchUsers' => $searchUsers,
                'url' => '/app/src/diary/register.php',
                'login' => $login,
                'userName' => $userName,
                'btn_title' => 'ダイアリー登録',
                "token" => $_SESSION["token"],
            ]);
        } else {
            // 認証済みのユーザーのダイアリーは登録済み
            $unregisterFlg = false;
            echo $template->render([
                'unregisterFlg' => $unregisterFlg,
                'nowDate' => $nowDate,
                'nowWeek' => $nowWeek,
                'preDate' => $preDate,
                'nextDate' => $nextDate,
                'searchUsers' => $searchUsers,
                'diaries' => $diaries,
                'show_url' => '/app/src/diary/show.php',
                'login' => $login,
                'userName' => $userName,
                'userCnt' => 1,         // ユーザー数は、認証済みユーザーの一人のみ
                'userId'  => $_SESSION['userId'],
                "token" => $_SESSION["token"],
            ]);
        }
    } else {

        // 一般ユーザーのダイアリー 取得
        $login = false;
        if ($searchUserId == 0) {
            // 全員
            $sql = "select u.id as userId, u.name, d.id as diaryId, d.title, d.path, d.image, d.comment, d.modified, DATE_FORMAT(d.modified, '%Y-%m-%d %H:%i') as modified_time from  users u, diaries d where u.id = d.user_id and d.release_date = :release_date order by d.modified desc";
            $stmt = $dbh->prepare($sql);
            $params = [
                ":release_date" => $nowDate,
            ];

        } else {
            // ユーザー指定（検索）
            $sql = "select u.id as userId, u.name, d.id as diaryId, d.title, d.path, d.image, d.comment, d.modified, DATE_FORMAT(d.modified, '%Y-%m-%d %H:%i') as modified_time from  users u, diaries d where u.id = d.user_id and u.id = :searchUserId and d.release_date = :release_date order by d.modified desc";
            $stmt = $dbh->prepare($sql);
            $params = [
                ":searchUserId" => $searchUserId,
                ":release_date" => $nowDate,
            ];

        }
        $stmt->execute($params);
        $diaries = $stmt->fetchAll();

        // 検索ユーザー一覧を作成
        $sql = "select distinct u.id, u.name from  users u, diaries d where u.id = d.user_id order by u.id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $searchUsers =  $stmt->fetchAll();
        if (count($searchUsers)) {
            $top['id'] = 0;
            $top['name'] = '全員';
            array_unshift($searchUsers, $top);
        } else {
            $top['id'] = 0;
            $top['name'] = 'ユーザー未投稿';
            $searchUsers[] = $top;
        }

        // Render
        $template = $twig->load('diary/index.html');
        // 一般ユーザーのダイアリーは登録済みか
        $unregisterFlg = false;
        if (!count($diaries)) {
            // 一般ユーザーのダイアリーは未登録
            $unregisterFlg = true;
            $unregisters = [];
            $unregisters['title'] = $msg['unregister']['title'];
            $unregisters['comment'] = $msg['unregister']['comment'];
            echo $template->render([
                'unregisterFlg' => $unregisterFlg,
                'unregisters' => $unregisters,
                'nowDate' => $nowDate,
                'nowWeek' => $nowWeek,
                'preDate' => $preDate,
                'nextDate' => $nextDate,
                'searchUsers' => $searchUsers,
                'searchUserId' => $searchUserId,
                'url' => '/app/src/auth/login.php',
                'login' => $login,
                'btn_title' => 'ログイン',
                "token" => $_SESSION["token"],
            ]);
        } else {
            // 一般ユーザーのダイアリーは登録済み
            $unregisterFlg = false;
            echo $template->render([
                'unregisterFlg' => $unregisterFlg,
                'nowDate' => $nowDate,
                'nowWeek' => $nowWeek,
                'preDate' => $preDate,
                'nextDate' => $nextDate,
                'searchUsers' => $searchUsers,
                'searchUserId' => $searchUserId,
                'diaries' => $diaries,
                'show_url' => '/app/src/diary/show.php',
                'login' => $login,
                "token" => $_SESSION["token"],
            ]);
        }
    }
