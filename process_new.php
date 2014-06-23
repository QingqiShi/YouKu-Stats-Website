<?php
require_once 'core/init.php';

// date_default_timezone_set('PRC');

// $name = $_GET["name"];
// $frequency = $_GET["frequency"];
// $graphType = $_GET["graphType"];
// $range = $_GET["range"];

if (false) {
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="JS/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="JS/chart.js"></script>
</head>
<body>

    <div class="wrap">
    <div class="back">
        <a href="./">返回首页</a>
    </div>
    <p class="title">
        <a href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a>
    </p>
    <div class="freq_change">

        <form action="#" method="POST">
            <input type="hidden" name="name" value="<?php echo $name;?>">
            <select name="frequency" class="name">
                <option value="daily"<?php if($frequency == "daily") {echo " selected";}?>>每日</option>
                <option value="weekly"<?php if($frequency == "weekly") {echo " selected";}?>>每周</option>
                <option value="monthly"<?php if($frequency == "monthly") {echo " selected";}?>>每月</option>
            </select>
            <select name="range" class="name">
                <option value="lifetime"<?php if($range == "lifetime") {echo " selected";}?>>生命周期</option>
                <option value="lastweek"<?php if($range == "lastweek") {echo " selected";}?>>过去7天</option>
                <option value="lastmonth"<?php if($range == "lastmonth") {echo " selected";}?>>过去30天</option>
                <option value="last90days"<?php if($range == "last90days") {echo " selected";}?>>过去90天</option>
                <option value="lastyear"<?php if($range == "lastyear") {echo " selected";}?>>过去365天</option>
            </select>
            <select name="graphType" class="name">
                <option value="1"<?php if($graphType == 1) {echo " selected";}?>>粉丝数</option>
                <option value="2"<?php if($graphType == 2) {echo " selected";}?>>粉丝增长</option>
                <option value="3"<?php if($graphType == 3) {echo " selected";}?>>播放数</option>
                <option value="4"<?php if($graphType == 4) {echo " selected";}?>>播放增长</option>
            </select>
            <input type="submit" value="拉取数据" class="submit" />
        </form>

    </div>

    <div id="chartcontainer">图表无法加载 :-(</div>


    <?php
    $arrayData = array();
    $lableInterval = count($subArray)/8;
    $dateFormat = "n\月j\日";
    if ($graphType == 1) {
        $arrayData = $subArray;
        $title = '粉丝数变化';
    } elseif ($graphType == 2) {
        $arrayData[0] = 0;
        for ($i = 1; $i < count($subArray); $i++) {
            array_push($arrayData, $subArray[$i] - $subArray[$i-1]);
        }
        $arrayData[0] = $arrayData[1] * 0.9;
        $title = '粉丝数增长';
    } elseif ($graphType == 3) {
        $arrayData = $viewArray;
        $title = '播放数变化';
    } elseif ($graphType == 4) {
        $arrayData[0] = 0;
        for ($i = 1; $i < count($viewArray); $i++) {
            array_push($arrayData, $viewArray[$i] - $viewArray[$i-1]);
        }
        $arrayData[0] = $arrayData[1] * 0.9;
        $title = '播放数增长';
    }
    ?>


    <script type="text/javascript">
        var myChart = new JSChart('chartcontainer', 'line', '');
        myChart.setDataArray([<?php
            for ($i = 0; $i < count($arrayData); $i++) {
                echo '[';
                echo $i;
                echo ', ';
                echo $arrayData[$i];
                echo ']';
                if ($i+1 < count($arrayData)) {
                    echo ', ';
                }
            }

        ?>]);
        myChart.colorize(['#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9','#3E90C9']);
        myChart.setSize(830, 320);
        myChart.setIntervalEndY(<?php echo max($arrayData) * 1.1; ?>);
        myChart.setIntervalStartY(0);
        myChart.setAxisNameX('');
        myChart.setAxisNameY('');
        myChart.setAxisValuesNumberY(8);
        myChart.setTitle('<?php
                         echo $title;
                         ?>');
        myChart.setTitleFontSize(12);
        myChart.setLineSpeed(100)
        myChart.setTitleColor('#424342');
        myChart.setAxisValuesColor('#444444');
        myChart.setShowXValues(false);

        <?php

            for ($i = 0; $i < count($arrayData); $i+=$lableInterval) {
                echo 'myChart.setLabelX([';
                echo $i;
                echo ', \'';
                echo date($dateFormat, $dateArray[$i]);
                echo '\']);
        ';
            }
        ?>

        myChart.setLineColor('#D92323');

        <?php

            for ($i = 0; $i < count($arrayData); $i++) {
                echo 'myChart.setTooltip([';
                echo $i;
                echo ', \'';
                echo date($dateFormat, $dateArray[$i]);
                echo ' ';
                echo $arrayData[$i];
                echo '\']);
        ';
            }
        ?>
        myChart.setFlagRadius(2);
        myChart.draw();
    </script>

            <div class="hide_show"><button type="button">显示数据</button></div>
            <div class="table">
            <table border="1">
                <tr>
                    <th>日期 时间</th>
                    <th>粉丝数</th>
                    <th>粉丝增长</th>
                    <th>播放数</th>
                    <th>播放增长</th>
                <tr>
                <?php
                    $totalSub = 0;
                    $totalView = 0;
                    for ($i = 0; $i < count($dateArray); $i++) {
                        ?>
                        <tr>
                            <td><?php echo date($dateFormat, $dateArray[$i]); ?></td>
                            <td><?php echo $subArray[$i]; ?></td>
                            <td><?php
                            if ($i > 0) {
                                echo $subArray[$i] - $subArray[$i - 1];
                            }  ?></td>
                            <td><?php echo $viewArray[$i]; ?></td>
                            <td><?php
                            if ($i > 0) {
                                echo $viewArray[$i] - $viewArray[$i - 1];
                            }  ?></td>
                        <tr>
                    <?php
                    }
                ?>
            </table>
            </div>
    <div class="back"><a href="./">返回首页</a></div>
    </div>
</body>
</html> 
<?php
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="JS/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="JS/chart.js"></script>
</head>
<body>

<?php
if (Input::exists('get')) {
    $validator = new Validate();
    $validator->check($_GET, array(
        'name' => array(
            'name' => '用户名',
            'required' => true
            ),
        'frequency' => array(
            'name' => '频率',
            'required' => true
            ),
        'range' => array(
            'name' => '日期范围',
            'required' => true
            ),
        'graphType' => array(
            'name' => '数据种类',
            'required' => true
            )
        ));
    if ($validator->passed()) {
        echo 'passed';
    } else {
        print_r($validator->errors());
    }
} else {
    echo '没有输入数据。';
}

?>

</body>
</html>