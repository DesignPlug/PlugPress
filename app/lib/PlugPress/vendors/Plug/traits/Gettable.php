<?php namespace Plug\traits;

trait Gettable{
   
    function __get($var){
        return @$this->$var;
    }
    
}

?>
