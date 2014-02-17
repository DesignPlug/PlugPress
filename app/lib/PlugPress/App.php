<?php namespace Plugpress;

use \Plug\Autoloader as Autoloader;
use \Plug\Session as Session;
use \Plugpress\Plugpress as Plugpress;
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;


if(!function_exists("DS")){
    function DS($path){
        return Plugpress::DS($path);
    }
}

if(!class_exists("Plugpress\APP")){
    
    
    class APP {
        
        static protected $plugins = array();

        static public function init($plugin_name, $plugin_file_name, $namespace)
        {
            if(!isset(self::$plugins[$plugin_name]))
            {   
                //if this is the first call
                
                if(count(self::$plugins) === 0) 
                {
                    require 'vendors/Plug/Autoloader.php';
                    
                   //register core and core vendors dir
                    spl_autoload_register(array(new Autoloader(Plugpress::APP_DIR('\lib\\') ), 'load'));
                    spl_autoload_register(array(new Autoloader(Plugpress::DIR('\vendors\\') ), 'load'));
                    spl_autoload_register(array(new Autoloader(Plugpress::DIR('\vendors\{class}\\') ), 'load'));
                    
                    //start session 
                    Session::start(DS(Plugpress::DIR('\sessions')));
                    
                    //load db
                    self::loadDB();
                    
                    //create route 
                    add_action("init", array("\Plugpress\Route", "create"));
                    
                    //kill  all flashes at the end  of sessions
                    add_action("shutdown", array('\Plug\Session', 'clearFlash'));
                }
                
                //bootstrap plugin application
                self::$plugins[$plugin_name] = PluginInitializer::init($plugin_name, $plugin_file_name, $namespace);
                
            }
            else
            {
                throw new Exception("Attempted to call Plugpress\APP:init on " .$plugin_name ." twice");
            }
        }
        
        static function pluginExists($name){
            return isset(self::$plugins[$name]) ? true : false;
        }
        
        static protected function loadDB()
        {
            //require Composer autoloader
            require Plugpress::DIR('\vendors\WPMVC\vendor\autoload.php');            
            
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
