<?php

use PlugPress;

/*
Plugin Name: {The Name of Your Plugin}
Plugin URI: { The Plugin Url }
Description: {Replace with the Description of Your Plugin} 
Version: { Replace with the version of your plugin }
Author: { Replace with your name }
Author URI: { Replace with Your URI }
License: { Replace }
*/

if(!defined("PLUGPRESS")){
    require "app/lib/Plugpress/Plugpress.php";
}

//Plugins are only initiated if Plugpress is activated

Plugpress::run(function(){
   APP::init("{plugin_name}", "{plugin_file_name}", "{plugin_namespace}");
   
}) or add_action('admin_notices', function(){
    
   PlugPress::error_notice("<strong>There was an error initializing PlugPress dependent plugin: 
                           '{plugin_file_name}'.</strong><br/> 
                           1) Make sure Plugpress is installed and activated. <br/>
                           2) If Plugpress is already activated, <a href='" .$_SERVER['PHP_SELF'] ."'>try refreshing</a>");
});

?>