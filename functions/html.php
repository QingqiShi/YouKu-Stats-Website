<?php

function head($param) {
    echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
';

    foreach ($param as $type => $list) {
        foreach ($list as $file) {
            switch ($type) {
                case 'css':
                    echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"" . Config::get('site_url') . "{$file}\">\n";
                    break;

                case 'js':
                    echo "    <script type=\"text/javascript\" src=\"" . Config::get('site_url') . "{$file}\"></script>\n";
                    break;
                
                default:
                    break;
            }
        }
    }

    echo '</head>
<body>
';
}

function foot() {
    echo '</body>
</html>
';
}

function userMeta($id, $name, $url, $avatar, $video_num) {
          ?><div class="avatar_container">
                <img class="avatar" src="<?php echo $avatar; ?>" alt="<?php echo $name; ?>">
            </div>
            <div class="meta_info">
                <div class="meta_user_name">
                    <a href="<?php echo $url; ?>" target="_blank">
                        <span><?php echo $name; ?></span>
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAWklEQVR42mNgGMxgKh5MlGZBAvJEGaCCxXZBfAagKwQBb2INgEkKohlQQooBDGgGuEPZJUguIckAFaghDEjeYSDFAJJiAVlQkJx0gC9uBYmNe4oMoDj5DgwAAEf5JaU8++UEAAAAAElFTkSuQmCC" alt="">
                    </a>
                </div>
                <div class="meta_user_meta">
                    <span>视频数：<?php echo $video_num; ?></span>
                    <span>&nbsp;&nbsp;•&nbsp; </span>
                    <span>总观看次数：<?php

                        $info = DB::getInstance()->query('SELECT d_view, d_sub FROM data WHERE u_id = ' . $id . ' ORDER BY d_timestamp DESC LIMIT 1');
                        echo $info->results(0, 'd_view');

                    ?></span>
                    <span>&nbsp;&nbsp;•&nbsp; </span>
                    <span>粉丝数：<?php echo $info->results(0, 'd_sub'); ?></span>
                </div>
            </div>



            <?php
}

function dataRange($range, $id) {

    $exploded_range = explode('-', $range);

    $start_date = strtotime($exploded_range[0]);
    $end_date = strtotime($exploded_range[1]);

?>
            <div class="ui item">
                <div class="ui selection dropdown">
                    <div class="text"><?php echo $range; ?></div>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <a href="<?php 

                        rangeLink((DB::getInstance()->query('SELECT * FROM data WHERE `u_ID` = ' . $id . ' ORDER BY `d_timestamp` LIMIT 1')->results(0, 'd_timestamp')), time()); ?>">
                            <div class="item">存在期间</div>
                        </a>

                        <div class="ui fitted divider"></div>

                        <a href="<?php rangeLink(mktime(0,0,0,date("n"),date("j")-date("N")), time()); ?>">
                            <div class="item">本周</div>
                        </a>
                        <a href="<?php rangeLink(mktime(0,0,0,date("n"),date("j")-date("N")-7), mktime(0,0,0,date("n"),date("j")-date("N")-1)); ?>">
                            <div class="item">上周</div>
                        </a>
                        <a href="<?php rangeLink(time()-(6*24*60*60), time()) ?>">
                            <div class="item">过去7天</div>
                        </a>

                        <div class="ui fitted divider"></div>

                        <a href="<?php rangeLink(mktime(0,0,0,date("n"),1), time()) ?>">
                            <div class="item">本月</div>
                        </a>
                        <a href="<?php rangeLink(mktime(0,0,0,date("n")-1,1), mktime(0,0,0,date("n"),1-1)) ?>">
                            <div class="item">上月</div>
                        </a>
                        <a href="<?php rangeLink(time()-(28*24*60*60), time()) ?>">
                            <div class="item">过去28天</div>
                        </a>
                        <a href="<?php rangeLink(time()-(30*24*60*60), time()) ?>">
                            <div class="item">过去30天</div>
                        </a>

                        <div class="ui fitted divider"></div>

                        <a href="<?php rangeLink(mktime(0,0,0,1,1), time()) ?>">
                            <div class="item">今年</div>
                        </a>
                        <a href="<?php rangeLink(mktime(0,0,0,1,1,date('Y')-1), mktime(0,0,0,1,1-1)) ?>">
                            <div class="item">去年</div>
                        </a>
                         <ahref="<?php rangeLink(time()-(365*24*60*60), time()) ?>">
                            <div class="item">过去365天</div>
                        </a>
                    </div>
                </div>
            </div>

            <script>
                $('.ui.dropdown').dropdown();
            </script>
<?php
}

