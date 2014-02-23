<?php namespace Plugpress;

use \Plugpress\HTTP as HTTP;

class Route {
    static protected $routes = array();
    static protected $created = false;
    static protected $router;
    static protected $request_types = array("GET", "POST", "DELETE", "PUT");
    
    
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
            
            //to store returned template
            $response;
            
            //if target is an array, that means before and 
            //after callbacks are possibly set
            
            if(is_array($match['target'])){
                
                if(isset($match['target']['before'])){
                    
                    //only fire target callback if before doesn't return false
                    if(self::callTarget($match['target']['before'], $match) !== false){
                        $response = self::callTarget($match['target']['cb'], $match['params']); 
                    }
                } else {
                    $response = self::callTarget($match['target']['cb'], $match['params']);
                }
                
                //call after callback if given
                if(isset($match['target']['after'])){
                    self::callTarget($match['target']['after'], $match['params']);
                }
                   
            } else {
                $response = self::callTarget($match['target'], $match['params']);
            }
            
            return $response ?: $default_response;
            
        } else {
            return $default_response;
            
        }
        
    }
    
    static protected function callTarget($target, $param){
        if(!is_callable($target)){
            $target = explode('#', trim($target));
            $target[0] = new $target[0];
            if($target[0] instanceof \Plugpress\Controller){
                if($target[0]->ajaxOnly === true && !HTTP::isAjaxRequest()){
                    $target[1] = 'get404';
                }
            }
        }

        return call_user_func_array($target, $param); 
    } 
    
    
    static function map($method, $route, $target, $name = null){
       return call_user_func_array(array(self::getRouter(), "map"), func_get_args());
    }
    
    static function __callStatic($fn, $param){
        if(in_array(strtoupper($fn), self::$request_types)){
            return call_user_func_array(array("\Plugpress\Route", "map"), array_merge((array) $fn, $param));
        }
        throw new \BadMethodCallException("call to undefined method $fn \Plugpress\Route");
    }
    
    static function getRouter(){
        return self::$router ?: self::$router = new \AltoRouter(array(), basename(site_url()) ."/");
    }
    
    static function URL($route, $param = array()){
        $path = ltrim(self::getRouter()->generate($route, $param), self::getRouter()->getBasePath());
        return site_url($path);
    }
    
    static function redirect($to, $status = 302){
        return wp_redirect($to, $status);
    }
}
 
?>
