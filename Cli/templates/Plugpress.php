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
            self::$plugins = self::DB()->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'active_plugins' ");
            self::$plugins = unserialize(self::$plugins);
        }
        return self::$plugins;
    }
    
    static function plugin_is_activated($path){
        return in_array($path, self::get_active_plugins());
    }
    
    static function DB(){
        if(!isset(self::$DB)){
            global $wpdb;
            self::$DB = $wpdb;
        }
        return self::$DB;
    }
}

?>
