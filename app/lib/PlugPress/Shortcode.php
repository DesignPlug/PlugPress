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
        if($this->recur == true) $param[1] = do_shortcode($param[1]);
        
        if(count($this->atts) > 0){
            $param[0] = shortcode_atts($this->atts, $param[0], $this->tag);
        }
        
        return call_user_func_array($callback, $param);
    }
    
    
    
    
    
    
    
}

?>
