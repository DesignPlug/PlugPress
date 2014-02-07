<?php namespace PlugPress;

abstract class CustomTemplate implements Template{

    protected $template;
    protected $posttype;
    protected $template_dir          = "";
    protected $template_override_dir = "";
    protected $allow_theme_override  = true;
    protected $filter_tag            = "";
    protected $redirect              = false;
    protected $real_include          = false;

    function templateInclude( $template ) {

        // For all other CPT
        if (self::getPostType() != $this->posttype ) {
            return $template;
        }

        // Else use custom template
        $template = is_single() ? "single" : "archieve";

        return $this->template = $this->getTemplate($template);
    }

    static function getPostType()
    {
        $post_id = get_the_ID();
        return get_post_type( $post_id );
    }

    abstract function loadTemplateStyles($template = null);

    function getTemplate( $template )
    {
        //enqueue scripts
        add_action( 'wp_enqueue_scripts', array($this, 'loadTemplateStyles'));

        // Get the template slug
        $sep = DIRECTORY_SEPARATOR;        
        
        $templateFile = rtrim( $template, '.php' );
        //str_replace(array("/","\\"), $sep, $templateFile);
        $template     = $templateFile . '.php';

        // Check if a custom template exists in the theme folder, if not, load the plugin template file
        // if them override allowed
        
        if($this->allow_theme_override === true)
            $theme_file = locate_template( array( rtrim($this->template_override_dir, $sep) .$sep .$template ) );


        if (@$theme_file)
            $file = $theme_file;
        else
            $file = rtrim($this->template_dir, $sep) .$sep . $template;
        
        //if filer tag was set, use it as first arg
        //for apply filters, else use {posttype}-{template}

        if(empty($this->filter_tag)) 
            $tag = $this->posttype .'-' .$template;
        else
            $tag = $this->filter_tag;

        //if rediect is true, include template only
        //but if it is false filter content area

        if($this->redirect === true)
        {
            include $file;
            exit;
        }
        else
        {
            if($this->real_include === true){
                include $file;
            } else {
                return apply_filters( $tag, $file );
            }
        }
    }        
}
?>
