<?php namespace Plugpress\Posttypes;

use \Plugpress\Posttype as Posttype;


class Post extends Posttype{
    
    protected $name = "Post";
    
    function init(){
        $Post = $this;
        add_action("init", function() use ($Post){
            add_action("add_meta_boxes", array($this, "load_meta_boxes"));
        });
    }
    
}

?>
