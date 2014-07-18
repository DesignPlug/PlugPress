<?php namespace Plugpress;

class Shortcode {
    
    protected $callback, 
              $tag, 
              $recur, 
              $atts = array();
    
    public static function __callStatic($fn, $param){
        $call = $fn."_shortcode";
        if(!function_exists($call)){
            $call = "shortcode_" .$fn;
            if(!function_exists($call)){
                throw new \BadMethodCallException("call to undefined Shortcode method " .$fn);
            }
        }
        return call_user_func_array($call, $param);
    }
    
    public static function add($tag, $callback, $param = array(), $recur = false){
        add_shortcode($tag, array(new Shortcode($tag, $callback, $param, $recur), "callBack"));
    }
    
    function __construct($tag, $callback, $atts = array(), $recur = false){
        $this->tag      = $tag;
        $this->callback = $callback;
        $this->recur    = $recur;
        $this->atts     = !empty($atts) ? $atts : array();
    }
    
    function callBack($param, $content){
        $callback = parse_callable($this->callback);
        
        $param = func_get_args();
        if($this->recur == true){
            
            //don't allow anything to be outputted directly from here
            //otherwise content that was intended to be inside current 
            //shortcode may be echoed to the view BEFORE and outside it's container
            
            ob_start();
                $param[1] = do_shortcode($param[1]);
            $content = ob_get_clean();
            
            //if content was echoed instead of returned, we'll
            //set that content as the second param else we'll 
            //just use the returned content
            
            if(trim($content) !== ""){
                $param[1] = $content;
            }
        }
        
        if(count($this->atts) > 0){
            $param[0] = shortcode_atts($this->atts, $param[0], $this->tag);
        }
        
        return call_user_func_array($callback, $param);
    }
    
    
    
    
    
    
    
}

?>
