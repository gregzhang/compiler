<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/24
 * Time: 10:47
 */
define('COM_DS', DIRECTORY_SEPARATOR);
define('COMPILER_PATH', dirname(dirname(__FILE__)) . COM_DS);

require_once(COMPILER_PATH . COM_DS . 'lib' . COM_DS . 'compiler.dom.class.php');
require_once(COMPILER_PATH . COM_DS . 'lib' . COM_DS . 'compiler.func.php');


class compiler {

    private $theme;
//    private $smarty;

    function __construct() {

        $config = require_once(COMPILER_PATH . COM_DS . 'lib' . COM_DS . 'compiler.config.php');
        $this->theme['themes_path'] = isset($config ['THEMES_PATH']) ? $config ['THEMES_PATH'] : COMPILER_PATH . 'themes' . COM_DS;
        $this->theme['preview_path'] = isset($config ['PREVIEW_PATH']) ? $config ['PREVIEW_PATH'] : COMPILER_PATH . 'preview'
            . COM_DS;
        $this->theme['push_path'] = isset($config ['PUSH_PATH']) ? $config ['PUSH_PATH'] : COMPILER_PATH . 'push'
            . COM_DS;
        $this->theme['edit_url'] = isset($config ['EDIT_URL']) ? $config ['EDIT_URL'] : '/api/edit.php'
            . COM_DS;
        $this->theme['delete_url'] = isset($config ['DELETE_URL']) ? $config ['DELETE_URL'] : '/api/delete.php'
            . COM_DS;
        $this->theme['add_url'] = isset($config ['ADD_URL']) ? $config ['ADD_URL'] : '/api/add.php'
            . COM_DS;
        $this->theme['list'] = $this->get_theme_list();
//        $this->smarty = $smarty_obj;
//        if ($this->smarty) {
//            $this->set_smarty_config();
//        }
    }


    function set_smarty_config(Smarty $smarty) {
        $smarty->setLeftDelimiter('<{');
        $smarty->setRightDelimiter('}>');
        $smarty->setTemplateDir($this->theme['themes_path']);
        $smarty->setCompileDir($this->theme['preview_path'] . 'cache' . COM_DS . 'smarty' . COM_DS . 'c');
        $smarty->setCacheDir($this->theme['preview_path'] . 'cache' . COM_DS . 'smarty');
//        $smarty->setPluginsDir($this->smarty_path.'/demo/plugins');
//        $smarty->addPluginsDir($this->smarty_path.'/libs/plugins');
        $smarty->debugging = false;
        $smarty->caching = false;
        $smarty->cache_lifetime = 120;
        return $smarty;
    }

    function add_widget_html($theme_name, $template_name, $widget_id, $widget_index, $widget_type) {
        if (!$this->check_widget_place($theme_name, $template_name, $widget_id)) {
            return false;
        }
        $widget_set_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'widget' . COM_DS .
            $widget_type . COM_DS . 'set.html';
        $edit_html = new simple_html_dom($widget_set_path);
        $form = $edit_html->find('form', 0);
        $form->action = $this->theme['add_url'];
        $form->method = 'post';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[theme]" value="' . $theme_name . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[template]" value="' . $template_name . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[widget]" value="' . $widget_id . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[index]" value="' . $widget_index . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[type]" value="' . $widget_type . '">';

        $str = $edit_html->save();
        return $str;
    }

    function delete_widget_html($theme_name, $template_name, $widget_id, $widget_index) {
        $themes_info = $this->read_themes_xml();
        if (!$this->check_widget_place($theme_name, $template_name, $widget_id)) {
            return false;
        }
        $widget_info = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget']
        [$widget_id][$widget_index];
        $edit_html = new simple_html_dom('<form><input type="submit" value="delete"></form>');
        $form = $edit_html->find('form', 0);
        $form->action = $this->theme['delete_url'];
        $form->method = 'post';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[theme]" value="' . $theme_name . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[template]" value="' . $template_name . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[widget]" value="' . $widget_id . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[index]" value="' . $widget_index . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[type]" value="' . $widget_info['type'] . '">';
        $str = $edit_html->save();
        return $str;
    }

