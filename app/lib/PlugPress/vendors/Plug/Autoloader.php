<?php namespace Plug;

class Autoloader {
    
    public $path;
    
    function __construct($path) {
        $this->path = $path;
    }
    
    function load($class){
        $file = rtrim(preg_replace("/{([\s]*class[\s]*)}/", $class, $this->path), '\\') .'\\' .ltrim($class, '\\') .'.php';
        if(!file_exists($file)) return false;
        require $file;
    }
    
}

?>
