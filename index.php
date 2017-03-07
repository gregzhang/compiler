<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/24
 * Time: 15:48
 */
require_once 'lib/compiler.class.php';
require 'lib/smarty/smarty-3.1.30/libs/Smarty.class.php';
$smarty = new Smarty;
$theme_compiler = new compiler();
$theme_compiler->use_theme('theme1');

//$data[] = array('type'=>'logo','data'=>array('name'=>'Greg'));
//$theme_compiler->update_widget_info('default','index.html','w_4',$data);
//$r=$theme_compiler->build_preview('default','index.html');
//var_dump($r);

echo $theme_compiler->edit_widget_html('default', 'index.html', 'w_4', 0);

echo $theme_compiler->delete_widget_html('default', 'index.html', 'w_4', 0);

echo $theme_compiler->add_widget_html('default', 'index.html', 'w_4', 0, 'logo');

$theme_compiler->build_preview('default', 'index.html',$smarty);

$theme_compiler->build_push('default', 'index.html',$smarty);
