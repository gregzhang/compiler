<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/24
 * Time: 14:33
 */
if (!isset($_POST['w_info'])) {
    exit('no w_info');
}
$widget_info = $_POST['w_info'];
if (!isset($widget_info['theme'])) {
    exit('no theme');
}
if (!isset($widget_info['template'])) {
    exit('no template');
}
if (!isset($widget_info['widget'])) {
    exit('no widget');
}
if (!isset($widget_info['index'])) {
    exit('no index');
}
if (!isset($widget_info['type'])) {
    exit('no type');
}

//$data[];
//die;
require_once '../lib/compiler.class.php';

$theme_compiler = new compiler();
$res = $theme_compiler->delete_widget_info($widget_info['theme'], $widget_info['template'], $widget_info['widget'], $widget_info['index']);
var_dump($res);