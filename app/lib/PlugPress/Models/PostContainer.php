<?php namespace Plugpress\Models;

use \Illuminate\Database\Eloquent\Collection as Collection;

class PostContainer extends Collection{
    
    protected $caller;
    
    function __construct(\Illuminate\Database\Eloquent\Collection $posts, $caller){
        $this->items  = $posts->all();
        $this->caller = $caller;
    }
    
    function with_thumbnails(){
        
         $thumbnail_ids = array();
         $meta = array();
         foreach($this->items as $post){
             $meta[$post->ID] = $post->get_postmeta();
             if(isset($meta[$post->ID]->_thumbnail_id)){
                 $thumbnail_ids[] = $meta[$post->ID]->_thumbnail_id;
             }
         }
         
         if(count($thumbnail_ids) > 0){
             
             $thumbnails = PostMeta::whereIn("post_id", $thumbnail_ids)
                                     ->where("meta_key", "=", "_wp_attachment_metadata")
                                     ->get(array('meta_value', 'post_id'));
             $upload_paths = wp_upload_dir();
             
             foreach($this->items as $post){
                 foreach($thumbnails as $thumb){
                     if($thumb->post_id == $meta[$post->ID]->_thumbnail_id){
                         $post->set_thumbnail(unserialize($thumb->meta_value), $upload_paths['url']);
                     }
                 }
             }
         }
         return $this;
    }
}

?>
