<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Top Bar Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Topbar') && !class_exists('Plethora_Module_Topbar_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-topbar.php
   */
  class Plethora_Module_Topbar_Ext extends Plethora_Module_Topbar { 

	public function init() {

		add_action( 'plethora_after_update', array( $this, 'after_update' ) );
	}

    /** 
    * A version update fix added since 1.3.1
    * Remove top bar default text option from all post metaboxes.
    * This is necessary to avoid conflicts with non used saved meta options
    */
  	public function after_update() {

        $args = array(
                'posts_per_page'   => -1,
                'meta_key' => METAOPTION_PREFIX .'topbar-col2-text',
                'post_type' => array( 'post', 'page', 'product', 'terminology', 'profile' )
            );
        $posts = get_posts( $args );
        foreach ( $posts as $post ) {

            delete_post_meta( $post->ID, METAOPTION_PREFIX .'topbar-col1-text' );
            delete_post_meta( $post->ID, METAOPTION_PREFIX .'topbar-col2-text' );
        }
        wp_reset_postdata();

  	}
  }
}