<?php namespace {plugin_name}\Controllers;

use WPMVC\Framework\Models;


class BaseController extends \Plugpress\Controller{
    
    protected $View;
    
    function __construct() {
        $this->View = \Plugpress\Views::getInstance({plugin_namespace}); 
    }
}

?>
