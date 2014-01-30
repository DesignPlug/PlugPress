<?php namespace Plug;

class HTTP {
    
    static function setRequestVars(Fields $fields, $request = "REQUEST"){
        
        switch(strtoupper($request)){
            
            case "POST":
                $request = $_POST;
            break;
            case "REQUEST":
                $request = $_REQUEST;
            break;
            default:
                $request = $_GET;
            break;
        }
        
        foreach($fields as $fld){
            if(isset($request[$fld->name()])){
                $fld->value($request[$fld->name()]);
            }
        }
        
    }
    
    static function setPostVars(Fields $fields){
        return self::setRequestVars($fields, "POST");
    }
    
    static function setGetVars(Fields $fields){
        return self::setRequestVars($fields, "GET");
    }    
    
}

?>
