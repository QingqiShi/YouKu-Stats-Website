<?php
require_once 'core/init.php';

date_default_timezone_set('Asia/Shanghai');

function get_xpath_result($result) {
	if ($result->length > 0) {
		return $result->item(0)->nodeValue;
	} else {
		return '';
	}
}



$data_array = array();

$db = DB::getInstance()->query('SELECT * FROM user');

if (Input::get('id') !== '') {
	$retrieved_values = array(
		'id' => '',
		'user_name' => '',
		'view' => '',
		'sub' => '',
		'avatar' => '',
		'video_num' => ''
	);

	// Get user ID
	$retrieved_values['id'] = $db->results(Input::get('id'), 'u_id');

	// Fetch url content
	$url_content = file_get_contents($db->results(Input::get('id'), 'u_url'));

	// Setting up DOM parser
	$dom = new DOMDocument();
	@$dom->loadHTML($url_content);
	$xPath = new DOMXpath($dom);


	// Parse DOM
	$user_name = $xPath->query('//*[@id="topzone"]/div/div[2]/div[2]/div[1]/div/a[1]');
	$retrieved_values['user_name'] = get_xpath_result($user_name);
	$view = $xPath->query('//*[@id="topzone"]/div/div[2]/div[2]/div[3]/ul/li[1]/em');
	$retrieved_values['view'] = get_xpath_result($view);
	$sub = $xPath->query('//*[@id="topzone"]/div/div[2]/div[2]/div[3]/ul/li[3]/em');
	$retrieved_values['sub'] = get_xpath_result($sub);
	$avatar = $xPath->query('//*[@id="topzone"]/div/div[2]/div[1]/a/img');
	if($avatar->length > 0) {
		$retrieved_values['avatar'] = $avatar->item(0)->getAttribute('src');
	} else {
		$retrieved_values['avatar'] = '';
	}
	$video_num = $xPath->query('//*[@id="lpart1"]/div/div[1]/div[1]/span/a');
	$retrieved_values['video_num'] = trim(get_xpath_result($video_num), ')(');

	echo json_encode($retrieved_values);

} else {
	$url = array();
	for ($i = 0; $i < $db->count(); $i++) {
		$url[] = Config::get('site_url').'/schedule.php?id='.$i;
	}
	
	$results = multiRequest($url);

	// update user table
	foreach ($results as $result) {
		$d = json_decode($result);
		DB::getInstance()->update('user', array('u_id', $d->{'id'}), array('u_name' => $d->{'user_name'}, 'u_avatar' => $d->{'avatar'}, 'u_videoNum' => $d->{'video_num'}));
		DB::getInstance()->insert('data', array('d_timestamp' => time(), 'u_id' => $d->{'id'}, 'd_sub' => str_replace(',', '', $d->{'sub'}), 'd_view' => str_replace(',', '', $d->{'view'})));
	}

	echo '成功';

}





function multiRequest($data, $options = array()) {
 
  // array of curl handles
  $curly = array();
  // data to be returned
  $result = array();
 
  // multi handle
  $mh = curl_multi_init();
 
  // loop through $data and create curl handles
  // then add them to the multi-handle
  foreach ($data as $id => $d) {
 
    $curly[$id] = curl_init();
 
    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($curly[$id], CURLOPT_URL,            $url);
    curl_setopt($curly[$id], CURLOPT_HEADER,         0);
    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
 
    // post?
    if (is_array($d)) {
      if (!empty($d['post'])) {
        curl_setopt($curly[$id], CURLOPT_POST,       1);
        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
      }
    }
 
    // extra options?
    if (!empty($options)) {
      curl_setopt_array($curly[$id], $options);
    }
 
    curl_multi_add_handle($mh, $curly[$id]);
  }
 
  // execute the handles
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while($running > 0);
 
 
  // get content and remove handles
  foreach($curly as $id => $c) {
    $result[$id] = curl_multi_getcontent($c);
    curl_multi_remove_handle($mh, $c);
  }
 
  // all done
  curl_multi_close($mh);
 
  return $result;
}