<?php namespace CodeTemplates\Posttypes;


class Code extends \PlugPress\Post{
    
    static function create()
    {
        //posttype name
        self::$posttype = "Code";
        
        //labels
        self::$register_param = array("plural_name", "Repos");
        
        
        //set fields
        
        self::Fields("_ppct_")
                    ->add("github_link", function($fld){
                        
                        $fld->value("#")
                            ->title("Github")
                            ->description("Link to Github Repo")
                            ->type("url");
                        
                    })
                    ->add("codepen_link", function($fld){
                        
                        $fld->value("#")
                            ->title("Codepen")
                            ->description("Link to Codepen Source")
                            ->type("url");
                        
                    })
                    ->add("example", function($fld){
                        
                        $fld->title("Textarea")
                            ->description("Textarea example")
                            ->type("textarea")
                            ->validation_rules("min_len,30|alpha");
                        
                    });
                    
        parent::create();
    }
}

?>
