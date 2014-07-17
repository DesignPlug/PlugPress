<?php namespace Plug;

    //checks validity given fields,
    //and sets the field value to filtered version
    //if successful and returns true, else sets flash error message 
    //if flash is true, and returns false

class Validator implements Interfaces\Resetable{
    
    public $setFlash = true;
    protected $fieldErrors = array(),
              $validFields = array(),
              $GUMP;
    
    
    function __construct()
    {
        $param = func_get_args();
        $GUMP = new \GUMP;
        if(count($param) > 0) $this->validate($param[0]);
    }
    
    static function getInstance(){
        $class = get_called_class();
        return new $class;
    }
    
    function validate(){
        $param = func_get_args();
        
        //if first given param is not an instance of Plug\Field, treat as the normal validate method
        
        if(!($param[0] instanceof Fields)){
            return call_user_func_array(array($this->GUMP, "validate"), $param);
        }
        
        //else validate each given field
        
        //clear valid fields and errors array
        $this->reset();
        
        foreach($param[0] as $field){
            
            if($field->validation_rules()){
 
                $result =   $this->GUMP->is_valid( array($field->title() => $field->value()), 
                                                   array($field->title() => $field->validation_rules()));
                
                
                if($result === true){
                    //if field validates filter field value if filter is given

                    if($field->filter_rules()){
                        $r = $this->GUMP->filter_input(array($field->name() => $field->value()), 
                                                       array($field->name() => $field->filter_rules()));

                        $field->value($r[$field->name()]);
                    }

                    //store valid fields
                    $this->validFields[$field->name()] = $field;
                    
                } else {
                
                    //store field errors
                    $this->fieldErrors[$field->name()] = $result;

                    //create error flash if set flash is true
                    if($this->setFlash === true){
                        Session::createFlash($field->name() ."_error", $result);
                    }
                }
                
                
            } else {
                $this->validFields[$field->name()] = $field;                
            }

        }
        
        return $this;
    }
    
    function getErrors(){
        return $this->fieldErrors;
    }
    
    function getValidFields(){
        return $this->validFields;
    }
    
    function reset(){
        $this->fieldErrors = array();
        $this->validFields = array();
    }
    
    
}

?>
