<?php namespace PlugPress;

interface Bootstrap {
    function activate();
    function deactivate();
    function uninstall();
    function autoload();
}
?>
