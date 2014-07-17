<?php namespace Plugpress\Models;

use PlugPress\Plugpress as Plugpress;

class PostMeta extends Meta{

    protected $primaryKey = 'meta_id';
    
    function __construct($attributes = array()){
        $this->table = Plugpress::DB()->prefix.'postmeta';
        parent::__construct($attributes);
    }
    
    function post(){
        return $this->belongsTo("\WPMVC\Framework\Models\Post", "ID");
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


