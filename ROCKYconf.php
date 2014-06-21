<?php
    // Define variables to store our username
    $user = 'root';
    $pass = '';
    $database = 'rocky_youkustats';

    // Create a connection to the MySql server
    $conn = mysql_connect('localhost', $user, $pass);
    
    // Check if connection was successful
    if (!$conn)
    {
        die ('连接数据库失败：' . mysql_error());
    }
    
    // Change to correct database on the server
    $select_db_success = mysql_select_db($database, $conn);
    
    // Check database selection was successful
    if (!$select_db_success)
    {
        die ("无法对数据库执行SELECT操作：" . mysql_error());
    }

    mysql_query("SET NAMES 'UTF8'");
?>