    function edit_widget_html($theme_name, $template_name, $widget_id, $widget_index) {
        $themes_info = $this->read_themes_xml();
        if (!$this->check_widget_place($theme_name, $template_name, $widget_id)) {
            return false;
        }
        $widget_info = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget']
        [$widget_id][$widget_index];
        $widget_set_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'widget' . COM_DS .
            $widget_info['type'] . COM_DS . 'set.html';
        $edit_html = new simple_html_dom($widget_set_path);
        $form = $edit_html->find('form', 0);
        $form->action = $this->theme['edit_url'];
        $form->method = 'post';
        $form_data = $widget_info['data'];
        foreach ($form_data as $key => $value) {
            $input = $form->find('[name="' . $key . '"]', 0);
            $input->value = $value;
        }
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[theme]" value="' . $theme_name . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[template]" value="' . $template_name . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[widget]" value="' . $widget_id . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[index]" value="' . $widget_index . '">';
        $form->innertext = $form->innertext . '<input type="hidden" name="w_info[type]" value="' . $widget_info['type'] . '">';


        $str = $edit_html->save();
//        echo $str;
        return $str;


    }

    function check_widget_place($theme_name, $template_name, $widget_id) {
        if (!$this->check_template($theme_name, $template_name)) {
            return false;
        }
        $template_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'template' . COM_DS . $template_name;
        if (!is_file($template_path)) {
            return false;
        }

        $html = new simple_html_dom($template_path);
        $w_div = $html->getElementById($widget_id)->outertext;
        if (!$w_div) {
            return false;
        }
        return true;
    }

    function build_push($theme_name, $template_name,Smarty $smarty) {
        $smarty = $this->set_smarty_config($smarty);
        $themes_info = $this->read_themes_xml();

        if (!$this->check_template($theme_name, $template_name)) {
            return false;
        }

        $widget_info = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget'];
        $template_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'template' . COM_DS . $template_name;
        $html = new simple_html_dom();
        $html->load_file($template_path);
        foreach ($widget_info as $w_id => $widget) {
            if ('none' !== $widget) {
                $w_content = '';
                $w_div = $html->getElementById($w_id);
                foreach ($widget as $item) {
                    $widget_func_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'widget' . COM_DS .
                        $item['type'] . COM_DS . $item['type'] . '.php';
                    $widget_show_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'widget' . COM_DS .
                        $item['type'] . COM_DS . 'show.html';
                    if (!is_file($widget_func_path)) {
                        continue;
                    }
                    if (!is_file($widget_show_path)) {
                        continue;
                    }

                    include_once $widget_func_path;
                    $set_func_name = 'widget_fun_' . $item['type'] . '_set';
                    $set_data = $set_func_name($item['data']);
                    $show_func_name = 'widget_fun_' . $item['type'] . '_show';
                    $show_data = $show_func_name($set_data);
                    foreach ($show_data as $key => $value) {
                        $smarty->assign($key, $value);
                    }
                    $w_content .= $smarty->fetch($widget_show_path);//$theme_name .'/widget/'.$item['type'].'/show.html');

                }
                $w_div->innertext = $w_content;
                continue;
            }


        }

        $html->save($this->theme['push_path'] . $template_name);
        return true;
    }


