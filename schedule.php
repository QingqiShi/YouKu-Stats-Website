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

$retrieved_values = array(
	'id' => '',
	'user_name' => '',
	'view' => '',
	'sub' => '',
	'avatar' => '',
	'video_num' => ''
);

$data_array = array();

$db = DB::getInstance()->query('SELECT * FROM user');

for ($i = 0; $i < $db->count(); $i++) { 
	$retrieved_values['id'] = $db->results($i, 'u_id');


	$url_content = file_get_contents($db->results($i, 'u_url'));

	$dom = new DOMDocument();
	@$dom->loadHTML($url_content);
	$xPath = new DOMXpath($dom);

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

	$data_array[] = $retrieved_values;
}

// update user table
foreach ($data_array as $data) {
	DB::getInstance()->update('user', array('u_id', $data['id']), array('u_name' => $data['user_name'], 'u_avatar' => $data['avatar'], 'u_videoNum' => $data['video_num']));
	DB::getInstance()->insert('data', array('d_timestamp' => time(), 'u_id' => $data['id'], 'd_sub' => $data['sub'], 'd_view' => $data['view']));
}

echo '成功';
