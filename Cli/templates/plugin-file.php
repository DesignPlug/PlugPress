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

if(Plugpress::is_activated()){
    APP::init("{plugin_name}", "{plugin_file_name}", "{plugin_namespace}");     
}

?>