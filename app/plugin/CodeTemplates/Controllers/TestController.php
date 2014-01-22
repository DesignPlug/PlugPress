<?php namespace CodeTemplates\Controllers;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testController
 *
 * @author Richard
 */
class TestController extends \Plugpress\Controller{
    
    protected $View;
    
    function __construct() {
        $this->View = \Plugpress\Views::getInstance("PPCODE_"); 
    }
    
    function test($param, $a){

        return    $this->View->view("code\single")
                             ->add("x", 1)
                             ->add("y", 2)
                             ->nest("sub_form", "code/partials/subscribe")
                             ->wp_render();
    }
}

?>
