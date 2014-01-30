<?php namespace PlugPress;

use \Plug\Session as Session;
use \Plug\traits\FieldsStatic as Fields;
use \Plug\Validator as Validator;
use \Plug\HTTP as HTTP;

abstract class Post implements Crud{

    use Fields;    
    
    static protected $posttype,
                     $labels = array(),
                     $register_param = array(),
                     $namespace;
    
    
    static function create()
    {
        //set default param and register posttype
        
        //if no label name given use pluralized posttype name
        if(!@self::$labels['name']) 
            self::$labels['name'] = _x(\Inflector::titleize(\Inflector::pluralize(self::$posttype)), 'Post Type General Name');

        //if no singular name given, use singularized @label['name']
        if(!isset(self::$labels['singular_name']))
            self::$labels['singular_name'] = _x(\Inflector::singularize(self::$labels['name']), 'Post Type Singular Name');
        
        //if heirarchical yet no parent item colon is given set it
        if(@self::$register_param['hierarchical'] === true && !isset(self::$labels['parent_item_colon']))
            self::$labels['parent_item_colon'] = __("Parent " .self::$labels['singular_name'] .':');
        
        if(!isset(self::$labels['edit_item'])) 
            self::$labels['edit_item'] = __("Edit " .self::$labels['singular_name']);
        
        if(!isset(self::$labels['new_item']))         
            self::$labels['new_item'] = __("New " .self::$labels['singular_name']);
        
        if(!isset(self::$labels['view_item']))         
            self::$labels['view_item'] = __("View " .self::$labels['singular_name']);        

        if(!isset(self::$labels['search_item']))         
            self::$labels['search_item'] = __("Search " .self::$labels['name']);         
        
        if(!isset(self::$labels['not_found'])) 
            self::$labels['not_found'] = __("No " .self::$labels['name'] ." found");

        if(!isset(self::$labels['not_found_in_trash'])) 
            self::$labels['not_found_in_trash'] = __("No " .self::$labels['name'] ." found in Trash"); 

        
        if(!isset(self::$labels['add_new_item'])) 
            self::$labels['add_new_item'] = __("Add " .self::$labels['singular_name']);           
        
        //register param defaults      
        
        if(!isset(self::$register_param['supports']))
            self::$register_param['supports'] = array('title',
                                                     'editor',
                                                     'thumbnail',
                                                     'revisions');
        
        if(!isset(self::$register_param['public']))
            self::$register_param['public'] = true;
        
        //if no meta box callback is given, set it with the load_meta_boxes method
        if(!isset(self::$register_param['register_meta_box_cb']))
            self::$register_param['register_meta_box_cb'] = array(get_called_class(), 'load_meta_boxes');
        
	self::$register_param['labels'] = self::$labels;
        
        $posttype     = self::$posttype;
        $param        = self::$register_param;
        $called_class = get_called_class();
        
        add_action("init", function() use($posttype, $param, $called_class){
            
           register_post_type( $posttype, $param ); 
           
           add_action("save_post", [$called_class, "update"]);
           
        });
    }
    
    static function load_meta_boxes()
    {
        $post = get_called_class();
        add_meta_box(self::$posttype ."_meta_box",  
                     self::$labels['name'] ." meta",
                     function() use($post){
            
                            $post::setPostMeta(get_the_ID());
                            
                            $form_inputs = "";
                            
                            if($flds = $post::Fields()){
                                foreach($flds as $field){
                                    $form_inputs .= \Plug\Form::input($field);
                                }
                            }
                            echo "<table class='form-table'>" .$form_inputs ."</table>";
                     }, 
                     self::$posttype);
    }
    
    //run this function when the post is being viewed
    static function read(){}

    static function update() 
    {
        //update only if  posttype matches
        if(strtolower(self::$posttype) == $_POST['post_type']) 
        {
            //set custom fields with posted value
            HTTP::setPostVars(self::Fields());

            //now time to validate fields
            $v = new Validator(self::Fields());
            
            $id = $_POST['post_ID'];
            
            //call validate method for additional custom validation
            $result = self::validate($v->getValidFields(), $v->getErrors());
            
            //if result is null only update valid fields
            if($result === null){
                self::updatePostMeta($id, $v->getValidFields());
            } else if($result === true) {
                self::updatePostMeta($id);
            }
        }
    }
    
    static function updatePostMeta($id, $fields = false)
    {
        if(count(func_get_args()) === 1) $fields = self::Fields(); 
        foreach($fields as $field){
            update_post_meta($id, $field->name(), $field->value());
        }
    }
    
    static function setPostMeta($id){
        
        $meta = get_post_meta($id);
        
        foreach(self::Fields() as $field){
            if(isset($meta[$field->name()]))
                $field->value($meta[$field->name()][0]);
        }
    }
    
    static function validate($valid_fields, $error_fields){
        
        //override this method to handle additional validation, 
        //and field comparison/dependecy issues, 
        //return false to cancel update, true to update all fields including
        //error fields, null to update only  
        return null;
    }
    
    static function delete()
    {
        
    }
    
}

?>
