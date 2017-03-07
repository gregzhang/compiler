<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/1/23
 * Time: 上午1:29
 */
function compiler_exclude_row($arr = array()) {

    foreach ($arr as $key => $value) {


        if (is_array($value) && !empty($value)) {

            if ("row" === $key) {

                foreach ($value as $k => $val) {
                    if (is_array($val)) {
                        if (empty($val)) {
                            $arr[$k] = null;
                        } else {
                            $arr[$k] = compiler_exclude_row($val);
                        }
                    } else {
                        $arr[$k] = $val;
                    }
                }
                unset($arr[$key]);
            } else {

                if (isset($arr[$key]['row'][0]) || !isset($arr[$key]['row'])) {
                    $arr[$key] = compiler_exclude_row($value);
                    unset($arr[$key]['row']);
                } else {
                    $arr[$key][] = compiler_exclude_row($value);
                    unset($arr[$key]['row']);
                }

            }
        } else {

            if ("row" === $key) {
                $arr[] = $value;
                unset($arr[$key]);
            } else {
                if (empty($value)) {
                    $arr[$key] = null;
                }
            }
        }
    }
    return $arr;
}

/**
 * 随机数字生成
 * @param $lens
 * @return string
 */
function compiler_get_random($lens) {

    $chars = array(
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
    );

    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = "";

    for ($i = 0; $i < $lens; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

function compiler_arr_to_xml($arr = array(), $root = '', $node = null) {
    if ($node === null) {
        $simxml = new simpleXMLElement("<?xml version='1.0' encoding='utf-8'?><$root></$root>");
    } else {
        $simxml = $node;
    }
    // simpleXMLElement对象如何增加子节点?

    foreach ($arr as $k => $v) {
        if (is_array($v) && !is_numeric($k)) {
            //$simxml->addChild($k);
            compiler_arr_to_xml($v, $root, $simxml->addChild($k));
        } else if (is_numeric($k)) { //标签不能以数字开头，和变量类似
            if (is_array($v)) {
                compiler_arr_to_xml($v, $root, $simxml->addChild('row'));
            } else {
                $simxml->addChild('row', $v);
            }


        } else {
            $simxml->addChild($k, $v);
        }
    }
    return $simxml->saveXML();
}

function read_xml($file_path) {
    if (!is_file($file_path)) {
        return false;
    }
    $myfile = fopen($file_path, "r");
    if (!$myfile) {
        return false;
    }
    $content = fread($myfile, filesize($file_path));
    fclose($myfile);

    $xml = simplexml_load_string($content);
    $xml = json_decode(json_encode((array)$xml), true);
    $content = compiler_exclude_row($xml);

    return $content;
}

function compiler_write_xml($file_path, $data = array()) {
    $root = pathinfo($file_path)['filename'];
    $content = compiler_arr_to_xml($data, $root);
    $myfile = fopen($file_path, "w");
    if (!$myfile) {
        return false;
    }
    fwrite($myfile, $content);
    fclose($myfile);
    return true;
}

function compiler_read_file($file_path) {
    if (!is_file($file_path)) {
        return false;
    }
    $myfile = fopen($file_path, "r");
    if (!$myfile) {
        return false;
    }
    $content = fread($myfile, filesize($file_path));
    fclose($myfile);
    return $content;

}

function compiler_get_path_names($dir_path, $type = 'all') {
    $files = array();
    if (is_dir($dir_path)) {
        if ($dh = opendir($dir_path)) {
            while (($file = readdir($dh)) !== false) {

                if ('..' !== $file && '.' !== $file) {
                    $file_t ['name'] = $file;
                    $file_t ['path'] = $dir_path . $file;
                    if (is_file($dir_path . $file)) {

                        if ('all' == $type) $file_t ['type'] = 'file';
                        $files['file'][$file] = $file_t;
                    }

                    if (is_dir($dir_path . $file)) {
                        if ('all' == $type) $file_t ['type'] = 'dir';
                        $files['dir'][$file] = $file_t;
                    }

                    $files['all'][$file] = $file_t;
                }
            }
            closedir($dh);
        }
    }
    switch ($type) {
        case 'dir':
            return isset($files['dir']) ? $files['dir'] : false;
        case 'file':
            return isset($files['file']) ? $files['file'] : false;
        case 'all':
            return isset($files['all']) ? $files['all'] : false;
    }
}