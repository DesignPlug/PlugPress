<?php namespace Plug\traits;

trait FieldsStatic {

    static protected $namespace,
                     $fields;
    
    static function Fields($namespace = "")
    {
        self::$namespace = $namespace;
        
        if(!isset(self::$fields)){
            self::$fields = new \Plug\Fields($namespace);
        }
        return self::$fields;
    }    
    
}

?>
