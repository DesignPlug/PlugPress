<?php namespace Plug;


class Session {

    static protected $flash_messages = array();
    static protected $flash_key = "flash";
    
    static function start(){
        session_start();
    }
    
    static function create($name, $value, $flash = false){
        if($flash === true){     
            $_SESSION[self::$flash_key][$name] = self::$flash_messages[$name] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
    }
    
    static function get($name, $flash = false){
            if($flash === true){
                return @$_SESSION[self::$flash_key][$name];
            } else {
                return @$_SESSION[$name];
            }
    }
    
    
    static function createFlash($name, $value){
       return self::create($name, $value, true);
    }
    
    static function getFlash($name){
        return self::get($name, true);
    }
    
    static function clearFlash(){      
        
        if(isset($_SESSION[self::$flash_key])){
            foreach($_SESSION[self::$flash_key] as $key => $val){
                if(!isset(self::$flash_messages[$key])) unset($_SESSION[self::$flash_key][$key]);
            }
        }
            
    }
    
    
}

?>
