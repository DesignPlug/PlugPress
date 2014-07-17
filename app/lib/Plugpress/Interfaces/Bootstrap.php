<?php namespace PlugPress\Interfaces;

interface Bootstrap {
    function init();
    function activate();
    function deactivate();
    function uninstall();
    function autoload();
}
?>
