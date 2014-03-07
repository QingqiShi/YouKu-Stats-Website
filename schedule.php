<?php

	function get_url_contents($url){
		$crl = curl_init();
		$timeout = 3;
		curl_setopt ($crl, CURLOPT_URL,$url);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		$ret = curl_exec($crl);
		curl_close($crl);
		return $ret;
	}

	$file = fopen("list.txt","r");

	$file_id = 1;
	while (!feof($file)) {
		$line = fgets($file);
        $line = rtrim($line);
        $line = ltrim($line);
        $user_id = substr($line, 
                          0, 
                          strpos($line, " "));
        $name = substr($line, 
                       strpos($line, " ") + 1, 
                       strpos($line, " ", strlen($user_id)+1) - (strpos($line, " ") + 1));
        $url = substr($line, 
                      strlen($user_id) + strlen($name) + 2);

		$i = false;
		$j = false;
		$count = 0;
		while (($i == false || $j == false) && $count < 5) {
			$str = get_url_contents($url);
			$i = strpos($str, "<li class=\"vnum\"><em>");
			$j = strrpos($str, "<li class=\"snum\" ><em sum_num=\"");
			$count++;
		}

		$view = substr($str, $i +21, 50);
		$view = substr($view, 0, strpos($view, "</em>"));
		$view = str_replace(",", "", $view);	
		
		$sub = substr($str, $j +31, 50);
		$sub = substr($sub, 0, strpos($sub, "\">"));
		
		$visit = 0;

		$analyse = fopen($file_id.".txt","a");


		echo time()." ".$sub." ".$view." ".$visit."\n";
		fwrite($analyse, time()." ".$sub." ".$view." ".$visit."\n");
		fclose($analyse);

		$file_id++;
	}
	fclose($file);
	
?>