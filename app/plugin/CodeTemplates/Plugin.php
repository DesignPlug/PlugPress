<?php namespace CodeTemplates;


class Plugin extends \Plugpress\Plugin{

    var $register_autoload = false;
 
    function __construct() {
        
        \Plugpress\Route::controller("test", "\CodeTemplates\Controllers\TestController", "test");
        
        //init code snippets templates
        //add_filter( 'template_redirect', array(Templates\CodeTemplates::getInstance(), 'templateInclude'));
        
/*        
        $data =  self::View()->add("foo", "bar")
                             ->add("foo2", "baz")
                             ->add("another", "yo")
                             ->render("code/archieve");
*/        
        //var_dump($data);
        
        
        Posttypes\Code::create();
    }
    
    function activate() {
        //code to run when plugin is activated
    }
    
    function uninstall() {
        //code to run when plugin is uninstalled
    }
    
    function deactivate() {
        //code to run when plugin is deactivated
    }
    
    function autoload()
    {
        //assign custom autloader 
    }
    
}
?>
