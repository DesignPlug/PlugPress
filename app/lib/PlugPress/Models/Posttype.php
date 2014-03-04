<?php namespace Plugpress\Models;

abstract class Posttype extends \WPMVC\Framework\Models\Post{
    
    function __construct($attributes = array()){
        $cls = get_called_class();
        $this->attributes['post_type'] = $cls::$post_type;
        parent::__construct($attributes);
    }
    
    public function setPostTypeAttribute($value){
        $cls = get_called_class();
        trigger_error("Cannot change posttype: " .$cls::$post_type, E_USER_ERROR);
    }
    
    static function create(array $attributes){
        $cls = get_called_class();
        $attributes['post_type'] = $cls::$post_type;
        return parent::create($attributes);
    }
    
    static function all($columns = array('*')){
        $cls = get_called_class();
        return parent::where('post_type', '=', $cls::$post_type)->get($columns);
    }
    
    static function get_posts(array $columns = array('*')){
        return self::posts()->get($columns);
    }
    
    static function posts(){
        $cls = get_called_class();
        return parent::where('post_status', '=', 'publish')->where('post_type', '=', $cls::$post_type);
    }
    
    function save(array $options){
        return parent::save($options);
    }
}

?>
