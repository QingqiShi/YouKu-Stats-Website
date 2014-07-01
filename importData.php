<?php

require_once 'core/init.php';

date_default_timezone_set('Asia/Shanghai');

for ($i=1; $i < 13; $i++) {
    importDataFromFile($i. '.txt', $i);
}



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

    $result = DB::getInstance()->query($query);

    if (!$result->error()) {
        echo $fileName . "完成！";
    } else {
        echo $fileName . "失败 :-(";
    }

    fclose($file);
}

?>