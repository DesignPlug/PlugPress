<?php namespace Plug\traits;

trait PropertyMethods {
    
    protected $properties = array();
    
    function __call($fn, $param){
        if(count($param) === 0) return @$this->properties[$fn];
        
        $this->properties[$fn] = $param[0];
        return $this;
    }
}

?>
