<?php

include_once('ROCKYconf.php');

importDataFromFile('12.txt', 12);


function importDataFromFile($fileName, $u_id) {
    $file = fopen($fileName, "r");

    $pre_sub = 0;
    $pre_view = 0;

    $query = "INSERT INTO `data`(`d_timestamp`, `u_ID`, `d_sub`, `d_view`) VALUES ";

    while (feof($file) == false) {
        $record = rtrim(fgets($file));
        if ($record != "") {
            $data_array = explode(' ', $record, 4);
            
            if ($data_array[1] == "") {
                $data_array[1] = $pre_sub;
            }
            if ($data_array[2] == '') {
                $data_array[2] = $pre_view;
            }
            
            $query .= "('".$data_array[0]."', '".$u_id."', '".$data_array[1]."', '".$data_array[2]."'), ";
            

            // mysql_query($query);


            $pre_sub = $data_array[1];
            $pre_view = $data_array[2];
        }
    }

    $query = substr($query, 0, -2);
    echo $query;
    $result = mysql_query($query);
    if ($result) {
        echo "完成！";
    } else {
        echo "失败 :-(";
    }
}

?>