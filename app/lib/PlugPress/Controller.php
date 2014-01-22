<?php namespace Plugpress;

abstract class Controller {
    
    function __construct($method = null) {
        if(isset($method)){
            if(!method_exists($this, $method)){
                throw new \BadMethodCallException("method: " .$method ." does not exist for " .get_called_class());
            }
        }
    }
    
    static final function getInstance($method = null){
        $cls = get_called_class();
        return new $cls($method);
    }
}

?>
