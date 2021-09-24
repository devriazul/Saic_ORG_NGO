<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Profile Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Profile') && !class_exists('Plethora_Posttype_Profile_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-framework/features/posttype/profile/posttype-profile.php
	*/
	class Plethora_Posttype_Profile_Ext extends Plethora_Posttype_Profile {

		public function __construct() {

			parent::__construct();

			add_filter( 'plethora_wrapper_content_open', array( $this, 'filter_wrapper_content_open' ) );
			add_filter( 'plethora_wrapper_content_close', array( $this, 'filter_wrapper_content_close' ) );
		}

		public function filter_wrapper_content_open( $wrapper_content_open ) {

			if ( get_post_type() === 'profile' ) {

				$layout   = Plethora_Theme::get_layout();
				switch ( $layout ) {
					case 'no_sidebar' :
						$wrapper_content_open = '<section class="vc_off sidebar_off '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array() ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">';
						break;
					case 'right_sidebar' :
						$wrapper_content_open = '<section class="sidebar_on '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array( 'padding_top_half' ) ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">';
						break;
					case 'left_sidebar' :
						$wrapper_content_open = '<section class="sidebar_on '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array( 'padding_top_half' ) ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">';
						break;
					default:
						$wrapper_content_open = '<section class="'. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array() ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">';
						break;
				}
			}
			return $wrapper_content_open;
		}

		public function filter_wrapper_content_close( $wrapper_content_close ) {

			if ( get_post_type() === 'profile' ) {

				$wrapper_content_close = '</div></div></section>';
			}
			return $wrapper_content_close;
		}
  	}
}