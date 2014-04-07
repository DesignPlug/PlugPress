<?php namespace Plugpress;

abstract class Controller {
    
    protected $View, $ajaxOnly, $plugin, $loginRequired;
    
    function __get($var){
        return @$this->$var;
    }
    
    function get404(){
        if(!Plugpress::DB()->is_404){
            HTTP::setHeaderStatus(404);
            return $this->View->view("404.php")->wp_render();
        }
    }
    
    function requireLogin(){
        if(!is_user_logged_in()){
            $this->get404();
        }
    }
    
    function getView($path = false){
        $plugin = $this->plugin;
        $path = $path ?: $plugin::VIEWS_DIR();
        return new View($path, $plugin::Scripts());
    }
    
    
}

?>
