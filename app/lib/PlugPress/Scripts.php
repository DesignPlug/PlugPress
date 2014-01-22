<?php namespace PlugPress;

    class Scripts {

        public $css_dir, $js_dir;
        static protected $scripts = array();

        static function getInstance($namespace, $css_dir = "", $js_dir = ""){
            if(isset(self::$scripts[$namespace])) return self::$scripts[$namespace];

            $slf = __CLASS__;
            self::$scripts[$namespace] = new $slf($css_dir, $js_dir);
            return self::$scripts[$namespace];
        }
        
        static function create($namespace, $css_dir, $js_dir){
            return self::getInstance($namespace, $css_dir, $js_dir);
        }

        function __construct($css_dir, $js_dir) {
            $this->css_dir = rtrim($css_dir, '/');
            $this->js_dir  = $js_dir;
        }

        function css($handle, $file)
        {
            wp_register_style($handle, plugins_url($this->css_dir .'/' .rtrim($file, '.css') .'.css'));
            wp_enqueue_style ($handle);
            return $this;
        }

        function js($handle, $file, $deps = array())
        {
            wp_register_script($handle, plugins_url($this->js_dir .'/' .rtrim($file, '.js') .'.js'), $deps);
            wp_enqueue_script($handle);
            return this;
        }

        function jquery($handle, $file)
        {
            return $this->js($handle, $file, array('jquery'));
        }

    }
?>
