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
		while ($i == false || $j == false || $k == false) {	
			$str = get_url_contents($url);
			$i = strpos($str, "<strong class=\"number\">");
			$j = strrpos($str, "<strong class=\"number\">");
			$k = strpos($str, "<dt>访问:</dt>");
		}

		$sub = substr($str, $i +23, 50);	
		$sub = substr($sub, 0, strpos($sub, "</strong>"));
		$sub = str_replace(",", "", $sub);	
		
		$view = substr($str, $j +23, 50);		
		$view = substr($view, 0, strpos($view, "</strong>"));
		$view = str_replace(",", "", $view);
		
		$visit = substr($str, $k +26, 50);
		$visit = substr($visit, 0, strpos($visit, "</dd>"));
		$visit = str_replace(",", "", $visit);

		$analyse = fopen($id.".txt","a+");
		fwrite($analyse, time()." ".$sub." ".$view." ".$visit."\n");
		fclose($analyse);
	}
	fclose($file);
	
?>