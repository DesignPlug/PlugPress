<?php namespace CodeTemplates\Templates;

class CodeTemplates extends \PlugPress\CustomTemplate{

    static protected $instance;
    
    
    static function getInstance()
    {
        if(is_object(self::$instance)) return self::$instance;
        
        $slf = __CLASS__;
        self::$instance = new $slf;
        return self::$instance;
    }
    
    function __construct() {
        $this->posttype              = "post";
        $this->template_dir          = PPCODE_VIEWS_DIR .'/code';
        $this->template_override_dir = "plugpress/code";
        $this->redirect              = true;
    }
    
    function loadTemplateStyles($template = null)
    {
         \CodeTemplates\Plugin::Scripts()->css('ppcode_main_style', 'main')
                                         ->jquery('ppcode_main_js', 'main');
                                        
    }
    
}
?>
