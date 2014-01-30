<?php namespace Plugpress;

class View extends CustomTemplate{

    protected $view_data = array(),
              $scripts_callback,
              $scripts_object,
              $file;
    
    function __construct($view_dir, Scripts $scripts_object){
        $this->template_dir = $view_dir;        
        $this->scripts_object = $scripts_object;
    }
    
    function add($key, $value){
        $this->view_data[$key] = $value;
        return $this;
    }
    
    function remove($key){
        unset($this->view_data[$key]);
        return $this;
    }
    
    function nest($key, $view){
        
        if($view instanceof \Plugpress\View){
            return $this->add($key, $view);
        } else {
            $dir = $this->template_dir;
            return $this->add($key, new \Plug\_closure(function() use ($dir, $view) {
                            $sep = DIRECTORY_SEPARATOR;
                            include rtrim($dir, $sep) .$sep .str_replace(array("/","\\"), $sep, $view) .".php";
                          }));   
        }
    }
    
    function getData(){
        return $this->view_data;
    }
    
    function scripts($callable_scripts){
        if(!is_callable($callable_scripts)){
            throw new \InvalidArgumentException("View::scripts expects a callable callback as the first argument");
        }
        $this->scripts_callback = $callable_scripts;
    }
    
    function wp_render($type = "include"){
        $this->redirect     = ($type === "redirect") ? true : false; 
        $this->real_include = false;
        if(!$this->redirect)
            add_filter ("template_include", array($this, "templateInclude"));
        else 
            return $this->templateInclude($this->file);
    }
    
    function render(){     
        $this->redirect     = false;
        $this->real_include = true;
        $this->templateInclude();
    }
    
    function view($file){
        $this->file = $file;
        return $this;
    }
    
    function __toString() {
        $this->render();
        return "";
    }
    
    function templateInclude($template = null)
    {
        return $this->getTemplate($this->file);
    }
    
    function loadTemplateStyles($template = null) {
        if(is_callable($this->scripts_callback))
            call_user_func ($this->scripts_callback, $this->scripts_object);
    }
    
    
}

?>