    function build_preview($theme_name, $template_name,Smarty $smarty) {
        $smarty = $this->set_smarty_config($smarty);
        $themes_info = $this->read_themes_xml();

        if (!$this->check_template($theme_name, $template_name)) {
            return false;
        }

        $widget_info = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget'];
        $template_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'template' . COM_DS . $template_name;
        $html = new simple_html_dom();
        $html->load_file($template_path);
        foreach ($widget_info as $w_id => $widget) {
            if ('none' !== $widget) {
                $w_content = '';
                $w_div = $html->getElementById($w_id);
                foreach ($widget as $item) {
                    $widget_func_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'widget' . COM_DS .
                        $item['type'] . COM_DS . $item['type'] . '.php';
                    $widget_show_path = $this->theme['themes_path'] . $theme_name . COM_DS . 'widget' . COM_DS .
                        $item['type'] . COM_DS . 'show.html';
                    if (!is_file($widget_func_path)) {
                        continue;
                    }
                    if (!is_file($widget_show_path)) {
                        continue;
                    }

                    include_once $widget_func_path;
                    $set_func_name = 'widget_fun_' . $item['type'] . '_set';
                    $set_data = $set_func_name($item['data']);
                    $show_func_name = 'widget_fun_' . $item['type'] . '_show';
                    $show_data = $show_func_name($set_data);
                    foreach ($show_data as $key => $value) {
                        $smarty->assign($key, $value);
                    }
                    $w_content .= $smarty->fetch($widget_show_path);//$theme_name .'/widget/'.$item['type'].'/show.html');

                }
                $w_div->innertext = $w_content;
                continue;
            }


        }

        $html->save($this->theme['preview_path'] . $template_name);
        return true;
    }

    /**
     * 检查template可用性
     * @param $theme_name
     * @param $template_name
     * @return bool
     */
    function check_template($theme_name, $template_name) {
        if (!$this->check_theme($theme_name)) {
            return false;
        }

        $template_list = $this->get_template_list($theme_name);
        if (!isset($template_list[$template_name])) {
            return false;
        }

        if (!is_file($template_list[$template_name]['path'])) {
            return false;
        }

        return true;
    }

    /**
     * 获取主题
     * @return bool|mixed
     */
    function get_theme_list() {
        $theme_dir = $this->theme['themes_path'];

        $theme_list = compiler_get_path_names($theme_dir, 'dir');
        foreach ($theme_list as $theme) {

            if (!$this->check_theme($theme['name'])) {
                unset($theme_list[$theme['name']]);
            }
        }
        return $theme_list;
    }

    /**
     * 设置主题
     */
    function use_theme($theme_name) {
        $this->update_themes_xml($theme_name);
    }

    function add_widget_info($theme_name, $template_name, $widget_id, $data, $index) {
        $themes_info = $this->read_themes_xml();
        $widget_data_old = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget'][$widget_id];
        if (isset($widget_data_old[$index])) {
            var_dump($widget_data_old);
            array_splice($widget_data_old, $index, 0, array($data));
            var_dump($widget_data_old);
        }
        $data ['theme'] = $theme_name;
        $data ['template'] = $template_name;
        $data ['w_id'] = $widget_id;
        $data ['data'] = $widget_data_old;
        return $this->update_themes_xml(array('widget' => $data));
    }

    function delete_widget_info($theme_name, $template_name, $widget_id, $index) {
        $themes_info = $this->read_themes_xml();
        $widget_data_old = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget'][$widget_id];
        if (isset($widget_data_old[$index])) {
            array_splice($widget_data_old, $index, 1);
        }
        $data ['theme'] = $theme_name;
        $data ['template'] = $template_name;
        $data ['w_id'] = $widget_id;
        $data ['data'] = $widget_data_old;
        return $this->update_themes_xml(array('widget' => $data));
    }

    function update_widget_info($theme_name, $template_name, $widget_id, $widget_data = array(), $index) {
        $themes_info = $this->read_themes_xml();
        $widget_data_old = $themes_info['theme_list'][$theme_name]['template_list'][$template_name]['widget'][$widget_id];
        $widget_data_old[$index] = $widget_data;
        $data ['theme'] = $theme_name;
        $data ['template'] = $template_name;
        $data ['w_id'] = $widget_id;
        $data ['data'] = $widget_data_old;

        return $this->update_themes_xml(array('widget' => $data));

    }