function rangeLink($date_one, $date_two) {
    echo generate_self_url(array(
        'range' => (date('Ymd', $date_one) . '-' . date('Ymd', $date_two))
    ));
}

function dataType($data_type, $id, $range) {

    $sql = '(SELECT d_view, d_sub FROM data WHERE u_ID = ' . $id . ' AND d_timestamp > ' . (strtotime(explode('-', $range)[0]) + (24*60*60)) . ' AND d_timestamp < ' . strtotime(explode('-', $range)[1]) . ' ORDER BY d_timestamp LIMIT 1) UNION ALL (SELECT d_view, d_sub FROM data WHERE u_ID = ' . $id . ' AND d_timestamp > ' . (strtotime(explode('-', $range)[0]) + (24*60*60)) . ' AND d_timestamp < ' . strtotime(explode('-', $range)[1]) . ' ORDER BY d_timestamp DESC LIMIT 1)';

?>
            <ul>
                <li>
                    <a href="<?php 

                    echo generate_self_url(array(
                        'data_type' => 'view'
                    ));

                    ?>"<?php

                        if ($data_type == 'view') {
                            echo ' class="selected"';
                        }

                    ?>>
                        <div class="data_type_container">
                            <div class="data_type_title">估计观看次数</div>
                            <div class="data_type_value"><?php 
                                $db = DB::getInstance()->query($sql);

                                echo number_format($db->results(1, 'd_view') - $db->results(0, 'd_sub'));
                            ?></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?php 

                    echo generate_self_url(array(
                        'data_type' => 'sub'
                    ));

                    ?>"<?php

                        if ($data_type == 'sub') {
                            echo ' class="selected"';
                        }

                    ?>>
                        <div class="data_type_container">
                            <div class="data_type_title">订阅人数</div>
                            <div class="data_type_value"><?php 
                                echo number_format($db->results(1, 'd_sub') - $db->results(0, 'd_sub'));
                            ?></div>
                        </div>
                    </a>
                </li>
            </ul>
<?php
}

function dataFrequency($frequency, $cumulate) {
?>

            <div class="ui form check">
                <div class="inline field">
                    <div class="ui selection dropdown">
                        <div class="text"><?php echo getFrequencyName($frequency); ?></div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <a href="<?php echo generate_self_url(array(
                                'frequency' => 'day'
                            )); ?>">
                                <div class="item"><?php echo getFrequencyName('day'); ?></div>
                            </a>
                            <a href="<?php echo generate_self_url(array(
                                'frequency' => 'week'
                            )); ?>">
                                <div class="item"><?php echo getFrequencyName('week'); ?></div>
                            </a>
                            <a href="<?php echo generate_self_url(array(
                                'frequency' => 'month'
                            )); ?>">
                                <div class="item"><?php echo getFrequencyName('month'); ?></div>
                            </a>
                        </div>
                    </div>

                    <div class="ui checkbox">
                            <input type="checkbox">
                            <label>累计</label>
                    </div>
                </div>
            </div>

            <script>
                $('.ui.dropdown').dropdown();

                $('.ui.checkbox').checkbox(<?php

                if ($cumulate == 'true') {
                    echo '\'enable\'';
                } else {
                    echo '\'disable\'';
                }

                ?>);
                

                $('.ui.checkbox').checkbox({

                    onDisable: function() {
                        window.location = "<?php

                        echo generate_self_url(array(
                            'cumulate' => 'false'
                        ));

                        ?>";
                    },

                    onEnable: function() {
                        window.location = "<?php

                        echo generate_self_url(array(
                            'cumulate' => 'true'
                        ));

                        ?>";
                    }
                    
                });
            </script>

<?php
}

function getFrequencyName($frequency) {
    switch ($frequency) {
        case 'day':
            return '每日';
            break;

        case 'day7':
            return '每日（总共7天）';
            break;

        case 'day28':
            return '每日（总共28天）';
            break;

        case 'day30':
            return '每日（总共30天）';
            break;

        case 'week':
            return '每周';
            break;

        case 'month':
            return '每月';
            break;
        
        default:
            return '每日';
            break;
    }
}

