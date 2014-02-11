<?php require "Cli/GeneratePlugin.php";

    fwrite(STDOUT, "Name of Plugin File?");
    
    $plugin_file_name = trim(fgets(STDIN));
    
    fwrite(STDOUT, "Name of Plugin?");

    $plugin_name = trim(fgets(STDIN));
    
    fwrite(STDOUT, "Plugin Namespace?");
    
    $plugin_namespace = trim(fgets(STDIN));    
    
    $gen = new PluginGenerator($plugin_name, $plugin_file_name, $plugin_namespace);
    $gen->create();
    
    
    
/*
if(trim($rsp) == 'y'){
    fwrite(STDOUT, "Damn Right!");
} else {
    fwrite(STDOUT, "");    
}
*/

    
    

?>
