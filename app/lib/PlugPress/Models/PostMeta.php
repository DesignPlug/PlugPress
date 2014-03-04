<?php namespace PlugPress\Models;

use PlugPress\Plugpress as Plugpress;

class PostMeta extends \WPMVC\Framework\Model{

    protected $primaryKey = 'meta_id';
    
    function __construct($attributes = array()){
        $this->table = Plugpress::DB()->prefix.'postmeta';
        parent::__construct($attributes);
    }
    
    function __get($name){
        if($name === $this->meta_key){
            return $this->meta_value;
        }
        return parent::__get($name);
    }
    
    function __set($name, $value){
        if($name === $this->meta_key){
            return $this->meta_value = $value;
        }
        return parent::__set($name, $value);
    }
    
    function post(){
        return $this->belongsTo("\WPMVC\Framework\Models\Post", "ID");
    }
    
    function save($options){
        $options['validate'] = true;
        return parent::save($options);
    }
  
    function add_rules_to($validator){
        $validator->rule('required', array('meta_key', 'meta_id', 'post_id'));
    }
    
    function setPostIdAttribute($value){
        trigger_error("Cannot change post meta's post_id", E_USER_ERROR);
    }
    
    function setMetaIdAttribute($value){
        trigger_error("Cannot change post meta's meta_id", E_USER_ERROR);
    }    
    
}
?>


