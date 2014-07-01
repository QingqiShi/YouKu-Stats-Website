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

    <div class="pull_form float_card">
        <form action="data.php" method="get">
            <select name="name" class="name">

<?php
    $userList = DB::getInstance()->query("SELECT u_name FROM user ORDER BY u_ID");
    for ($i=0; $i < $userList->count(); $i++) { 
        $user = $userList->results($i, 'u_name');
        echo "                <option value=\"{$user}\">{$user}</option>\n";
    }
?>

            </select>
            <input type="hidden" name="frequency" value="day">
            <input type="hidden" name="range" value="<?php echo date('Ymd', time()-(28*24*60*60)).'-'.date('Ymd'); ?>">
            <input type="hidden" name="data_type" value="view">
            <input type="hidden" name="cumulate" value="false">
            <input type="submit" value="拉取数据">
        </form>
    </div>
<?php
    foot();