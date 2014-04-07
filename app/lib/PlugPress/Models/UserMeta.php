<?php namespace Plugpress\Models;

use PlugPress\Plugpress as Plugpress;

class UserMeta extends Meta{

    protected $primaryKey = 'umeta_id';
    
    function __construct($attributes = array()){
        $this->table = Plugpress::DB()->prefix.'usermeta';
        parent::__construct($attributes);
    }
    
    function user(){
        return $this->belongsTo("\WPMVC\Framework\Models\Post", "ID");
    }
    
    function save($options){
        $options['validate'] = true;
        return parent::save($options);
    }
  
    function add_rules_to($validator){
        $validator->rule('required', array('meta_key', 'umeta_id', 'user_id'));
    }
    
    function setUmetaIdAttribute($value){
        trigger_error("Cannot change user meta's umeta_id", E_USER_ERROR);
    }    
    
}
?>


