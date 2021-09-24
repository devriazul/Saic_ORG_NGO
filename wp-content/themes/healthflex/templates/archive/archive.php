<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Page Template Class
*/
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Template_Archive') ) {

  class Plethora_Template_Archive { 

    public $post_type; 

    public function __construct() {

        $post_type          = Plethora_Theme::get_this_view_post_type();
        $supported_archives = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives' ) );
        $this->post_type    = in_array( $post_type, $supported_archives ) ? $post_type : 'post';
  
        add_action( 'plethora_content_before', array( $this, 'wrapper_grid_open'), 99);       // Post main wrapper opening
        add_action( 'plethora_content', array( $this, 'listing'));       // Post main wrapper opening

        add_action( 'plethora_content_after', array( $this, 'wrapper_grid_close'), 0);  // Page title
        add_action( 'plethora_content_after', array( $this, 'pagination'), 0);  // Page title

        // NOTICE: ONLY FOR DEMO PURPOSES ON PLETHORA SERVER OR LOCALHOST
        add_action( 'plethora_archive_list_class', array( $this, 'demo_list_switch_class' ), 0);  // Page title

    }

   /**
     * Returns single page wrapper tag opening
     */
    public function wrapper_grid_open() {

        Plethora_WP::get_template_part('templates/archive/wrapper_grid_open');
    }

    /**
     * Returns single page wrapper tag closing
     */
    public function wrapper_grid_close() {

        Plethora_WP::get_template_part('templates/archive/wrapper_grid_close');
    }

    /**
     * Returns single page wrapper tag closing
     */
    public function listing() {
       
       if ( Plethora_Theme::is_archive_page() ) { // if this is a classic blog index page

            $list_type = Plethora_Theme::get_archive_list(); 
            Plethora_WP::get_template_part('templates/archive/listing', $list_type);
       }
    }


    /**
     * Returns single page wrapper tag closing
     */
    public function pagination() {

        Plethora_WP::get_template_part('templates/archive/pagination');
    }

    /**
     * Filters list type to display 'demo_list' url parameter value
     * NOTICE: ONLY FOR DEMO PURPOSES ON PLETHORA SERVER OR LOCALHOST
     */
    public function demo_list_switch_class( $list_type ) {

        if ( !empty( $_GET['demo_list'] ) ) {

            $domain = $_SERVER['SERVER_NAME'];
            if ( $domain === 'plethorathemes.com' || $domain === 'localhost' ) {

                $list_type   = array();
                $list_type[] = $_GET['demo_list'];
            }
        }

        return $list_type;
    }
  } 
}