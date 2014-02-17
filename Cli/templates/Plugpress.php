<?php namespace PlugPress;

define("PLUGPRESS", true);


class Plugpress {
    
    static protected $DB, $plugins, $current_admin_page;
    
    static function is_activated($callback = null, $param = array()){
        if(self::plugin_is_activated("plugpress/plugpress.php")){
            if(is_callable($callback)){
                call_user_func_array($callback, $param);
            }
            return true;
        } 
        return false;
    }
    
    static function admin_current_page(){
        if(is_admin()){
            if(!isset(self::$current_admin_page)){
                
                global $pagenow;
                
                if(!isset($pagenow)){
                    $url=strtok($_SERVER["REQUEST_URI"],'?');
                    self::$current_admin_page = basename($url);
                } else {
                    self::$current_admin_page = $pagenow;
                }
            }
            return self::$current_admin_page;
        }
    }
    
    static function get_active_plugins(){
        if(!isset(self::$plugins)){
            self::$plugins = self::DB()->get_var("SELECT option_value FROM ". self::DB()->options ." WHERE option_name = 'active_plugins' ");
            self::$plugins = unserialize(self::$plugins);
        }
        return self::$plugins;
    }
    
    static function load_app_dir(){
        $app_dir = self::APP_DIR("/lib/Plugpress/App.php");
        if(file_exists($app_dir) && !class_exists("Plugpress\App", false)){
            return self::is_activated(function() use($app_dir){
                         require $app_dir;
                   });
        }
    }
    
    static function APP_DIR($path = ""){
        return self::DS(ABSPATH .'/wp-content/plugins/plugpress/app' .$path); 
    }
    
    static function DIR($path = ""){
        return self::APP_DIR("/lib/Plugpress/" .$path);
    }
    
    static function run($callback){
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
    
    static function init_error_notice($plugin_name){
        $message = "<strong>There was an error initializing PlugPress dependent plugin: '%s'.</strong><br/> 
                    1) Make sure Plugpress is installed and activated. <br/>";

        if(self::admin_current_page() == "plugins.php")
            $message .= " 2) If Plugpress is already activated, <a href='" .$_SERVER['PHP_SELF'] ."'>try refreshing</a>";

        return self::error_notice($message, (array) $plugin_name);
    }
    
    static function error_notice($message, $param = null){
        return self::admin_notice("error", $message, $param);
    }
    
    static function admin_notice($type, $message, $param = array()){
        echo "<div class='" .$type ."'><p>" .  __(call_user_func_array("sprintf", array_merge((array)$message, $param) )) ."</p></div>";
    }
    
    static function DS($path){
        return str_replace(array("//","/", "\\\\", "\\"), DIRECTORY_SEPARATOR, $path);
    }
    
    static function DB(){
        if(!isset(self::$DB)){
            global $wpdb;
            self::$DB = $wpdb;
        }
        
        $param = func_get_args();
        if(count($param) > 0){
            return call_user_func_array(array(self::$DB, 'query'), $param);
        }
        
        return self::$DB;
    }
    
    static function debug_mode(){
        ini_set('display_errors',1);
        ini_set('display_startup_errors',1);
        error_reporting(-1);
    }
    
}

?>
