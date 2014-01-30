<?php namespace Plug;


class Field{
    
    use traits\ValidInputTypes;
    use traits\PropertyMethods;
    
    protected $name,
              $title,
              $description,
              $value,
              $type,
              $options,
              $namespace;


    static function getInstance($name, $type)
    {
        $field = __CLASS__;
        return new $field($name, $type);
    }
    
    function __construct($name, $type) {
        $this->name($name);
        $this->type($type);
    }
            
    function name($name = null){
        if(func_num_args() === 0) return $this->namespace .$this->name;
        
        $this->name = (string) $name;
        return $this;
    }
    
    function _namespace($ns = null){
        if(func_num_args() === 0) return $this->namespace;
        $this->namespace = $ns;
        return $this;
    }
    
    function title($title = null){
        if(func_num_args() === 0){
            if(!isset($this->title))
                return \Inflector::titleize($this->name);
            else
                return $this->title;
        }
        
        $this->title = (string) $title;
        return $this; 
    }
    
    function description($desc = null){
        if(func_num_args() === 0) return $this->description;
        
        $this->description = $desc;
        return $this;
    }
    
    function value($value = null){

        $type = $this->type();
        
        if(func_num_args() === 0){
            if($type === "select" || $type === "select-multiple")
                return $this->options()->get($this->value);
            else
                return $this->value;
        }
        
        //check if type is select /select-multiple and  if so
        //require value to be among selectable options

        if($type === "select" || ($type === "select-multiple" && !is_array($value))){
            
            if(!$this->options()->isset($value))
                throw new \InvalidArgumentException("option name " .$value ." is undefined, cannot set value to undefined option ");
            
        } else if($type === "select-multiple" && is_array($value)){
            
            foreach($value as $v){
                if(!$this->options()->isset($v))
                    throw new \InvalidArgumentException("option name " .$v ." is undefined, cannot set value to undefined option ");                
            }
            
        }    
        
        $this->value = $value;
        return $this;
    }

    function type($type = null){
        if(func_num_args() === 0) return $this->type;
        
        if(!in_array(trim($type), Field::$valid_types))
            throw new Exception("Invalid type: '" .$type ."' given for Field " .$this->name);
                
        $this->type = $type;
        return $this;
    }    
    
    function options(callable $callback){
        if(func_num_args() === 0) return $this->options;
        
        if(!isset($this->options)) $this->options =  new \PlugPress\Fields;
        
        call_user_func($callback, $this->options);
        return $this;
    }
    
}

?>
