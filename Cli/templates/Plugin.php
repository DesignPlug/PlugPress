<?php namespace {plugin_name};

use \Plugpress\Plugpress as Plugpress;
use \Plugpress\Route as Route;


class Plugin extends \Plugpress\Plugin{

    var $register_autoload = false;
 
    function init() {
        
        //any initialization code
        //ideal for posttype creation etc...
    }
    
    function route(){
        
        //put your routes in here
        
    }
    
    
    function activate() {
        //code to run when plugin is activated
    }
    
    function uninstall() {
        //code to run when plugin is uninstalled
    }
    
    function deactivate() {
        //code to run when plugin is deactivated
    }
    
    function autoload()
    {
        //assign custom autloader 
    }
    
}
?>
