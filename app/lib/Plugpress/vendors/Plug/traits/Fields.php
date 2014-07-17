<?php namespace Plug\traits;

trait Fields {

    protected $namespace, $fields;
    
    function Fields($namespace = "")
    {
        $this->namespace = $this->namespace ?: $namespace;
        
        if(!isset($this->fields)){
            $this->fields = new \Plug\Fields($this->namespace);
        }
        return $this->fields;
    }    
    
}

?>
