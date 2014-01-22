<?php namespace PlugPress;


abstract class Post implements Crud{
    
    //fields object
    
    static private $fields;
    
    
    static protected $posttype,
                     $labels = array(),
                     $register_param = array(),
                     $namespace;
    
    
    static function Fields($namespace)
    {
        self::$namespace = $namespace;
        
        if(!isset(self::$fields)){
            self::$fields = new Fields($namespace);
        }
        return self::$fields;
    }
    
    
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
                                                     'revisions',
                                                     'custom-fields');
        
        if(!isset(self::$register_param['public']))
            self::$register_param['public'] = true;
        
        //if no meta box callback is given, set it with the load_meta_boxes method
        if(!isset(self::$register_param['register_meta_box_cb']))
            self::$register_param['register_meta_box_cb'] = array(get_called_class(), 'load_meta_boxes');
        
	self::$register_param['labels'] = self::$labels;
        
        $posttype = self::$posttype;
        $param    = self::$register_param;
        
        add_action("init", function() use($posttype, $param){
           register_post_type( $posttype, $param ); 
        });
    }
    
    static function load_meta_boxes()
    {
        
    }
    
    //run this function when the post is being viewed
    static function read(){}

    static function update() 
    {
        //get all posted values and validate them
        //save the ones that pass, and create errors for the
        //ones that fail, if none fail create success message flash
    }
    
    static function delete(){
        
    }
    
}

?>
