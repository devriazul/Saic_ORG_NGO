<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

About Us Widget Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Widget_Aboutus') && !class_exists('Plethora_Widget_Aboutus_Ext') ) {

	add_action('widgets_init',  function() {

		return register_widget("Plethora_Widget_Aboutus_Ext");
	}); 	

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/widget/widget-aboutus.php
   */
  class Plethora_Widget_Aboutus_Ext extends Plethora_Widget_Aboutus { 

  }
}