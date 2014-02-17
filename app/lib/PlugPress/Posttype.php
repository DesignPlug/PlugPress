<?php namespace Plugpress;


use \Plug\Validator as Validator;
use \Plug\traits\Instantiate as Instantiate;
use \Plug\traits\Gettable as Gettable;
use \Plug\traits\Fields as Fields;
use \Plug\HTTP as HTTP;

abstract class Posttype{ 
    
    static protected $registered_posttypes,
                     $registered_posttypes_class;

    
    protected $name,
              $labels = array(),
              $register_param,
              $namespace,
              $fields;
    
    function __toString()
    {
        return $this->name;
    }
    
    static function __callStatic($name, $param)
    {
        $posttype = get_called_class();
        $posttype = $posttype::get();
        if($posttype){
            return call_user_func_array(array($posttype, $name), $param);
        }
        throw new \Exception("cannot call ::" .$name ." on non registered posttype. 
                              Must register first eg." .get_called_class() ."::create()");
    }
    
    function __get($var){
        return @$this->$var;
    }
    
    static function getInstance(){
        $class = get_called_class();
        return new $class;
    }    

    function Fields($namespace = "")
    {
        $this->namespace = $this->namespace ?: $namespace;
        
        if(!isset($this->fields)){
            $this->fields = new \Plug\Fields($this->namespace);
        }
        return $this->fields;
    } 

    static function create()
    {      
       //instantiate the posttype 
       $posttype_class_name = get_called_class();
       $posttype            = $posttype_class_name::getInstance();
       
       //register and init, if not registered
       if(!isset(self::$registered_posttypes[$posttype->name])){

           //init
           $posttype->init();
           
           //register
           self::$registered_posttypes[$posttype->name] = $posttype_class_name;
           self::$registered_posttypes_class[$posttype_class_name] = $posttype->name;
           
       } else {
           throw new \Exception("cannot create post type '" .$posttype->name ."' twice");
       }
    }
    
    static function get_posttype($type){
        $posttype_class = @self::$registered_posttypes[$type];
        return isset($posttype_class) ? $posttype_class::getInstance() : null;
    }
    
    static function get($id = null){
        $posttype_name = @self::$registered_posttypes_class[get_called_class()] ?: null;
        
        if(isset($posttype_name)){
            $posttype = self::get_posttype($posttype_name);
            if(isset($id)){
                $posttype->setPostMeta($id);
            }
            return $posttype;
        }
        return null;
    } 
    
    static function updatePostMeta($id, $fields)
    {
        foreach($fields as $field){
                //only save field value if (a) field is not same as default and (b) field has value
                if(trim($field->value()) != trim($field->defaultValue()) && trim($field->value()) != "")
                    update_post_meta($id, $field->name(), $field->value());
        }
    }    
    
    function init(){
         //if no label name given use pluralized posttype name
         if(!isset($this->labels['name'])) 
             $this->labels['name'] = _x(\Inflector::titleize(\Inflector::pluralize($this->name)), 'Post Type General Name');

         //if no singular name given, use singularized @label['name']
         if(!isset($this->labels['singular_name']))
             $this->labels['singular_name'] = _x(\Inflector::singularize($this->labels['name']), 'Post Type Singular Name');

         //if heirarchical yet no parent item colon is given set it
         if(@$this->register_param['hierarchical'] === true && !isset($this->labels['parent_item_colon']))
             $this->labels['parent_item_colon'] = __("Parent " .$this->labels['singular_name'] .':');

         if(!isset($this->labels['edit_item'])) 
             $this->labels['edit_item'] = __("Edit " .$this->labels['singular_name']);

         if(!isset($this->labels['new_item']))         
             $this->labels['new_item'] = __("New " .$this->labels['singular_name']);

         if(!isset($this->labels['view_item']))         
             $this->labels['view_item'] = __("View " .$this->labels['singular_name']);        

         if(!isset($this->labels['search_item']))         
             $this->labels['search_item'] = __("Search " .$this->labels['name']);         

         if(!isset($this->labels['not_found'])) 
             $this->labels['not_found'] = __("No " .$this->labels['name'] ." found");

         if(!isset($this->labels['not_found_in_trash'])) 
             $this->labels['not_found_in_trash'] = __("No " .$this->labels['name'] ." found in Trash"); 


         if(!isset($this->labels['add_new_item'])) 
             $this->labels['add_new_item'] = __("Add " .$this->labels['singular_name']);           

         //register param defaults      

         if(!isset($this->register_param['supports']))
             $this->register_param['supports'] = array('title',
                                                      'editor',
                                                      'thumbnail',
                                                      'revisions');

         if(!isset($this->register_param['public']))
             $this->register_param['public'] = true;

         //if no meta box callback is given, set it with the load_meta_boxes method
         if(!isset($this->register_param['register_meta_box_cb']))
             $this->register_param['register_meta_box_cb'] = array($this, 'load_meta_boxes');

         $this->register_param['labels'] = $this->labels;

         $posttype     = $this->name;
         $param        = $this->register_param;
         $called_class = $this;
         
         add_action("init", function() use($posttype, $param, $called_class){
             
            register_post_type( $posttype, $param );
            add_action("save_post", array($called_class, "update"));
            add_action("delete_post", array($called_class, "delete"));
            
         });
    }
    
    function load_meta_boxes()
    {
        $post = $this;
        add_meta_box($this->name ."_meta_box",  
                     $this->labels['name'] ." meta",
                     function() use($post){
            
                            $post->setPostMeta(get_the_ID());
                            
                            $form_inputs = "";
                            
                            if($flds = $post->Fields()){
                                foreach($flds as $field){
                                    $form_inputs .= \Plug\Form::input($field);
                                }
                            }
                            echo "<table class='form-table'>" .$form_inputs ."</table>";
                     }, 
                     $post->name);
    }

    function update() 
    {       
        //update only if  posttype matches
        if(strtolower($this->name) == $_POST['post_type']) 
        {
            //set custom fields with posted value
            HTTP::setPostVars($this->Fields());
            
            $id = $_POST['post_ID'];
            
            $this->save($id);
        }
    }
    
    function save($id, $values = null)
    {
        if(is_array($values)){
            //convert array values into fields
            $this->Fields()->arrayToFields($values, $this->Fields());
        }
        
        //validate fields
        $v = new Validator($this->Fields());

        //call validate method for additional custom validation
        $result = $this->validate($v->getValidFields(), $v->getErrors());

        //call validate method for additional custom validation
        $result = $this->validate($v->getValidFields(), $v->getErrors());

        //if result is null only update valid fields
        if($result === null){
            self::updatePostMeta($id, $v->getValidFields());
        } else if($result === true) {
            self::updatePostMeta($id, $this->Fields());
        }
    }
    
    function setPostMeta($id){
        
        $meta = get_post_meta($id);
        foreach($this->Fields() as $field){
            if(isset($meta[$field->name()]))
                $field->value($meta[$field->name()][0]);
        }
    }
    
    function validate($valid_fields, $error_fields){
        
        //override this method to handle additional validation, 
        //and field comparison/dependecy issues, 
        //return false to cancel update, true to update all fields including
        //error fields, null to update only  
        return null;
    }
    
    function delete()
    {
        //override this method
    }
    
}

?>
