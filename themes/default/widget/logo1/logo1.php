<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/1/23
 * Time: 上午12:15
 */
function logo1_set()
{
    return array('a' => 'greg', 'b' => 'logotype');
}

function logo1_show($set_data)
{
    return array('name' => $set_data['a'], 'title' => $set_data['b'] . '1000');
}