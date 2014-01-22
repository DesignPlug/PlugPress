<?php namespace Plugpress;

 abstract class Plugin implements \PlugPress\Bootstrap{
   
     static protected $constants = array();
     static protected $namespace;
     
     static function set_const($key, $value, $use_namespace = false){
         if(trim($key) === "NAMESPACE") {
             self::$namespace = $value;
             return;
         }
         if($use_namespace === true) $key = self::$namespace .$key;
         self::$constants[$key] = $value;
     }
     
     static function __callStatic($name, $arg = null) {
         if($name === "NAMESPACE"){
             return self::$namespace;
         }
         else if(isset(self::$constants[$name])){
             if(is_callable(self::$constants[$name])){
                 return call_user_func(self::$constants[$name]);
             }
             else{
                 return self::$constants[$name];
             }
         }
         else{
             throw new \BadMethodCallException;
         }
     }


     abstract function activate();
     abstract function uninstall();
     abstract function deactivate();
     abstract function autoload();
     
}

?>
