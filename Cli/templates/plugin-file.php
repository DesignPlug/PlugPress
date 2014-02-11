<?php

/*
Plugin Name: {The Name of Your Plugin}
Plugin URI: { The Plugin Url }
Description: {Replace with the Description of Your Plugin} 
Version: { Replace with the version of your plugin }
Author: { Replace with your name }
Author URI: { Replace with Your URI }
License: { Replace }
*/

add_action("plugins_loaded", function(){
   PlugPress\APP::init("{plugin_name}", "{plugin_file_name}", "{plugin_namespace}"); 
});

?>