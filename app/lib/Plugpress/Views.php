<?php namespace Plugpress;


class Views{

    static protected $views = array(),
                     $data  = array();
    
    static function getInstance($namespace) {
        if(!isset(self::$views[$namespace])){
            throw new Exception("No Instance of View found with namespace " .$namespace);
        }
        return self::$views[$namespace];
    }
    
    static function get($namespace){
        return self::getInstance($namespace);
    }
    
    static function add($namespace, $dir, Scripts $scripts_object) {
        return self::$views[$namespace] = new View($dir, $scripts_object);
    }
    
    static function remove($namespace){
        unset(self::$views[$namespace]);
    }
    
    static function create($namespace, $dir, Scripts $scripts_object){
        return self::add($namespace, $dir, $scripts_object);
    }
    
    static function setData($file, $data){
        $file = strtolower($file);
        if(isset(self::$data[$file])){
            self::$data = array_merge(self::$data[$file], $data);
        } else {
            self::$data[$file] = $data;
        }
    }
    
    /**
     * 
     * if getData is called from a file that has data bound to it
     * we can get the view data without specifiying a namespace
     * 
     **/
    
    static function getData(){
        
        $trace         = debug_backtrace();
        $calling_file  = strtolower($trace[0]['file']);
        if(isset(self::$data[$calling_file]))
            return self::$data[$calling_file];
    }
}

?>
