<?php namespace Plugpress;

class _closure{

    protected $closure;

    function __construct($callable){
         $this->closure = $callable;
    }

    function __toString(){
         return @(string) call_user_func($this->closure);
    }

}


?>