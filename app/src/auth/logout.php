<?php
    require_once '../functions/twig_bootstrap.php';
    require_once '../functions/function.php';
    require_once '../../config/message.php';

    $userName = $_SESSION['userName'];
    sessionDestroy();

    // Topへ戻る
    $back_url = 'http://' . $_SERVER['HTTP_HOST'] . '/app/src/diary/index.php?changeDate=' . date('Y-m-d');

    // テンプレートを使用
    $template = $twig->load("auth/logout.html");

    // レンダリング
    echo $template->render([
        "message" => $msg['logout']['msg01'] . $userName . $msg['logout']['msg02'],
        "back_url" => $back_url,
        "login" => false,
    ]);
