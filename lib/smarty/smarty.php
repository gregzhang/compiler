<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 16/12/4
 * Time: 下午7:44
 */
class smarty_plugin {

    private $smarty;
    private $smarty_path;

    function __construct()
    {
        $this->smarty_path =  APP_PATH.'/template/plugin/smarty/smarty-3.1.30';
        $smarty_path =  $this->smarty_path.'/libs/Smarty.class.php';
        require $smarty_path;
        $this->smarty = new Smarty();
        $this->set_config();
    }

    function set_config(){
        $this->smarty->setLeftDelimiter('<{');
        $this->smarty->setRightDelimiter('}>');
        $this->smarty->setTemplateDir($this->smarty_path.'/demo/templates');
        $this->smarty->setCompileDir(CACHE_PATH.'/smarty/compile');
        $this->smarty->setCacheDir(CACHE_PATH.'/smarty/cache');
        $this->smarty->addPluginsDir($this->smarty_path.'/demo/plugins');
//        $this->smarty->addPluginsDir($this->smarty_path.'/libs/plugins');
        $this->smarty->debugging = false;
        $this->smarty->caching = false;
        $this->smarty->cache_lifetime = 120;
    }

    function assign($tpl_var, $value = null, $nocache = false){
        $this->smarty->assign($tpl_var, $value, $nocache);
    }

    function display($template = null, $cache_id = null, $compile_id = null, $parent = null){
        $this->smarty->display($template, $cache_id, $compile_id, $parent);
    }

    function setTemplateDir($template_dir, $isConfig = false){
        $this->smarty->setTemplateDir($template_dir,$isConfig);
    }


}