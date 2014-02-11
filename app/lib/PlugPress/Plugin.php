<?php namespace Plugpress;

 abstract class Plugin implements \PlugPress\Bootstrap{
   
     static protected $constants = array();
     static protected $namespace;
     static protected $pluginVars = array();
     
     function setVar($key, $val){
         self::$pluginVars[get_called_class()][$key] = $val;
     }
     
     function getVar($key){
         return self::$pluginVars[get_called_class()][$key];
     }
     
     function __get($key){
         return $this->getConst($key);
     }
     
     static function __callStatic($fn, $param){
         $const = self::$pluginVars[get_called_class()][$fn];
         if(is_callable($const)){
             return call_user_func_array($const, $param);
         } else {
             return $const;
         }
     }


     abstract function activate();
     abstract function uninstall();
     abstract function deactivate();
     abstract function autoload();
     
}

?>
