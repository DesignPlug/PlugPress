<?php namespace PlugPress;

use \Plug\Autoloader as Autoloader;

if(!class_exists("PlugPress\APP")){
    
    require 'vendors/Plug/autoloader.php';
    
    class APP {
        
        static protected $plugins = array();

        static public function init($plugin_name, $plugin_file_name, $namespace)
        {
            if(!isset(self::$plugins[$plugin_name]))
            {
                
                //define plugin constants
                self::defineConst($namespace, $plugin_file_name, $plugin_name);
                
                //if this is the first call
                
                if(count(self::$plugins) === 0) 
                {
                    //start session 
                    add_action("init", ['\Plug\Session', 'start']);
                    
                    //create route 
                    add_action("init", ["\Plugpress\Route", "create"]);
                    
                   //register core and core vendors dir
                    spl_autoload_register([new Autoloader(constant($namespace .'APP_DIR') .'\lib\\'), 'load']);
                    spl_autoload_register([new Autoloader(constant($namespace .'APP_DIR') .'\lib\Plugpress\vendors\\'), 'load']);
                    spl_autoload_register([new Autoloader(constant($namespace .'APP_DIR') .'\lib\Plugpress\vendors\{class}\\'), 'load']);
                    
                    //kill  all flashes at the end  of sessions
                    add_action("shutdown", ['\Plug\Session', 'clearFlash']);
                    
                }
                
                //register autoloader for new plugin
                spl_autoload_register([new Autoloader(constant($namespace .'PLUGIN_DIR')), 'load']);

                //bootstraps plugin application
                self::bootstrap($plugin_name, $namespace);
                
            }
            else
            {
                throw new Exception("Attempted to call PlugPress:init on " .$plugin_name ." twice");
            }
        }
        
        static protected function defineConst($namespace, $plugin_file_name, $plugin_name)
        {
            $root_dir = str_replace('\wp-admin', '', getcwd());
            
            //define constants for specific plugin
            
            define($namespace ."BASE_DIR", dirname($root_dir .'\wp-content\plugins\\' .$plugin_file_name .'\\' .$plugin_file_name .'.php'));
            define($namespace ."APP_DIR",  constant($namespace .'BASE_DIR') .'\app');
            define($namespace ."PLUGIN_DIR", constant($namespace .'APP_DIR') .'\plugin');
            define($namespace ."DIR", constant($namespace .'PLUGIN_DIR') .'\\' .$plugin_name);
            define($namespace ."VIEWS_DIR",   constant($namespace .'DIR') .'\views');
            define($namespace ."CTRL_DIR",   constant($namespace .'DIR') .'\controllers');
            define($namespace ."MODELS_DIR",   constant($namespace .'DIR') .'\models');
            define($namespace ."PUBLIC_DIR",  constant($namespace .'BASE_DIR') .'\public');
            define($namespace ."CSS_PATH", '/' .$plugin_file_name .'/public/css');
            define($namespace ."JS_PATH",  '/' .$plugin_file_name .'/public/js');
        }
        
        static protected function bootstrap($plugin_name, $namespace)
        {
            $cls_name = str_replace(array('-',' '), '', $plugin_name) .'\Plugin';
            
            //make constants, views and scripts accessible via Plugin::CONSTANT_NAME()
            
            $call = "call_user_func";
            $set_const = array($cls_name, "set_const");
            
            $call($set_const, "NAMESPACE", $namespace);
            $call($set_const, "BASE_DIR", constant($namespace ."BASE_DIR"));
            $call($set_const, "APP_DIR",  constant($namespace ."APP_DIR"));
            $call($set_const, "PLUGIN_DIR",  constant($namespace ."PLUGIN_DIR"));
            $call($set_const, "VIEWS_DIR",  constant($namespace ."VIEWS_DIR"));
            $call($set_const, "DIR",  constant($namespace ."DIR"));
            $call($set_const, "CTRL_DIR",  constant($namespace ."CTRL_DIR"));
            $call($set_const, "MODELS_DIR",  constant($namespace ."MODELS_DIR"));
            $call($set_const, "PUBLIC_DIR",  constant($namespace ."PUBLIC_DIR"));
            $call($set_const, "CSS_PATH",  constant($namespace ."CSS_PATH"));
            $call($set_const, "JS_PATH",  constant($namespace ."JS_PATH"));
            
            //set method for getting scripts
            $call($set_const, "Scripts", Scripts::create($namespace, 
                                                         constant($namespace ."CSS_PATH"), 
                                                         constant($namespace ."JS_PATH")));
            
            //set methods for retrieving views
            $call($set_const, "View", Views::create($namespace, 
                                                    constant($namespace ."VIEWS_DIR"), 
                                                    $call(array($cls_name, "Scripts"))));
            
            
            //shorthand to get view vars
            $call($set_const, "ViewData", function() use ($namespace){
                        \Plugpress\Views::getInstance($namespace);
            });
            
            //instantiate bootstrap
            $bootstrap = self::$plugins[$plugin_name] = new $cls_name;
            
            //registers activation/deactivation hooks if bootstrap implements PlugPressBootstrap interface
            if($bootstrap instanceof \Plugpress\Plugin)
            {
                register_activation_hook($file, array($bootstrap, 'activate'));
                register_deactivation_hook($file, array($bootstrap, 'deactivate'));
                register_uninstall_hook($file, array($bootstrap, 'uninstall'));
                if($bootstrap->register_autoload === true){
                    spl_autoload_register (array($bootstrap, 'autoload'));
                }
            }
            else
            {
                throw new \InvalidArgumentException("Bootstrap for plugin: " .$plugin_name ." must extend Plugpress\Plugin Class");
            }
        }
    }
}

?>
