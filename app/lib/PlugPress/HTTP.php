<?php namespace Plugpress;

class HTTP extends \Plug\HTTP{

    static function setHeaderStatus($status = 200){
        
        global $wp_query;
        if($status !== 404){
            $wp_query->is_404 = false;
            $wp_query->query['error'] = $wp_query->query_vars['error'] = null;
        }
        status_header($status);
    }

}

?>
