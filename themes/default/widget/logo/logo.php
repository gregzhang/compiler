<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/1/23
 * Time: 上午12:15
 */
function widget_fun_logo_set($input_data) {
    return array('name' => widget_fun_logo_info($input_data['name']) . 'fix');
}

function widget_fun_logo_show($set_data) {
    return array('name' => $set_data['name'], 'title' => 'title' . $set_data['name']);
}

function widget_fun_logo_info($data) {
    $data= $data.'-';
    return $data;
}