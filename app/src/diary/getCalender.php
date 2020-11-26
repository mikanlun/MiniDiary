<?php

require_once '../functions/function.php';

// カレンダー作成
$changeMonth = (string)filter_input( INPUT_POST, "changeMonth" );
$login = (string)filter_input( INPUT_POST, "login" );
list($y, $m) = explode('-', $changeMonth);

// 当月にダイアリーが登録済みか調べる
$fromRelease = date('Y-m-d', strtotime('first day of ' . $changeMonth));
$toRelease = date('Y-m-d', strtotime('last day of ' . $changeMonth));
if ($login) {
    // ユーザー
    $sql = "select release_date as displayDate, DAYOFMONTH(release_date) as displayDay from diaries where user_id = :userId and release_date >= :fromRelease and release_date <= :toRelease order by release_date";
    $params = [
        ":userId" => $_SESSION['userId'],
        ":fromRelease" => $fromRelease,
        ":toRelease" => $toRelease,
    ];
} else {
    // 一般ユーザー
    $sql = "select release_date as displayDate, DAYOFMONTH(release_date) as displayDay  from diaries where release_date >= :fromRelease and release_date <= :toRelease order by release_date";
    $params = [
        ":fromRelease" => $fromRelease,
        ":toRelease" => $toRelease,
    ];
}

$dbh = connectDb();
$stmt = $dbh->prepare($sql);
$stmt->execute($params);
$resutl = $stmt->fetchAll();
$displayDates = [];
foreach ($resutl as $row) {
    $displayDates[$row['displayDay']] = $row['displayDate'];
}

// html作成
echo '<table>';
echo    '<tr>';
$preMonth = date('Y-m', strtotime($y .'-' . $m . ' -1 month'));
echo        '<th class="head"><a class="prev" data-change="' . $preMonth . '"></a></th>';
echo        '<th class="headYM">'. $y . '-' . $m . '</th>';
$nextMonth = date('Y-m', strtotime($y .'-' . $m . ' +1 month'));
echo        '<th class="head"><a class="next" data-change="' . $nextMonth . '"></a></th>';
echo    '</tr>';
echo '</table>';

echo <<<EOD
<table border="1">
<tr class="bg-info text-white">
    <th>日</th>
    <th>月</th>
    <th>火</th>
    <th>水</th>
    <th>木</th>
    <th>金</th>
    <th>土</th>
</tr>
<tr>
EOD;

// 1日の曜日を取得
$wd1 = date("w", mktime(0, 0, 0, $m, 1, $y));

// その数だけ空のセルを作成
for ($i = 1; $i <= $wd1; $i++) {
    echo "<td> </td>";
}
$dd = date('t', strtotime($y . '-' . $m . '-01'));
for ($d = 1; $d <= $dd; $d++) {

    // 日曜：赤色
    if(date("w", mktime(0, 0, 0, $m, $d, $y)) == 0) {
        if (array_key_exists ($d , $displayDates)) {
            echo "<td class='red register'>" . generateDayLink($d, $displayDates[$d]) . "</td>";
        } else {
            echo "<td class='red'>$d</td>";
        }
    }
    // 土曜：青色
    elseif(date("w", mktime(0, 0, 0, $m, $d, $y)) == 6) {
        if (array_key_exists ($d , $displayDates)) {
            echo "<td class='blue register'>" . generateDayLink($d, $displayDates[$d]) . "</td>";
        } else {
            echo "<td class='blue'>$d</td>";
        }
    }
    // 土日祝以外
    else {
        if (array_key_exists ($d , $displayDates)) {
            echo "<td class='register'>" . generateDayLink($d, $displayDates[$d]) . "</td>";
        } else {
            echo "<td>$d</td>";
        }
    }

    // 週の始まりと終わりでタグを出力
    if (date("w", mktime(0, 0, 0, $m, $d, $y)) == 6) {
        // 週を終了
        echo "</tr>";

        // 次の週がある場合は新たな行を準備
        if (checkdate($m, $d + 1, $y)) {
            echo "<tr>";
        }
    }

}

// 最後の週の土曜日まで空のセルを作成
$wdx = date("w", mktime(0, 0, 0, $m + 1, 0, $y));

for ($i = 1; $i < 7 - $wdx; $i++) {
    echo "<td>　</td>";
}

echo <<<EOD
</tr>
</table>
EOD;
