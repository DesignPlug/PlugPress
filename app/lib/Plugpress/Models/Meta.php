<?php namespace Plugpress\Models;

use PlugPress\Plugpress as Plugpress;

abstract class Meta extends \WPMVC\Framework\Model{
    
    function __get($name){
        if(isset($this->attributes['meta_key']) && $name === $this->attributes['meta_key']){
            return $this->attributes['meta_value'];
        }
        return parent::__get($name);
    }
    
    function __set($name, $value){
        if(isset($this->attributes['meta_key']) && $name === $this->attributes['meta_key']){
            return $this->attributes['meta_value'] = $value;
        }
        return parent::__set($name, $value);
    }
    
    function save($options){
        $options['validate'] = true;
        return parent::save($options);
    }
    
}
?>


