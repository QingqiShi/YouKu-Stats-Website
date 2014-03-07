<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); ?>
<?php
    date_default_timezone_set('PRC');
    $name = $_POST["name"];
if ($name != "") {
    $frequency = $_POST["frequency"];
    $graphType = $_POST["graphType"];
    $range = $_POST["range"];
    
    $tmp_file = fopen("list.txt", "r");
    $id = 0;
    while (!feof($tmp_file)) {
        $id++;
        $line = fgets($tmp_file);
        $line = rtrim($line);
        $user_id = substr($line, 
                          0, 
                          strpos($line, " "));
        $user_name = substr($line, 
                       strpos($line, " ") + 1, 
                       strpos($line, " ", strlen($user_id)+1) - (strpos($line, " ") + 1));
        $url = substr($line, 
                                          strlen($user_id) + strlen($name) + 2);
        if ($name == $user_name) {
            break;
        }
    }
    $file = fopen($id.".txt", "r");
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="style.css">
        <script type="text/javascript" src="jquery-2.0.3.min.js"></script>
        <script type="text/javascript" src="jscharts.js"></script>
    </head>
    <body>

        <?php

            $dateArray = array();
            $subArray = array();
            $viewArray = array();

            $pre_sub = 0;
            $pre_view = 0;
            $pre_visit = 0;
            $sub = 0;
            $view = 0;
            $visit = 0;
            $i = 0;
            $pre_set = 0;   
            if ($frequency == 1) {
                $timeStr = "G";
                $loop = 22;
            } else if ($frequency == 24) {
                $timeStr = "j";
                $loop = 30;
            } else if ($frequency == 144) {
                $timeStr = "W";
                $loop = 51;
            } else if ($frequency == 744) {
                $timeStr = "n";
                $loop = 11;
            }

            while (feof($file) == false && $range != 0) {
                $record = rtrim(fgets($file));
                $temp = substr($record, 0, strpos($record, " "));
                if ((time() - $temp) <= ($range * 24 * 3600)) {
                    break;
                }
                if ($record != "" && date($timeStr, $temp) != $pre_set) {
                    $pre_set = date($timeStr, $temp);

                    $temp = ltrim(substr($record, strpos($record, " ")));
                    $sub = substr($temp, 0, strpos($temp, " "));

                    $pre_sub = $sub;

                    $temp = ltrim(substr($temp, strpos($temp, " ")));
                    $view = substr($temp, 0, strpos($temp, " "));

                    $pre_view = $view;

                    $visit = substr($temp, strpos($temp, " ")+1);

                    $pre_visit = $visit;
                }
            }
                
            $total_sub = 0;
            $total_view = 0;
            $total_visit = 0;
            $count = 0;

            while (feof($file) == false) {
                $record = rtrim(fgets($file));
                $temp = substr($record, 0, strpos($record, " "));
                if ($record != "" && date($timeStr, $temp) != $pre_set) {
                            //echo date("n\月j\日 G\:i", $temp);
                            array_push($dateArray, $temp);
                            $pre_set = date($timeStr, $temp);

                            $temp = ltrim(substr($record, strpos($record, " ")));
                            $sub = substr($temp, 0, strpos($temp, " "));
                            array_push($subArray, $sub);

                            if ($pre_sub != 0) {
                                // echo ($sub - $pre_sub);
                                $total_sub += $sub - $pre_sub;
                            }
                            $pre_sub = $sub;

                            $temp = ltrim(substr($temp, strpos($temp, " ")));
                            $view = substr($temp, 0, strpos($temp, " "));
                            array_push($viewArray, $view);

                            if ($pre_view != 0) {
                                // echo ($view - $pre_view);
                                $total_view += $view - $pre_view;
                            }
                            $pre_view = $view;

                            // $visit = substr($temp, strpos($temp, " ")+1);
                            // echo $visit;

                            // if ($pre_visit != 0) {
                            //     echo ($visit - $pre_visit);
                            //     $total_visit += $visit - $pre_visit;
                            // }
                            // $pre_visit = $visit;

                    $count++;
                }
            }
        ?>





        <div class="wrap">
        <div class="back"><a href="./">返回首页</a></div>
        <p class="title"><a href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a></p>
        <div class="freq_change">
        <form action="#" method="POST">
            <input type="hidden" name="name" value="<?php echo $name;?>">
            <select name="frequency" class="name">
                <option value="24"<?php if($frequency == 24) {echo " selected";}?>>每日</option>
                <option value="144"<?php if($frequency == 144) {echo " selected";}?>>每周</option>
                <option value="744"<?php if($frequency == 744) {echo " selected";}?>>每月</option>
            </select>
            <select name="range" class="name">
                <option value="0"<?php if($range == 0) {echo " selected";}?>>生命周期</option>
                <option value="7"<?php if($range == 7) {echo " selected";}?>>过去7天</option>
                <option value="30"<?php if($range == 30) {echo " selected";}?>>过去30天</option>
                <option value="90"<?php if($range == 90) {echo " selected";}?>>过去90天</option>
                <option value="365"<?php if($range == 365) {echo " selected";}?>>过去365天</option>
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
} else {
     header( 'Location: http://www.cssanott.co.uk/YouKu-Stats-Website/' ) ;
}