<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<div class="pull">
		<form action="process.php" method="post">
			<select name="name" class="name">
				<?php
					$file = fopen("./list.txt", "r");
					while (!feof($file)) {
						$temp = rtrim(fgets($file));
						echo "<option value=\"".$temp."\">".$temp."</option>\n";
					}
					fclose($file);
				?>
			</select>
			<input type="hidden" name="frequency" value="24">
            <input type="hidden" name="range" value="30">
			<input type="submit" value="拉取数据" class="submit"/>
		</form>
		</div>
	</body>
</html>					