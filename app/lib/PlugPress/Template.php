<?php namespace PlugPress;

    
interface Template{
    function templateInclude($template);
    function getTemplate($template);
    function loadTemplateStyles($template = null);
    function setPageTitle($title);
}

?>
