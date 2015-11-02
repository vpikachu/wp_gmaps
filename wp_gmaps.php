<?php
/*
Plugin Name: WP Gmaps
Plugin URI: http://wordpress.org/plugins/wp_gmaps/
Description: Google maps content on wordpress pages.
Author: Vitali Guchek
Version: 0.5
Author URI: http://vpikachu.github.io/
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WP_Gmaps {
    public function __construct() {        
        $this->is_default = FALSE;
        if(is_admin()){  $this->init_admin();}
        else {   $this->init_frontend();}        
    }
    public $meta_props = ["gmap_lat"=>0,"gmap_lng"=>0,"gmap_zoom"=>0];
    /**
     * set metta props to default from plugin options
     */
    public function set_default_meta_props(){
        $this->meta_props["gmap_lat"] = 53.9314418;
        $this->meta_props["gmap_lng"] = 27.6846242;
        $this->meta_props["gmap_zoom"] = 14;
        $this->is_default = TRUE;
    }
    private $is_default;
    /**
     * retriving metadata drom post
     * @param type $postid  
     */
    public function retrive_meta_props($postid){
        $arr = get_post_custom($postid);
        
        if($arr == null){//new post
            $this->set_default_meta_props();
        }
        else {
            foreach ($this->meta_props as $key=>$value)
            {
                if(array_key_exists($key, $arr) && count($arr[$key]) > 0 && !empty($arr[$key][0]))
                {
                    $this->meta_props[$key] = $arr[$key][0];//pass values
                }    
                else {//if some value not exist or broken then sett to default all and break search
                    $this->set_default_meta_props();
                    break;
                }
            }
        }
    }
    /**
     * enqueue google maps api script
     */
    public function enqueue_googlemaps_api($hook){                      
        if($hook == "post.php" || is_single() || is_page()){
            wp_enqueue_script("gmaps_api", "http://maps.googleapis.com/maps/api/js?sensor=true&v=3");        
        }
    }
    /**
     * init admin hooks
     */
    public function init_admin(){       
        add_action( 'admin_enqueue_scripts', array($this,'enqueue_googlemaps_api'));
        add_action( 'add_meta_boxes',array($this,'add_admin_mbox'));                
        add_action( 'save_post', array($this,'save_gmap'));
    }
    /**
     * init front end hooks
     */
    public function init_frontend(){
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_googlemaps_api') );
        add_action( 'add_meta_boxes',array($this,'add_frontend'));                
        add_filter( 'the_content', array($this,'add_frontend') );
        
    }
    /**
     * hook to add metabox in frontend part
     */
    public function add_frontend($content){        
        if(is_single() || is_page()){
            global $post;
            $this->retrive_meta_props($post->ID);            
            if($this->is_default) return $content;            
            
            require_once  plugin_dir_path( __FILE__ ).'views/frontend.php';            
             
            return fronend_template($this->meta_props['gmap_lat'],
                    $this->meta_props["gmap_lng"],
                    $this->meta_props["gmap_zoom"]).$content;
        } else return $content;
    }
    /**
     * hook to add metabox in admin part
     */
    public function add_admin_mbox(){        
        add_meta_box("wp_gmaps_mbox", "Gmap", array($this,"render_admin_mbox"),
                "post", "normal", "default");
        add_meta_box("wp_gmaps_mbox", "Gmap", array($this,"render_admin_mbox"),
                "page", "normal", "default");
    }
    /**
    * Meta box display callback.
    *
    * @param WP_Post $post Current post object.
    */
    public function render_admin_mbox($post){
        $this->retrive_meta_props($post->ID);
        
        require_once  plugin_dir_path( __FILE__ ).'views/admin.php';            
             
        admin_template($this->meta_props,  $this->is_default);
    }    
    /**
     * save gmaps parameters of post
     * @param type $post_id 
     */
    public function save_gmap($post_id ) {        
        foreach ($this->meta_props as $key=>$value)
        {
            if(array_key_exists($key, $_POST) && !empty($_POST[$key]))
            {
                $this->meta_props[$key] = $_POST[$key];
                update_post_meta( $post_id,$key,$this->meta_props[$key]);
            }    
            else{                
                delete_post_meta ($post_id,$key);
            }
            
        }
    }
}

$wpgmaps = new WP_Gmaps();