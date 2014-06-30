<?php
require_once 'core/init.php';

date_default_timezone_set('Asia/Shanghai');

if (Input::exists('get')) {

    $validator = new Validate();
    $validator->check($_GET, array(
        'name' => array(
            'name' => '用户名',
            'required' => true
            ),
        'frequency' => array(
            'name' => '频率',
            'required' => true
            ),
        'range' => array(
            'name' => '日期范围',
            'required' => true,
            'format' => '/^[0-9]{8}-[0-9]{8}$/'
            ),
        'data_type' => array(
            'name' => '数据种类',
            'required' => true,
            'values' => array('view', 'sub')
            ),
        'cumulate' => array(
            'name' => '累计'
            )
        ));

    if (!$validator->passed()) {
        echo '
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
</head>
<body>';
        print_r($validator->errors());
        echo '
</body>
</html>';

    } else {

        head(array(
            'css' => array(
                '/css/global.css', 
                '/css/data_style.css', 
                '/libs/Semantic-UI/build/minified/elements/icon.min.css', 
                '/libs/Semantic-UI/build/minified/modules/dropdown.min.css', 
                '/libs/Semantic-UI/build/minified/modules/checkbox.min.css'
            ),
            'js' => array(
                '/libs/jquery/dist/jquery.min.js', 
                '/libs/highstock-release/highstock.js', 
                '/libs/Semantic-UI/build/minified/modules/dropdown.min.js', 
                '/libs/Semantic-UI/build/minified/modules/checkbox.min.js'
            )
            ));  ?>


<div id="wrap" class="clearfix">
    <div class="content float_card">
        <div class="back">
            <a href="<?php echo Config::get('site_url'); ?>">返回首页</a>
        </div>

        <div class="user_meta">
            <?php 

            $db = DB::getInstance()->get('user', array('u_name', '=', Input::get('name')));
            $current_user = array(
                'id'          => $db->results(0, 'u_id'),
                'name'        => $db->results(0, 'u_name'),
                'url'         => $db->results(0, 'u_url'),
                'avatar'      => $db->results(0, 'u_avatar'),
                'videoNum'    => $db->results(0, 'u_videoNum')
            );
            userMeta($current_user['id'], $current_user['name'], $current_user['url'], $current_user['avatar'], $current_user['videoNum']);

            ?>
        </div>

        <div class="data_range">
            <?php dataRange(Input::get('range')); ?>
        </div>

        <div class="data_type">
            <?php dataType(Input::get('data_type')); ?>
        </div>

        <div class="data_frequency">
            <?php dataFrequency(Input::get('frequency'), Input::get('cumulate')); ?>
        </div>

        <div class="chart">
            <script type="text/javascript">
            var data = [<?php 

            echo dataSet($current_user['id'], Input::get('frequency'), Input::get('range'), Input::get('data_type'), Input::get('cumulate'));

            ?>];

            <?php

                if (Input::get('data_type') == 'sub') {
                    $data_name = '订阅人数';
                } else {
                    $data_name = '估算观看次数';
                }
                chartSettings($data_name);
            ?>

            </script>
            <div id="dataChart" style="height: 340px; min-width: 310px"></div>
        </div>
    </div>
</div>


<?php
        foot();

    }
}