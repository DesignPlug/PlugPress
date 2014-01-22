<?php namespace Plugpress;

class Route {
    static protected $routes = array();
    static protected $created = false;
    static public $ignore_methods = array('__construct',
                                          '__get',
                                          '__set',
                                          '__call',
                                          '__callStatic',
                                          '__destruct',
                                          'getInstance');
    
    
    static function create(){
        if(!self::$created){        
            
            add_filter( 'query_vars', function($vars){
                $vars[] = "pp_controller";
                $vars[] = "pp_action";
                $vars[] = "pp_param";
                $vars[] = "pp_as";
                return $vars;
            });
            
            add_action("template_redirect", array("\Plugpress\Route", "route"));
            
            self::$created = true;
        }
    }
    
    static function route(){
        $as = get_query_var( 'pp_as' );
        
        if($as){
            
            //get controller
            $ctrl    = self::$routes[$as]['ctrl'];
            
            //if class does not exist rediect to 404
            if(!class_exists($ctrl)) return var_dump(get_404_template ());
            
            $restful = self::$routes[$as]['restful'];
            
            //get param
            $param   = ($p = get_query_var( 'pp_param' )) ? explode("/", $p) : array();
            
            //check for action
            
            $action = get_query_var( 'pp_action' );
            
            if($action){
                
                //if restful
                
                if($restful){
                              
                    $form_method = count($_POST) > 0 ? "post" : "get";
                   
                    $action_t = \Inflector::titleize($form_method .$action);
                    $action_c = lcfirt(\Inflector::camelize($form_method .$action));
                    $action_p = $form_method .$action;
                    
                    if(method_exists($ctrl, $action_t)){
                         $method = $action_t;
                         
                    } else if(method_exists($ctrl, $action_c)) {
                         $method = $action_c;
                         
                    } else if(method_exists($ctrl, $action_p)) {
                         $method = $action_p;
                    }  
                } else {
                    if(method_exists($ctrl, $action)) {
                         $method = $action;
                    }
                }
                
                //if we still haven't found the method, check for a default action
                if(!isset($method) && isset(self::$routes[$as]['default_action'])){
                    $default_action = self::$routes[$as]['default_action'];
                    
                    if($default_action && method_exists($ctrl, $default_action)){
                            array_unshift($param, $action);
                            $method = $default_action;
                    }
                }
            
            } else {
                
                $method = self::$routes[$as]['default_action'];
                if(!$method || !method_exists($ctrl, $method)){
                    return var_dump(get_404_template ());
                }
                    
            }
            
            //if no method found, or method is defined in ignore_methods array
            //redirect to 404 template
            if(!@$method || in_array(@$method, self::$ignore_methods)){
                return var_dump(get_404_template ());
            } else {
                //count the passed param, and check for the methods required param

                $rfl = new \ReflectionMethod($ctrl, $method);
                if(!$rfl->isPublic()){
                    return var_dump(get_404_template ());
                }
                $rfl_param     = $rfl->getParameters();
                $passing_param = array();
                $count = 0;
                
                if($rfl_param){
                    foreach($rfl_param as $r_param){
                        if(!$r_param->isOptional()){
                            if(!isset($param[$count]))
                                return var_dump("param " .$r_param->getName() ." is missing"); 
                        }
                        if(isset($param[$count])) $passing_param[] = $param[$count];
                        $count++;
                    }
                }
                
                if(count($passing_param) !== count($param)) exit("wrng param number");
                
                //instatiate ctrl
                return call_user_func_array(array(call_user_func(array($ctrl, 'getInstance')), $method), $passing_param);
            }  
            
            
        }
    }


    static function Controller($as, $controller_name, $default_action = null, $restful = false){
        add_action("init", function() use($as, $controller_name, $default_action){
            
            
            add_rewrite_rule( trim($as,'/') .'/([^/]+)/(.*)', 
                              'index.php?pp_controller='.$controller_name.'&pp_action=$matches[1]&pp_param=$matches[2]&pp_as=' .$as, 
                              'top' );
            
            add_rewrite_rule( trim($as,'/') .'/([^/]+)', 
                              'index.php?pp_controller='.$controller_name.'&pp_action=$matches[1]&pp_as=' .$as, 
                              'top' ); 
            
            if(isset($default_action)){
                add_rewrite_rule( trim($as,'/'), 
                                  'index.php?pp_controller='.$controller_name .'&pp_as=' .$as .'&pp_action=' .$default_action, 
                                  'top' );    
            }
        });
        
        self::$routes[$as] = array('ctrl' => $controller_name, 
                                   'restful' => $restful, 
                                   'default_action' => $default_action);
    }
}

?>
