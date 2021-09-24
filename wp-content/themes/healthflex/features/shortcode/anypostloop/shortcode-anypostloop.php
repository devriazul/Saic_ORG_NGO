<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             	   (c) 2017

{post} LOOP Shortcodes class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Shortcode_Anypostloop') && !class_exists('Plethora_Shortcode_Anypostloop_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-featureslib/features/shortcode/anypostloop/shortcode-anypostloop.php
	*/
	class Plethora_Shortcode_Anypostloop_Ext extends Plethora_Shortcode_Anypostloop {}

	// Construct all Loop shortcodes, according to supported post types
	// Should construct only using the Plethora_Shortcode_Anypostloop_Ext
	function plethora_create_anypostloop_objects(){

		$grid_post_types   = array();
		$slider_post_types = array( 'post' );
		$all_post_types    = array_merge( $slider_post_types, $grid_post_types );
		$all_post_types    = array_unique( $all_post_types );
		$instances         = array( 'grids' => array(), 'sliders' => array() );
		foreach ( $all_post_types as $post_type ) {

			$posttype_obj = get_post_type_object( $post_type );
			if ( is_object( $posttype_obj ) ) {
				
				if ( in_array( $post_type , $grid_post_types ) && !array_key_exists( $post_type, $instances['grids'] ) ) {

					$instances['grids'][$post_type] = new Plethora_Shortcode_Anypostloop_Ext( $posttype_obj, 'grid' );
				}

				if ( in_array( $post_type , $slider_post_types ) && !array_key_exists( $post_type, $instances['sliders'] ) ) {

					$instances['sliders'][$post_type] = new Plethora_Shortcode_Anypostloop_Ext( $posttype_obj, 'slider' );
				}
			}
		}
	}
	add_action( 'init', 'plethora_create_anypostloop_objects' );
}