<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/7
 * Time: 10:40
 */
require_once '../lib/compiler.class.php';
require '../lib/smarty/smarty-3.1.30/libs/Smarty.class.php';
$smarty = new Smarty;
$theme_compiler = new compiler();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/layer/layer.js"></script>
    <title>theme_compiler</title>
</head>
<body>
<button id="main_btn">点我</button>
<script>
    $(function ($) {
        $('#main_btn').on({
            'click':function (e) {
                layer.open({
                    type: 1,
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: '<?php echo $theme_compiler->edit_widget_html('default', 'index.html', 'w_4', 0);?>'
                });
                console.log(e);
            }
        });
    });
</script>
</body>
</html>
