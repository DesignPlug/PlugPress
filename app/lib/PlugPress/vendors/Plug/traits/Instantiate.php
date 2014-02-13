<?php namespace Plug\traits;


trait Instantiate {
    static function getInstance(){
        $class = get_called_class();
        return new $class;
    }
}

?>
