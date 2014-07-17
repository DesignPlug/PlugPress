<?php namespace Plugpress\Controllers;


class LogController extends Controller{
    protected $ajaxOnly = true;
    
    function login(){
        if(is_user_logged_in()){
            //return 404 if user is already logged in
            return $this->get404();
        } else {
            $user = wp_signon($_POST);
            if(is_wp_error($user)){
                $this->getView()->add("errors", $user->errors)
                                ->add("message", $user->get_error_message())
                                ->json_render();
            } else {
                $this->getView()->add("errors", false)
                                ->add("logged_in", true)
                                ->add("user", $user)
                                ->json_render();
            }
        }
        exit();
    }
    
    function logout(){
        if(!is_user_logged_in()){
            //return 404 if user is not already logged in
            return $this->get404();
        } else {
            //log user out, and return json cofirmation
            wp_logout();
            $this->getView()->add('logged_out', true)
                            ->json_render();
        }
        exit();
    }
}

?>
