<?php require_once 'core/init.php' ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>优酷数据统计</title>
</head>
<body>
    <div class="title largeMargin">
        优酷数据统计
    </div>
    <div class="pull">
        <form action="process_new.php" method="get">
            <select name="name" class="name">

<?php
    $userList = DB::getInstance()->query("SELECT u_name FROM user ORDER BY u_ID");
    foreach ($userList->results() as $user) {
        echo "                <option value=\"{$user->u_name}\">{$user->u_name}</option>\n";
    }
?>

            </select>
            <input type="hidden" name="frequency" value="daily">
            <input type="hidden" name="range" value="lastmonth">
            <input type="hidden" name="graphType" value="2">
            <input type="submit" value="拉取数据" class="submit"/>
        </form>
    </div>
</body>
</html>