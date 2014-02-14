<?php namespace PlugPress;

define("PLUGPRESS", true);


class Plugpress {
    
    static protected $DB, $plugins;
    
    static function is_activated(callable $callback = null, $param = []){
        if(self::plugin_is_activated("plugpress/plugpress.php")){
            if(is_callable($callback)){
                call_user_func_array($callback, $param);
            }
            return true;
        } 
        return false;
    }
    
    static function get_active_plugins(){
        if(!isset(self::$plugins)){
            self::$plugins = self::DB()->get_var("SELECT option_value FROM ". self::DB()->options ." WHERE option_name = 'active_plugins' ");
            self::$plugins = unserialize(self::$plugins);
        }
        return self::$plugins;
    }
    
    static function load_app_dir($path = null){
        $path = $path ?: ABSPATH .'/wp-content/plugins';
        $app_dir = $path ."/plugpress/app/lib/Plugpress/App.php";
        if(file_exists($app_dir) && !class_exists("Plugpress\App", false)){
            return self::is_activated(function() use($path){
                         require $path ."/plugpress/app/lib/Plugpress/App.php";
                   });
        }
    }
    
    static function run(callable $callback){
        if(self::load_app_dir()){
            call_user_func($callback);
            return true;
        } else {
            return false;
        }
    }
    
    static function plugin_is_activated($path){
        return in_array($path, self::get_active_plugins());
    }
    
    static function error_notice($message, $param = null){
        return self::admin_notice("error", $message, $param);
    }
    
    static function admin_notice($type, $message, $param = null){
        echo "<div class='" .$type ."'><p>" .  __(sprintf($message, $param)) ."</p></div>";
    }
    
    static function DB(){
        if(!isset(self::$DB)){
            global $wpdb;
            self::$DB = $wpdb;
        }
        
        $param = func_get_args();
        if(count($param) > 0){
            return call_user_func_array([self::$DB, 'query'], $param);
        }
        
        return self::$DB;
    }
}

?>
