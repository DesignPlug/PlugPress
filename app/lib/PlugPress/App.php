<?php namespace PlugPress;

use \Plug\Autoloader as Autoloader;
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;


if(!function_exists("DS")){
    function DS($path){
        return str_replace(["/","\\"], DIRECTORY_SEPARATOR, $path);
    }
}


if(!class_exists("PlugPress\APP")){
    
    require 'vendors/Plug/autoloader.php';
    
    class APP {
        
        static protected $plugins = array();

        static public function init($plugin_name, $plugin_file_name, $namespace)
        {
            if(!isset(self::$plugins[$plugin_name]))
            {   
                //if this is the first call
                
                if(count(self::$plugins) === 0) 
                {
                    //define Plugpress App Dir

                    define("Plugpress_APP_DIR", DS(ABSPATH . 'wp-content\plugins\plugpress\app'));
                
                    //start session 
                    add_action("init", ['\Plug\Session', 'start']);
                    
                    //create route 
                    add_action("init", ["\Plugpress\Route", "create"]);
                    
                   //register core and core vendors dir
                    spl_autoload_register([new Autoloader(\Plugpress_APP_DIR .'\lib\\'), 'load']);
                    spl_autoload_register([new Autoloader(\Plugpress_APP_DIR .'\lib\Plugpress\vendors\\'), 'load']);
                    spl_autoload_register([new Autoloader(\Plugpress_APP_DIR .'\lib\Plugpress\vendors\{class}\\'), 'load']);
                    
                    //load db
                    self::loadDB();
                    
                    //kill  all flashes at the end  of sessions
                    add_action("shutdown", ['\Plug\Session', 'clearFlash']);
                }
                
                //bootstrap plugin application
                self::$plugins[$plugin_name] = PluginInitializer::init($plugin_name, $plugin_file_name, $namespace);
                
            }
            else
            {
                throw new Exception("Attempted to call PlugPress\APP:init on " .$plugin_name ." twice");
            }
        }
        
        static function pluginExists($name){
            return isset(self::$plugins[$name]) ? true : false;
        }
        
        static protected function loadDB()
        {
            //require Composer autoloader
            require \Plugpress_APP_DIR .'\lib\Plugpress\vendors\WPMVC\vendor\autoload.php';            
            
            $capsule = new Capsule();
            $capsule->addConnection(array(
                    'driver' => 'mysql',
                    'host' => DB_HOST,
                    'database' => DB_NAME,
                    'username' => DB_USER,
                    'password' => DB_PASSWORD,
                    'charset' => DB_CHARSET,
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => ''));
            $capsule->setEventDispatcher(new Dispatcher(new Container()));
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
        }
    }
    
}

?>
