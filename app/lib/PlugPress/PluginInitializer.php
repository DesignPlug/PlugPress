<?php namespace Plugpress;

use \Plug\Autoloader as Autoloader;

final class PluginInitializer {
    
    protected $plugin_name, 
              $plugin_file_name, 
              $namespace,
              $plugin,
              $plugin_class_name,
              $base_path;
    
    function __get($name){
        return $this->{$name};
    }
    
    
    function __construct($name, $file_name, $namespace){
        
        if(!APP::pluginExists($name)){
            
            $this->plugin_name       = $name;
            $this->plugin_file_name  = $file_name;
            $this->namespace         = $namespace;
            $this->plugin_class_name = str_replace(array('-',' '), '', $this->plugin_name) .'\Plugin';
            
            $this->defineConstants();
            $this->bootstrap();
        } else {
            throw new \Exception("Cannot Initialize Plugin {$name} Twice");
        }
    }
    
    static function init($name, $file_name, $namespace){
        $cls     = get_called_class();
        $plugin  = new $cls($name, $file_name, $namespace);
        return $plugin->plugin;
    }
    
    protected function defineConstants(){
        
        $root_dir = str_replace(DS("\wp-admin"), "", getcwd());
        
        //define constants for specific plugin

        $ns = $this->namespace;
        define($ns ."BASE_DIR", $root_dir .'\wp-content\plugins\\' .$this->plugin_file_name);
        define($ns ."PLUGIN_FILE", $this->base_path = constant($ns ."BASE_DIR") ."\\" .$this->plugin_file_name .".php");
        define($ns ."APP_DIR",  constant($ns .'BASE_DIR') .'\app');
        define($ns ."PLUGIN_DIR", constant($ns .'APP_DIR') .'\plugin');
        define($ns ."DIR", constant($ns .'PLUGIN_DIR') .'\\' .$this->plugin_name);
        define($ns ."VIEWS_DIR",   constant($ns .'DIR') .'\views');
        define($ns ."CTRL_DIR",   constant($ns .'DIR') .'\controllers');
        define($ns ."MODELS_DIR",   constant($ns .'DIR') .'\models');
        define($ns ."PUBLIC_DIR",  constant($ns .'BASE_DIR') .'\public');
        define($ns ."CSS_PATH", '/' .$this->plugin_file_name .'/public/css');
        define($ns ."JS_PATH",  '/' .$this->plugin_file_name .'/public/js');

        //set wp table prefix
        global $wpdb;
        define($ns ."DB_PREFIX", strtolower($wpdb->prefix .$ns));
        
        
        $this->autoloadInit();
        
        //make constants, views and scripts accessible via Plugin::CONSTANT_NAME()
        
        $this->plugin = new $this->plugin_class_name;
        
        $call = "call_user_func";
        $set_const = array($this->plugin, "setVar");

        $call($set_const, "_NAMESPACE", $ns);
        $call($set_const, "BASE_DIR", constant($ns ."BASE_DIR"));
        $call($set_const, "PLUGIN_FILE", constant($ns ."PLUGIN_FILE"));
        $call($set_const, "DB_PREFIX", constant($ns ."DB_PREFIX"));
        $call($set_const, "APP_DIR",  constant($ns ."APP_DIR"));
        $call($set_const, "PLUGIN_DIR",  constant($ns ."PLUGIN_DIR"));
        $call($set_const, "VIEWS_DIR",  constant($ns ."VIEWS_DIR"));
        $call($set_const, "DIR",  constant($ns ."DIR"));
        $call($set_const, "CTRL_DIR",  constant($ns ."CTRL_DIR"));
        $call($set_const, "MODELS_DIR",  constant($ns ."MODELS_DIR"));
        $call($set_const, "PUBLIC_DIR",  constant($ns ."PUBLIC_DIR"));
        $call($set_const, "CSS_PATH",  constant($ns ."CSS_PATH"));
        $call($set_const, "JS_PATH",  constant($ns ."JS_PATH"));
        
        //set method for getting scripts
        $call($set_const, "Scripts", Scripts::create($ns, 
                                                     constant($ns ."CSS_PATH"), 
                                                     constant($ns ."JS_PATH")));

        //set methods for retrieving views
        $call($set_const, "View", Views::create($ns, 
                                                constant($ns ."VIEWS_DIR"), 
                                                $call(array($this->plugin_class_name, "Scripts"))));


        //shorthand to get view vars
        $call($set_const, "ViewData", function() use ($ns){
                    \Plugpress\Views::getInstance($ns);
        });
    }
    
    protected function autoloadInit(){
        //register autoload path to plugin
        spl_autoload_register(array(new Autoloader(constant($this->namespace .'PLUGIN_DIR')), 'load'));        
    }
    
    protected function bootstrap(){

        //instantiate bootstrap
        $bootstrap = $this->plugin;

        //registers activation/deactivation hooks if bootstrap implements PlugPressBootstrap interface
        if($bootstrap instanceof \Plugpress\Plugin)
        {
            add_action("plugins_loaded", array($bootstrap, 'init'));
            register_activation_hook($this->base_path, array($bootstrap, 'activate'));
            register_deactivation_hook($this->base_path, array($bootstrap, 'deactivate'));
            register_uninstall_hook($this->base_path, array($bootstrap, 'uninstall'));

            //initialize routes
            $bootstrap->route();

            if($bootstrap->register_autoload === true){
                spl_autoload_register (array($bootstrap, 'autoload'));
            }
        }
        else
        {
            throw new \InvalidArgumentException("Bootstrap for plugin: " .$plugin_name ." must be instance of Plugpress\Plugin Class");
        }
    }
    
}

?>
