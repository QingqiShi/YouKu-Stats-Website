<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
        <div class="title largeMargin">优酷数据统计</div>
    <div class="pull">
            <form action="process.php" method="post">
                <select name="name" class="name">
<?php
                        $file = fopen("./list.txt", "r");
                        while (!feof($file)) {
                            $line = fgets($file);
                            $line = rtrim($line);
                            $user_id = substr($line, 
                                              0, 
                                              strpos($line, " "));
                            $name = substr($line, 
                                           strpos($line, " ") + 1, 
                                           strpos($line, " ", strlen($user_id)+1) - (strpos($line, " ") + 1));
                            $url = substr($line, 
                                          strlen($user_id) + strlen($name) + 2);
                            echo "                    <option value=\"".$name."\">".$name."</option>\n";
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