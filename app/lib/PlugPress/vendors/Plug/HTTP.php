<?php namespace Plug;

class HTTP {
    
    static function setRequestVars(\Iterator $fields, $request = "REQUEST"){
        
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
        
        foreach($fields as $name => $val){
            if(isset($request[$name])){
                $fields[$name] = $request[$name];
            }
        }
        
    }
    
    static function setPostVars(\Iterator $fields){
        return self::setRequestVars($fields, "POST");
    }
    
    static function setGetVars(\Iterator $fields){
        return self::setRequestVars($fields, "GET");
    }    
    
}

?>
