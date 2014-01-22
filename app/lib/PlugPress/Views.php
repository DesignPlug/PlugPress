<?php namespace Plugpress;


class Views{

    static protected $views = array();
    
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
}

?>
