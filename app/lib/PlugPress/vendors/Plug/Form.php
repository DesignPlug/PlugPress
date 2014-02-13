<?php namespace Plug;

 class Form {
     
    static $input_wrapper = '<tr class="form-field"><th scope="row">{label}</th>
                             <td><span class="error">{error}</span> {input}<span class="help">{help}</span></td></tr>';
   
    static $input_vars = array('help', 
                               'label',
                               'input');
    
    
    static function input(\Plug\Field $field, $attr = array(), $wrap = true){ 
        
        $attr['name']   = $name = $field->name();
        $attr['type']   = $type    = $field->type();
        $label          = ($title = $field->title())  ?  $title : str_replace($field->_namespace(), "", $name); 
        $help           = ($h = @$attr['help'])       ?  $h : null;
        $id             = isset($attr['id'])          ?  $attr['id'] : $attr['id'] =  $field->name(); 
        $value          = isset($attr['value'])       ?  $attr['value'] : $attr['value'] = $field->value() ?: $attr['value'] = $field->defaultValue();
        $desc           = $field->description();
        $type           = $field->type();
        $label          = self::label($field->title(), $id);
        
        //if type is select  multiple strip the -multiple  from the string
        //and create multiple attr
        
        if($type === "select-multiple"){
               $type =  "select";
               $attr['multiple'] = "multiple";
        }
        else if($type !== "textarea"){
                $type = "input";
        }
        
        //get input markup 
        
        $mkup = call_user_func(array('\Plug\Form', $type .'HTML'), $attr);       
        
        //return wrapped element, if wrap is true
        
       if($wrap)
            return self::wrap($field, $mkup, ['help'=>$help, 'label' => $label, 'input' => $mkup]);
        
        //else return mark up without wrapper
        
        else
            return $mkup;
    }
    
    static function inputHTML($attr){
        return "<input " .self::attr_to_string($attr) ." />";
    }
    
    
    static function TextAreaHTML($attr){
        return "<textarea " .self::attr_to_string($attr, ['value', 'help']) .">" .@$attr['value']  ."</textarea>";
    }
    
    static function selectHTML($options, $attr){
        $mkup = '<select ' .self::attr_to_string($attr, ['value', 'help']) .'>' . self::optionHTML($attr['value']);

         foreach($options as $opt){
             if($opt->name() === $attr['value']->name())  continue;
             $mkup .= self::optionHTML($opt);
         }
         
         $mkup .= '</select>';
    }
    
    static function optionHTML($option){
          return    ' <option value="' .($option->value() ? $option->value() : $option->name()) .'"> ' .$option->title()  .'</option>';
    }
     
     static function wrap($field, $input, array $vars){
         
         if(self::$input_wrapper){               
             
             $wrapper = self::$input_wrapper;
             
             //if error  var not set create default handler
             self::$input_vars['error'] = function($fld){
                 return @implode(" ", Session::getFlash($fld->name() ."_error"));
             };
                 
             foreach(self::$input_vars as $k => $v){
                 
                 if(!is_numeric($k)){
                     if(isset($vars[$k]))
                        $rpl = $vars[$k];
                     else
                         $rpl = $v;
                 }
                 else
                 {
                     $k = $v;
                     
                     if(isset($vars[$v]))
                         $rpl = $vars[$v];
                     else
                         $rpl = " ";
                 }  
                 
                 if(is_callable($rpl)){
                     $wrapper = preg_replace( '/{([\s]*'.$k .'[\s]*)}/', call_user_func($rpl, $field), $wrapper);
                 } else {
                     $wrapper = preg_replace( '/{([\s]*'.$k .'[\s]*)}/', $rpl, $wrapper);
                 }
                 
             }
         }
         
         return $wrapper;
     }
     
     static function label($text, $id){
         return             '<label for="' .$id .'">' .$text .'</label>';
     }
     
     static function attr_to_string($attr, $ignore = array('help')){
         $string = "";
         foreach($attr as $k => $v){
             if(is_array($v)){
                    $string .= self::attr_to_string($v);
             }else{
                    if(!in_array($k, $ignore)){
                            $string .= $k;
                            $string .=  isset($v) ? "=" .$v ." " : " ";
                    }
             }
         }
         return $string;
     }
     
}

?>
