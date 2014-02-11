<?php

function DS($path){
    return str_replace(["/","\\"], DIRECTORY_SEPARATOR, $path);
}

class PluginGenerator {
    
    protected $folder_name,
              $file_name,
              $namespace,
              $path,
              $root_dir;
    
    function __construct($plugin_folder_name, $plugin_file_name, $plugin_namespace){
        $this->folder_name = $plugin_folder_name;
        $this->file_name   = basename($plugin_file_name, ".php");
        $this->namespace   = $plugin_namespace;
        $this->root_dir    = DS(getcwd() ."\Cli");
        $this->plugin_root = DS(dirname(getcwd()) ."/" .$this->file_name);
        $this->public_path = DS($this->plugin_root ."/public");
        $this->path        = DS($this->plugin_root ."/app/plugin/" .$this->folder_name);
        $this->bootstrap();
    }
    
    function create(){
        //create folders
        $this->generateControllersDir();
        $this->generateModelsDir();
        $this->generateViewsDir();
        $this->generatePublicDir("css");
        $this->generatePublicDir("js");
        $this->generatePublicDir("vendors");
    } 
    
    function generateControllersDir(){
        $ctrl_path = DS($this->path ."/Controllers");
        
        //create directory if not exists
        $this->generateDir("Controllers");
        
        //create base controller if not exists
        $baseController = $ctrl_path ."/BaseController.php";
        if(!file_exists($baseController)){
            
             //get and parse controller template
             $ctrl_mkup = $this->getTemplate("BaseController");
             $ctrl_mkup = $this->parse(["plugin_name" => $this->folder_name, 
                                        "plugin_namespace" => $this->namespace], 
                                        $ctrl_mkup);
             
             //create base controller
             $this->generateFile("Controllers/BaseController.php", $ctrl_mkup);
             
        }
    }
    
    function generateDir($path, $dir = "app"){
        
        if($dir === "app"){
            $path = $this->getPath($path);
        } else if($dir === "public") {
            $path = $this->getPublicPath($path);
        }
        
        if(!is_dir($path)){
            @mkdir(DS($path), 0777, true);
        }        
    }
    
    function generateFile($file, $content, $path = null){
        $path = $path ? DS($path ."\\" .$file) : $this->getPath($file);
        if(!file_exists($path)){
            return file_put_contents($path, $content);
        }
    }
    
    function generateViewsDir(){
        $this->generateDir("Views");
    }
    
    function generateModelsDir(){
        $this->generateDir("Models");
    }
    
    function generatePublicDir($path = ""){
        $this->generateDir($path, "public");
    }
    
    function getTemplate($template_name){
        return file_get_contents(DS($this->root_dir ."\\templates\\" .$template_name .".php"));
    }
    
    function getPath($file){
        return DS($this->path ."\\" .$file);
    }
    
    function getPublicPath($file){
        return DS($this->public_path ."\\" .$file);
    }
    
    function bootstrap(){
        $this->generateDir($this->path);
        
        /* gen plugin bootstrap file */
        
        $bootstrap = $this->getTemplate("Plugin");
        $bootstrap = $this->parse(["plugin_name" => $this->folder_name], $bootstrap);
        $this->generateFile("Plugin.php", $bootstrap);
        
        /* gen plugin init file */
        
        $plugin_file = $this->getTemplate("plugin-file");
        $plugin_file = $this->parse(['plugin_name'      => $this->folder_name, 
                                     'plugin_namespace' => $this->namespace,
                                     'plugin_file_name' => $this->file_name],
                                     $plugin_file);
        
        $this->generateFile($this->file_name .".php", $plugin_file, $this->plugin_root);
    }
    
    function parse($param, $content){
        foreach($param as $k => $v){
            $content = preg_replace("/{([\s]*".$k."[\s]*)}/", $v, $content);
        }
        return $content;
    }
    
    
}

?>
+