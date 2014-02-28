<?php namespace Plugpress;


class Model  extends \WPMVC\Framework\Model{
    
    
    static function set_rules(\Valitron\Validator $validator){
        return $validator;
    }
}

?>
