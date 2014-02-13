<?php namespace PlugPress\Models;



class PostMeta extends \WPMVC\Framework\Model{

    protected $primaryKey = 'meta_id';
    
    function __get($key){
        if( (trim($this->meta_key) == trim($key)) || 
            (trim($this->meta_key) == trim($key)) ){
            
        }
    }
    
    function post(){
        return $this->belongsTo("\WPMVC\Framework\Models\Post", "ID");
    }
    
    function save($options){
        if(!isset($options['post_id'])) {
            $options['post_id'] = $this->post->ID;
        }
        $options['validate'] = true;
        return parent::save($options);
    }
    
    function add_rules_to($validator)
    {
        $validator->rule('required', 'meta_key');
    }
}
?>


