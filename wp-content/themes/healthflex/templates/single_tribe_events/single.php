<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Post Template for user created CPTs
*/
if ( !class_exists('Plethora_Template_Single') ) {

  class Plethora_Template_Single { 

    public static $post_type;

  	public function __construct() {
        
        self::$post_type = Plethora_Theme::get_this_view_post_type();
        // Special treatment for full page layout display 
        add_filter( 'plethora_wrapper_column_open', array( $this, 'wrapper_column_open')); 

        // Main Post parts
        add_action( 'plethora_content', array( $this, 'wrapper_open'), 10);           // Post main wrapper opening
        add_action( 'plethora_content', array( $this, 'content'), 20);                // Post content
        add_action( 'plethora_content', array( $this, 'wrapper_close'), 20);          // Post main wrapper closing
        add_action( 'plethora_content', array( 'Plethora_Template', 'single_comments'), 20);         // Comments ( common for all singles )
  	    
    }


   /**
     * Returns single post wrapper tag opening
     */
    public static function wrapper_column_open( $wrapper_open ) {

      $layout   = Plethora_Theme::get_layout( self::$post_type );
      if ( $layout === 'no_sidebar' ) { 

        $wrapper_open = '<div class="col-md-8 col-md-offset-2">';
      }

      return $wrapper_open;
    }

   /**
     * Returns single post wrapper tag opening
     */
    public static function wrapper_open() {

      echo '<article id="post-'. get_the_id() .'" class="'. implode(' ', get_post_class( array( 'post', self::$post_type ) ) ) .'">';
    }

    /**
     * Returns single post content ( depending on format )
     */
    public static function content() {

      the_content();

      wp_link_pages(array(
               'before'      => '<div class="page-links post_pagination_wrapper"><span class="page-links-title">' . esc_html__( 'Pages:', 'healthflex' ) . '</span>',
               'after'       => '</div>',
               'link_before' => '<span class="post_pagination_page">',
               'link_after'  => '</span>',
      ));
    }

    /**
     * Returns single post wrapper tag closing
     */
    public static function wrapper_close() {

      echo '</article>';
    }
  } 
}    