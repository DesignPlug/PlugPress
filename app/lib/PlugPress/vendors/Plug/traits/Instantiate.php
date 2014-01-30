<?php namespace Plug\traits;


trait Instantiate {
    static function getInstance(){
        $class = __CLASS__;
        return new $class;
    }
}

?>
