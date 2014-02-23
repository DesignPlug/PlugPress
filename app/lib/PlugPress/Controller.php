<?php namespace Plugpress;

abstract class Controller {
    
    protected $View, $ajaxOnly;
    
    function __get($var){
        return @$this->$var;
    }
    
    function get404(){
        if(!Plugpress::DB()->is_404){
            HTTP::setHeaderStatus(404);
            return $this->View->view("404.php")->wp_render();
        }
    }
    
    
}

?>