    /**
     * 获取当前主题的模板
     * @param $theme_name
     * @return bool|mixed
     */
    function get_template_list($theme_name) {
        $theme_list = $this->theme['list'];
        if (!isset($theme_list[$theme_name])) {
            return false;
        }
        $theme = $theme_list[$theme_name];
        $templates_path = $theme['path'] . COM_DS . 'template' . COM_DS;
        return compiler_get_path_names($templates_path, 'file');
    }

    /**
     * 获取当前主题的挂件列表
     * @param string $theme_name
     * @return bool|mixed
     */
    function get_widget_list($theme_name = '') {
        $theme_list = $this->theme['list'];
        if (!isset($theme_list[$theme_name])) {
            return false;
        }
        $theme = $theme_list[$theme_name];
        $widget_path = $theme['path'] . COM_DS . 'widget' . COM_DS;

        $widget_list = compiler_get_path_names($widget_path, 'dir');
        //todo:过滤不可用挂件
        return $widget_list;

    }

    /**
     * 获取当前主题的信息
     * @param $theme_name
     * @return mixed
     */
    function get_theme_info($theme_name) {
        $data['template'] = $this->get_template_list($theme_name);
        $data['widget'] = $this->get_widget_list($theme_name);
        return $data;
    }

    /**
     * 获取模板的信息
     * @param $theme_name
     * @param $template_name
     * @return array|bool
     */
    function get_template_widget_info($theme_name, $template_name) {
        $theme = $this->get_theme_info($theme_name);

        if (!isset($theme['template'][$template_name])) {
            return false;
        }

        $template_path = $theme['template'][$template_name]['path'];
        if (!is_file($template_path)) {
            return false;
        }

        $html = new simple_html_dom();
        $html->load_file($template_path);
        $widgets = $html->find('div[class="widget"]');
        $data = array();
        foreach ($widgets as $widget) {
            $data[$widget->id] = 'none';
        }

        return $data;
    }

    /**
     * 检查主题的可用性
     * @param $theme_name
     * @return bool
     */
    function check_theme($theme_name) {

        $theme_path = $this->theme['themes_path'] . $theme_name . COM_DS;
        if (!is_dir($theme_path)) {
            return false;
        }

        $templates_path = $theme_path . 'template' . COM_DS;
        if (!is_dir($templates_path)) {
            return false;
        }

        $template_index = $templates_path . 'index.html';
        if (!is_file($template_index)) {
            return false;
        }
        return true;
    }

    private function read_themes_xml() {

        $themes_xml_path = $this->theme['themes_path'] . 'themes.xml';
        if (!is_file($themes_xml_path)) {
            $this->update_themes_xml();
        }
        $themes_info = read_xml($themes_xml_path);


        return $themes_info;
    }

