<?php
    require_once '../functions/twig_bootstrap.php';
    require_once '../functions/function.php';
    require_once '../../config/message.php';

    // ログイン認証
    $login = checkLogin(true);

    // Topへ戻る
    if (isset($_SESSION['nowDate'])) {
        $backDate = $_SESSION['nowDate'];
    } else {
        $backDate = date('Y-m-d');
    }

    $back_url = 'http://' . $_SERVER['HTTP_HOST'] . '/app/src/diary/index.php?changeDate=' . $backDate;

    // 新規登録
    $auth_register_url = '/app/src/auth/register.php';

     // テンプレートを使用
    $template = $twig->load("support/about.html");

    // レンダリング
    // 認証済みか
    if ($login) {
        // 認証済み
        echo $template->render([
            "userName" => $_SESSION['userName'],
            "aboutMsg1" => $msg['about']['comment1'],
            "back_url" => $back_url,
            "login" => $login,
        ]);
    } else {
        // 一般ユーザー
        echo $template->render([
            "aboutMsg1" => $msg['about']['comment1'],
            "aboutMsg2" => $msg['about']['comment2'],
            "back_url" => $back_url,
            "auth_register_url" => $auth_register_url,
            "login" => $login,
        ]);
    }

