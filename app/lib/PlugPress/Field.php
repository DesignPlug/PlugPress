<?php namespace Plugpress;


class Field{
    protected $name,
              $title,
              $description,
              $value,
              $validation_rules,
              $type,
              $options,
              $namespace;

    static protected $valid_types = array('text',
                                          'textarea',
                                          'select',
                                          'radio',
                                          'select-multiple',
                                          'checkbox',
                                          'url',
                                          'email',
                                          'tel',
                                          'search',
                                          'date',
                                          'month',
                                          'week',
                                          'time',
                                          'datetime-local',
                                          'number',
                                          'range',
                                          'color',
                                          'hidden');


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
    
    function _namespace($ns){
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
    
    function value($value = null, $as_new_option = false){
        if(func_num_args() === 0) return $this->value;
        
        if(is_array($value)){
            $value = array_values($value);
            $this->value = $value[0];

            if(count($value) > 1) {
                $this->options(array_values($value));
            }
        } else {
            if(isset($this->options)) {
                if(!in_array($value, $this->options) && $as_new_option !== true) {
                    throw new Exception("attempted to set value for Field " .$name ." not in predefined options list");
                } else {
                    $this->options[] = $value;
                }
                
            }
            $this->value = $value;
        }
        
        return $this;
    }
    
    function validation_rules($rules){
        if(func_num_args() === 0) return $this->validation_rules;
        
        $this->validation_rules = $rules;
        return $this;
    }

    function type($type){
        if(func_num_args() === 0) return $this->type;
        
        if(!in_array(trim($type), Field::$valid_types))
            throw new Exception("Invalid type: '" .$type ."' given for Field " .$this->name);
                
        $this->type = $type;
        return $this;
    }    
    
    function options($options){
        if(func_num_args() === 0) return $this->options;
        
        if(!is_array($options))
            throw new Exception("Field::options value must be an array");
            
        $this->options = $options;
        return $this;
    }
    
}

?>
