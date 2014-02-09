<?php namespace Plugpress;

use \Plugpress\HTTP as HTTP;

class Route {
    static protected $routes = array();
    static protected $created = false;
    static protected $router;
    static protected $request_types = array("GET", "POST", "DELETE", "PUT");
    static public $ignore_methods = array('__construct',
                                          '__get',
                                          '__set',
                                          '__call',
                                          '__callStatic',
                                          '__destruct',
                                          'getInstance');
    
    
    static function create(){
        if(!self::$created){
            
            //dispatch requests
            add_action("template_redirect", array("\Plugpress\Route", "route"));
            
            self::$created = true;
        }
    }
    
    static function route($default_response){

        
        //check url for a match
        
        if($match = self::getRouter()->match()){

            
            //default status heading 200
            HTTP::setHeaderStatus(200);
            
            //if target is an array, that means before and 
            //after callbacks are possibly set
            
            if(is_array($match['target'])){
                
                if(isset($match['target']['before'])){
                    
                    //only fire target callback if before doesn't return false
                    if(self::callTarget($match['target']['before'], $match) !== true){
                        
                        self::callTarget($match['target']['cb'], $match['params']); 
                    }
                } else {
                    self::callTarget($match['target']['cb'], $match['params']);
                }
                
                //call after callback if given
                if(isset($match['target']['after'])){
                    self::callTarget($match['target']['after'], $match['params']);
                }
                   
            } else {
                self::callTarget($match['target'], $match['params']);
            }
            
        } else {
            return $default_response;
            
        }
        
    }
    
    static protected function callTarget($target, $param){
        if(!is_callable($target)){
            $target = explode('#', trim($target));
            $target[0] = new $target[0]; 
        }

        return call_user_func_array($target, $param); 
    } 
    
    
    static function map($method, $route, $target, $name = null){
       return call_user_func_array([self::getRouter(), "map"], func_get_args());
    }
    
    static function __callStatic($fn, $param){
        if(in_array(strtoupper($fn), self::$request_types)){
            return call_user_func_array(["\Plugpress\Route", "map"], array_merge([$fn], $param));
        }
        throw new \BadMethodCallException("call to undefined method $fn \Plugpress\Route");
    }
    
    static function getRouter(){
        return self::$router ?: self::$router = new \AltoRouter([], basename(site_url()) ."/");
    }
}
 
?>