    /**
     * 更新主题数据
     * @param string $theme_name
     * @return bool
     */
    private function update_themes_xml($update_info = array()) {
        if (!empty($update_info)) {

            if (isset($update_info['theme'])) {
                $update_data ['theme_use'] = $update_info['theme'];
            }

            if (isset($update_info['widget']['theme'])
                && isset($update_info['widget']['template'])
                && isset($update_info['widget']['w_id'])
                && isset($update_info['widget']['data'])
            ) {
                $update_data ['widget'] = $update_info['widget'];

            }
        }

        $themes_xml_path = $this->theme['themes_path'] . 'themes.xml';
        if (is_file($themes_xml_path)) {
            $xml_data = read_xml($themes_xml_path);
            $update = false;
            foreach ($xml_data['theme_list'] as $k1 => $theme) {
                $theme_name = $theme['theme'];
                $theme['template_list'];
                $list_template = $this->get_template_list($theme_name);
                //fixme:跟新逻辑错误;
                foreach ($theme['template_list'] as $k2 => $template) {
                    $template_name = $template['page'];
                    if ($template['md5'] != md5_file($list_template[$template_name]['path'])) {
                        $xml_data['theme_list'][$k1]['template_list'][$k2]['md5']
                            = md5_file($list_template[$template_name]['path']);
                        $widget_info_new = $this->get_template_widget_info($theme_name, $template_name);
                        $widget_update = array();
                        foreach ($widget_info_new as $k3 => $widget_new) {
                            $widget_update[$k3] = isset($template['widget'][$k3]) ? $template['widget'][$k3] : null;
                        }
                        $xml_data['theme_list'][$k1]['template_list'][$k2]['widget'] = $widget_update;
                        $update = true;
                    }
                }
            }

            if (isset($update_data ['theme_use']) && isset($xml_data['theme_list'][$update_data ['theme_use']])) {

                if ($xml_data['theme_use'] != $update_data ['theme_use']) {
                    $xml_data['theme_use'] = $update_data ['theme_use'];
                    $update = true;
                }
            }

            if (isset($update_data ['widget'])) {
                if (isset($xml_data['theme_list'][$update_data['widget']['theme']]['template_list']
                    [$update_data['widget']['template']]['widget'][$update_data['widget']['w_id']])) {
                    $old_data = $xml_data['theme_list'][$update_data['widget']['theme']]['template_list']
                    [$update_data['widget']['template']]['widget'][$update_data['widget']['w_id']];

                    if ($old_data != $update_data['widget']['data']) {
                        $xml_data['theme_list'][$update_data['widget']['theme']]['template_list']
                        [$update_data['widget']['template']]['widget'][$update_data['widget']['w_id']]
                            = $update_data['widget']['data'];
                        $update = true;
                    }
                }
            }

            if ($update) {
                compiler_write_xml($themes_xml_path, $xml_data);
            }
            return true;
        } else {
            $theme_list = $this->get_theme_list();
            foreach ($theme_list as $n => $info) {
                unset($theme_list[$n]);
                $info = array();
                $info['theme'] = $n;
                $info['template_list'] = $this->get_template_list($n);
                foreach ($info['template_list'] as $tn => $tinfo) {
                    unset($info['template_list'][$tn]);
                    $tp = $tinfo['path'];
                    $tinfo = array();
                    $tinfo['page'] = $tn;
                    $tinfo['md5'] = md5_file($tp);
                    $tinfo['widget'] = $this->get_template_widget_info($n, $tn) ? $this->get_template_widget_info($n, $tn) : null;
                    //todo:template信息;
                    $info['template_list'][$tn] = $tinfo;
                }
                $info['widget_list'] = $this->get_widget_list($n);
                if ($info['widget_list']) {
                    foreach ($info['widget_list'] as $wn => $winfo) {
                        unset($info['widget_list'][$wn]);
                        $winfo = array();
                        $winfo['widget'] = $wn;
                        $winfo['is_used'] = 0;
                        //todo:widget信息;
                        $info['widget_list'][$wn] = $winfo;
                    }
                }
                $theme_list[$n] = $info;
            }
            if (isset($update_data ['theme_use']) && isset($xml_data['theme_list'][$update_data ['theme_use']])) {
                $xml_data['theme_use'] = $update_data ['theme_use'];
            }
            if (isset($update_data ['widget'])) {
                if (isset($xml_data['theme_list'][$update_data['widget']['theme']]['template_list']
                    [$update_data['widget']['template']]['widget'][$update_data['widget']['w_id']])) {
                    $xml_data['theme_list'][$update_data['widget']['theme']]['template_list']
                    [$update_data['widget']['template']]['widget'][$update_data['widget']['w_id']] = $update_data['widget']['data'];
                }
            }
            $xml_data['theme_list'] = $theme_list;
            compiler_write_xml($themes_xml_path, $xml_data);
            return true;
        }
    }


}