<?php

	function get_url_contents($url){
		$crl = curl_init();
		$timeout = 5;
		curl_setopt ($crl, CURLOPT_URL,$url);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		$ret = curl_exec($crl);
		curl_close($crl);
		return $ret;
	}

	$file = fopen("list.txt","r");

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

		$i = false;
		$j = false;
		$k = false;
		while ($i == false || $j == false) {	
			$str = get_url_contents($url);
			$i = strpos($str, "<li class=\"vnum\"><em>");
			$j = strrpos($str, "<li class=\"snum\" ><em sum_num=\"");
		}

		$view = substr($str, $i +21, 50);	
		$view = substr($sub, 0, strpos($sub, "</em>"));
		$view = str_replace(",", "", $sub);	
		
		$sub = substr($str, $j +31, 50);		
		$sub = substr($view, 0, strpos($view, "\">"));
		
		$visit = 0;

		$analyse = fopen($user_id.".txt","a+");

		fwrite($analyse, time()." ".$sub." ".$view." ".$visit."\n");
		fclose($analyse);
	}
	fclose($file);
	
?>