<?php namespace PlugPress;

interface Bootstrap {
    function init();
    function activate();
    function deactivate();
    function uninstall();
    function autoload();
}
?>
