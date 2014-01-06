<?php
    date_default_timezone_set('PRC');
    $name = $_POST["name"];
if ($name != "") {
    $frequency = $_POST["frequency"];
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
        <script type="text/javascript" src="script.js"></script>
    </head>
    <body>
        <div class="wrap">
        <div class="back"><a href="./">返回首页</a></div>
        <p class="title"><a href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a></p>
        <div class="freq_change">
        <form action="#" method="POST">
            <input type="hidden" name="name" value="<?php echo $name;?>">
            <select name="frequency" class="name">
                <option value="1"<?php if($frequency == 1) {echo " selected";}?>>每小时</option>
                <option value="24"<?php if($frequency == 24) {echo " selected";}?>>每日</option>
                <option value="144"<?php if($frequency == 144) {echo " selected";}?>>每周</option>
                <option value="744"<?php if($frequency == 744) {echo " selected";}?>>每月</option>
            </select>
            <select name="range" class="name">
                <option value="7"<?php if($range == 7) {echo " selected";}?>>过去7天</option>
                <option value="30"<?php if($range == 30) {echo " selected";}?>>过去30天</option>
                <option value="90"<?php if($range == 90) {echo " selected";}?>>过去90天</option>
                <option value="365"<?php if($range == 365) {echo " selected";}?>>过去365天</option>
                <option value="0"<?php if($range == 0) {echo " selected";}?>>生命周期</option>
            </select>
            <input type="submit" value="拉取数据" class="submit" />
        </form>
        </div>
        <div class="hide_show"><button type="button">显示数据</button></div>
        <div class="table">
        <table border="1">
            <tr>
                <th>日期 时间</th>
                <th>粉丝数</th>
                <th>粉丝增长</th>
                <th>播放次数</th>
                <th>播放增长</th>
                <th>主页访问</th>
                <th>访问增长</th>
            <tr>
<?php
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
?>
            <tr>
                <td><?php 
                    echo date("n\月j\日 G\:i", $temp);
                    $pre_set = date($timeStr, $temp);
                ?></td>
                <td><?php 
                    $temp = ltrim(substr($record, strpos($record, " ")));
                    $sub = substr($temp, 0, strpos($temp, " "));
                    echo $sub;
                ?></td>
                <td><?php 
                    if ($pre_sub != 0) {
                        echo ($sub - $pre_sub);
                        $total_sub += $sub - $pre_sub;
                    }
                    $pre_sub = $sub;
                ?></td>
                <td><?php 
                    $temp = ltrim(substr($temp, strpos($temp, " ")));
                    $view = substr($temp, 0, strpos($temp, " "));
                    echo $view;
                ?></td>
                <td><?php 
                    if ($pre_view != 0) {
                        echo ($view - $pre_view);
                        $total_view += $view - $pre_view;
                    }
                    $pre_view = $view;
                ?></td>
                <td><?php 
                    $visit = substr($temp, strpos($temp, " ")+1);
                    echo $visit;
                ?></td>
                <td><?php 
                    if ($pre_visit != 0) {
                        echo ($visit - $pre_visit);
                        $total_visit += $visit - $pre_visit;
                    }
                    $pre_visit = $visit;
                ?></td>
            </tr>
<?php
            $count++;
        }
    }
?>
        </table>
    </div>

        <div id="data_summary" class="clearfix">
            <div class="average_sub"><p><span class="lable">平均粉丝增长</span>
            <span class="data"><?php
                echo round($total_sub / $count);
            ?></span></p></div>
            <div class="average_view"><p><span class="lable">平均播放增长</span>
            <span class="data"><?php
                echo round($total_view / $count);
            ?></span></p></div>
            <div class="average_visit"><p><span class="lable">平均访问增长</span>
            <span class="data"><?php
                echo round($total_visit / $count);
            ?></span></p></div>
            <div></div>
        </div>
        <div class="back"><a href="./">返回首页</a></div>
        </div>
    </body>
</html> 
<?php
} else {
     header( 'Location: http://www.cssanott.co.uk/YouKu-Stats-Website/' ) ;
}