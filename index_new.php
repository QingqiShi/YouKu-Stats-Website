<?php
require_once 'core/init.php';

date_default_timezone_set('Asia/Shanghai');

head(array(
    'css' => array(
        '/css/global.css', 
        '/css/index_style.css', 
        '/libs/Semantic-UI/build/minified/elements/icon.min.css', 
        '/libs/Semantic-UI/build/minified/modules/dropdown.min.css'
    ),
    'js' => array(
        '/libs/jquery/dist/jquery.min.js',
        '/libs/Semantic-UI/build/minified/modules/dropdown.min.js'
    )
    ));
?>

    <div class="title largeMargin">
        优酷数据统计
    </div>
    <div class="pull">
        <form action="process_new.php" method="get">
            <select name="name" class="name">

<?php
    $userList = DB::getInstance()->query("SELECT u_name FROM user ORDER BY u_ID");
    foreach ($userList->results() as $user) {
        echo "                <option value=\"{$user->u_name}\">{$user->u_name}</option>\n";
    }
?>

            </select>
            <input type="hidden" name="frequency" value="daily">
            <input type="hidden" name="range" value="lastmonth">
            <input type="hidden" name="data_type" value="2">
            <input type="submit" value="拉取数据">
        </form>
    </div>
<?php
    foot();