function chartSettings($data_name) {
?>
    $(function() {
        Highcharts.setOptions({
            global: {
                timezoneOffset: -8 * 60
            },
            lang: {
                loading: '加载中...',
                months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                weekdays: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
                shortMonths: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']
            }
        });

        $('#dataChart').highcharts('StockChart', {

            chart: {
                pinchType: ""
            },
            rangeSelector : {
                enabled: false,
                selected: 1
            },
            plotOptions: {
                line: {
                    marker: {
                        enabled: true,
                        radius: 2.5
                    }
                }
            },
            scrollbar: {
                enabled: false
            },
            navigator: {
                enabled: false
            },
            legend : {
                enabled: true,
                verticalAlign: "top",
                align: 'left',
                floating: true,
                x: 30
            },
            yAxis: {
                opposite: false,
                min: 0
            },
            xAxis: {
                dateTimeLabelFormats: {
                    millisecond: '%H:%M:%S.%L',
                    second: '%H:%M:%S',
                    minute: '%H:%M',
                    hour: '%H:%M',
                    day: '%b月%e日',
                    week: '%b月%e日',
                    month: '%y年%B',
                    year: '%Y'
                }
            },
            tooltip: {
                formatter: function() {

                    var date = new Date(this.x);
                    var s = '<b>' + Highcharts.dateFormat('%Y-%b-%e', this.x) + '</b>';
                    
                    $.each(this.points, function(i, point) {
                        s += '<br/>'+ point.series.name +': <b>'+
                            point.y + '</b>';
                    });
            
                    return s;
                },
                shared: true
            },
            credits: {
                enabled: false
            },
            series : [{
                name : '<?php echo $data_name ?>',
                data : data,
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });
<?php
}

function dataSet($id, $frequency, $range, $data_type, $cumulate) {

    $data = new Data($range, $frequency);

    $data->input_data($id, $data_type);

    echo $data->get_data($cumulate);

    // $start_date = strtotime(explode('-', $range)[0]);
    // $end_date = strtotime(explode('-', $range)[1]);

    // $data_list = array();

    // for

    // $data = DB::getInstance()->query('SELECT * FROM data WHERE u_id = ' . $id . ' AND d_timestamp > ' . $start_date. ' AND d_timestamp < ' . ($end_date + (2*24*60*60)));

    // $data_str = "";
    // $prev_date = "0";
    // $counter = 0;
    // $data_name = ($data_type == 'sub' ? 'd_sub' : 'd_view');

    // $prev_date = $data->results(0, 'd_timestamp');
    // $prev_value = $data->results(0, $data_name);

    // for($counter = 1; $counter < $data->count(); $counter++) {
    //     if (strcmp(freq_calc($frequency, $data->results($counter, 'd_timestamp')), freq_calc($frequency, $prev_date)) !== 0) {
    //         break;
    //     }
    // }

    // $data_array = array();

    // for ($i = $counter; $i < $data->count(); $i++) {
    //     if (strcmp(freq_calc($frequency, $data->results($i, 'd_timestamp')), freq_calc($frequency, $prev_date)) !== 0) {
    //         $timestamp = $data->results($i, 'd_timestamp') - (24*60*60);
    //         if ($cumulate == 'true') {
    //             $value = $data->results($i, $data_name);
    //         } else {
    //             $value = (int)($data->results($i, $data_name)) - (int)($prev_value);
    //         }
    //         $data_array[$timestamp] = $value;
    //         $prev_date = $data->results($i, 'd_timestamp');
    //         $prev_value = $data->results($i, $data_name);
    //     }
    // };

    // return rtrim($data_str, ",");
}

function freq_calc($frequency, $timestamp) {
    switch ($frequency) {
        case 'day':
            $freq_letter = 'j';
            break;
        case 'week':
            $freq_letter = 'N';
            break;
        case 'month':
            $freq_letter = 'n';
            break;
        default:
            $freq_letter = 'j';
            break;
    }

    return date($freq_letter, $timestamp);
}

function data_correction($data, $prev, $next, $cumulate) {
    if (($cumulate == 'true' && $data == $prev) || ($cumulate != 'true' && (int)$data == 0)) {
        return 'error';
    } else {
        return $data;
    }
}
