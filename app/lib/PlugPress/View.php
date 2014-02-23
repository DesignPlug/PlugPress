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
    
    function add($key, $value = null){
        if(is_array($key)){
            foreach($key as $k => $v){
                $this->view_data[$k] = $v;
            }
            return $this;
        }
        
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
            $dir     = $this->template_dir;
            $ViewObj = $this;
            return $this->add($key, new \Plug\_closure(function() use ($dir, $view, $ViewObj) {
                            $sep = DIRECTORY_SEPARATOR;
                            $data = $ViewObj->getData();
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
    
    function json_render($return = false){
        if(isset($this->file)){
            
            if($return){
                ob_start();
                $this->render();
                return ob_get_clean();
            }
            $this->render();
        } else {
            $data = json_encode($this->view_data);
            if($return){
                return $data;
            }
            echo $data;
        }
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
        Views::setData($this->view_path($this->file, '.php'), $this->getData());
        return $this->getTemplate($this->file);
    }
    
    function view_path($path = "", $ext = ""){
        return Plugpress::DS($this->template_dir ."\\" .$path, $ext);
    }
    
    function loadTemplateStyles($template = null) {
        if(is_callable($this->scripts_callback))
            call_user_func ($this->scripts_callback, $this->scripts_object);
    }
    
    
}

?>
