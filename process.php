<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
<?php
	date_default_timezone_set('PRC');
	$name = $_POST["name"];
	$frequency = $_POST["frequency"];
    $range = $_POST["range"];
?>
		<div class="wrap">
		<p class="name"><?php echo $name; ?></p>
		<div class="freq_change">
		<form action="#" method="post">
			<input type="hidden" name="name" value="<?php echo $name;?>">
			<select name="frequency" class="name">
				<option value="7"<?php if($frequency == 1) {echo " selected";}?>>当天 </option>
				<option value="24"<?php if($frequency == 24) {echo " selected";}?>>每日</option>
				<option value="144"<?php if($frequency == 144) {echo " selected";}?>>每周</option>
				<option value="744"<?php if($frequency == 744) {echo " selected";}?>>每月</option>
			</select>
            <select name="range" class="name">
				<option value="1"<?php if($range == 7) {echo " selected";}?>>过去7天</option>
				<option value="7"<?php if($range == 30) {echo " selected";}?>>过去30天</option>
				<option value="144"<?php if($range == 90) {echo " selected";}?>>过去90天</option>
				<option value="744"<?php if($range == 365) {echo " selected";}?>>过去365天</option>
			</select>
			<input type="submit" value="拉取数据" class="submit" />
		</form>
		</div>
		<a href="./">返回</a>
<?php
	$file = fopen($name.".txt", "r");
?>
		<table border="1" class="table">
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

	while (feof($file) == false) {
		$record = rtrim(fgets($file));
		$temp = substr($record, 0, strpos($record, " "));
        if (time() - $temp <= $range * 24 * 60 * 60) {
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
                        }
                        $pre_visit = $visit;
                    ?></td>
                </tr>
    <?php
            }
        }
	}
?>
		</table>
		<a href="./">返回</a>
		</div>
	</body>
</html>					