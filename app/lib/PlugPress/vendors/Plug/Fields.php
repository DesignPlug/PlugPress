<?php namespace Plug;

class Fields implements \Iterator, \ArrayAccess{
    
    protected $fields, $namespace;
    
    function __construct($namespace = "") {
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
    
    function arrayToFields($array, $selected_fields = false){
        if(is_array($selected_fields) || ($selected_fields instanceof \ArrayAccess)){
            
            foreach($selected_fields as $field_name => $field_value){
                
                //if associative array, use the key as the field name
                //otherwise use the value
                if(is_numeric($field_name)) $field_name = $field_value;
                
                //remove namespace from copy used to create field
                //and use namespace for array key
                $fname    = str_replace($this->namespace, "", $field_name);
                $ns_fname = $this->namespace .$fname;
                
                if(isset($array[$ns_fname])){
                    $val = $array[$ns_fname];
                    $this->add(str_replace($this->namespace, "", $fname), function($fld) use ($val){
                        $fld->value($val);
                    });
                }
            }
            
        } else {
            
            foreach($array as $k => $v){
                $this->add(str_replace($this->namespace, "", $k), function($fld) use ($v){
                    $fld->value($v);
                });
            }
            
        }
            
    }
    
    function remove($name){
        if(isset($this->fields[$name])) unset($this->fields[$name]);
    }
    
    function get($name){
        return @$this->fields[$name];
    }
    
    function exists($name){
        return isset($this->fields[$name]) ? true : false;
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
        return $this->namespace .key($this->fields);
    }

    function next() 
    {
        return next($this->fields);
    }

    function valid() 
    { 
        return $this->current();
    }
    
    function offsetExists ( $offset ){
        return $this->exists(str_replace($this->namespace, "", $offset));
    }

    function offsetGet ( $offset ){
        return $this->get(str_replace($this->namespace, "", $offset));
    }
    
    function offsetSet ($offset ,$value){
        $this->add(str_replace($this->namespace, "", $offset), function($field) use ($value){ $field->value($value); });
    }
    
    function offsetUnset ($offset){
        $this->remove(str_replace($this->namespace, "", $offset));
    }    
    
}

?>
