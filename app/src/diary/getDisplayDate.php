<?php

    require_once '../functions/function.php';

    // 直近の過去のダイアリーを取得
    $key = (string)filter_input( INPUT_POST, "key" );
    $changeDate = (string)filter_input( INPUT_POST, "changeDate" );
    $login = (string)filter_input( INPUT_POST, "login" );

    if (!$key || !$changeDate || !$login) {
        echo "error";
    } else {

        switch ($key) {
            case 'prev':
                // 直近の過去のダイアリーを取得
                switch ($login) {
                    case "on":
                        // 認証済みのユーザー
                        $sql = "select release_date as displayDate from diaries where user_id = :userId and release_date <= :changeDate order by release_date desc limit 1";
                        $params = [
                            ":userId" => $_SESSION['userId'],
                            ":changeDate" => $changeDate,
                        ];
                        break;

                    default:
                        // 一般ユーザー
                        $sql = "select release_date as displayDate from diaries where release_date <= :changeDate order by release_date desc limit 1";
                        $params = [
                            ":changeDate" => $changeDate,
                        ];
                        break;
                }
                break;

            default:
               // 直近の未来のダイアリーを取得
                switch ($login) {
                    case "on":
                        // 認証済みのユーザー
                        $sql = "select release_date as displayDate from diaries where user_id = :userId and release_date >= :changeDate order by release_date asc limit 1";
                        $params = [
                            ":userId" => $_SESSION['userId'],
                            ":changeDate" => $changeDate,
                        ];
                        break;

                    default:
                        // 一般ユーザー
                        $sql = "select release_date as displayDate from diaries where release_date >= :changeDate order by release_date asc limit 1";
                        $params = [
                            ":changeDate" => $changeDate,
                        ];
                        break;
                }
                break;
        }

        $dbh = connectDb();
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $displayDates = $stmt->fetch(PDO::FETCH_ASSOC);
        // 表示するダイアリーがあるか
        if (count($displayDates)) {
            // 表示するダイアリーがある
            echo $displayDates['displayDate'];
        } else {
            // 表示するダイアリーが無し
            echo false;
        }
    }

