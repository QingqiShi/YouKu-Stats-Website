<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
    include_once('ROCKYconf.php');
?>
    <div class="title largeMargin">
        优酷数据统计
    </div>
    <div class="pull">
        <form action="process.php" method="get">
            <select name="name" class="name">

<?php
    $userList = getAllUsers();
    while ($row = mysql_fetch_array($userList))
    {
        echo "                <option value=\"".$row['u_name']."\">".$row['u_name']."</option>\n";
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

<?php
    function getAllUsers() {
        $query = 'SELECT u_name FROM user ORDER BY u_id ASC';
        $result = mysql_query($query);

        return $result;
    }
?>