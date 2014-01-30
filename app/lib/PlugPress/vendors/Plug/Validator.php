<?php namespace Plug;

    //checks validity given fields,
    //and sets the field value to filtered version
    //if successful and returns true, else sets flash error message 
    //if flash is true, and returns false

class Validator extends \GUMP implements Interfaces\Resetable{

    use traits\Instantiate;
    
    public $setFlash = true;
    protected $fieldErrors = array(),
              $validFields = array();
    
    
    function __construct()
    {
        $param = func_get_args();
        if(count($param) > 0) $this->validate($param[0]);
    }
    
    function validate(){
        $param = func_get_args();
        
        //if first given param is not an instance of Plug\Field, treat as the normal validate method
        
        if(!($param[0] instanceof Fields)){
            return call_user_func_array([parent, "validate"], $param);
        }
        
        //else validate each given field
        
        //clear valid fields and errors array
        $this->reset();
        
        foreach($param[0] as $field){
            
            if($field->validation_rules()){
 
                $result =   parent::is_valid( [$field->title() => $field->value()], 
                                              [$field->title() => $field->validation_rules()]);
                
                
                if($result === true){
                    //if field validates filter field value if filter is given

                    if($field->filter_rules()){
                        $r = self::filter_input([$field->name() => $field->value()], 
                                                [$field->name() => $field->filter_rules()]);

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
