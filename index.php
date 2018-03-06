<?php

/****************/
/* entry point */
/****************/

$mainUrl = "http://" . $_SERVER['HTTP_HOST'] . "/app/src/diary/index.php";
header ('Location:' . $mainUrl);
exit();
