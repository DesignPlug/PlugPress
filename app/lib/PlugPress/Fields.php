<?php namespace PlugPress;

class Fields implements \Iterator{
    
    protected $fields, $namespace;
    
    function __construct($namespace) {
        $this->namespace = $namespace;
    }
    
    function add($name, $type, $callback = null){
        
        if(func_num_args() === 2 && is_callable($type)){ 
            $callback = $type;
            $type = 'text';
        }
        
        if(!is_callable($callback))
            throw new Exception("Field->add method expects second param to be callable, field: " .$name ." could not be created");
        
        if(!isset($this->fields[$name])){
            $this->fields[$name] = Field::getInstance($name, $type)->_namespace($this->namespace);
        }
        
        call_user_func($callback, $this->fields[$name]);
        return $this;
    }
    
    function remove($name){
        if(isset($this->fields[$name])) unset($this->fields[$name]);
    }
    
    function rewind() 
    {
        reset($this->fields);
    }

    function current() 
    {
        return current($this->fields);
    }

    function key() 
    {
        return key($this->fields);
    }

    function next() 
    {
        return next($this->fields);
    }

    function valid() 
    {
        return $this->current();
    }
}

?>
