<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Sidebars Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Sidebars') && !class_exists('Plethora_Module_Sidebars_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-sidebars.php
   */
  class Plethora_Module_Sidebars_Ext extends Plethora_Module_Sidebars { 

	      /**
	       * Set default sidebars 
	       * @since 1.0
	       *
	       */
	    public function default_sidebars() {


	   		// echo '<div align="center">FRAMEWORK!</div>' . get_called_class();
			// Execute this only on first page load
			$default_sidebars = array();
			// IMPORTANT: this is necessary for repeater field...add a line for each sidebar record
			$default_sidebars['redux_repeater_data'] = array(
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			               );
			$default_sidebars['sidebar_name'] = array(
			                  esc_html__('Blog Sidebar', 'plethora-framework'),
			                  esc_html__('Pages Sidebar', 'plethora-framework'),
			                  esc_html__('Shop Sidebar', 'plethora-framework'),
			                  esc_html__('Events Sidebar', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #1', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #2', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #3', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #4', 'plethora-framework'),
			                );
			$default_sidebars['sidebar_desc'] = array(
			                  esc_html__('Default sidebar to add widgets for blog archives & posts', 'plethora-framework'),
			                  esc_html__('Default sidebar to add widgets for single pages', 'plethora-framework'),
			                  esc_html__('Default sidebar to add widgets for shop pages', 'plethora-framework'),
			                  esc_html__('Default sidebar to add widgets for event pages', 'plethora-framework'),
			                  esc_html__('Footer widgets area #1', 'plethora-framework'),
			                  esc_html__('Footer widgets area #2', 'plethora-framework'),
			                  esc_html__('Footer widgets area #3', 'plethora-framework'),
			                  esc_html__('Footer widgets area #4', 'plethora-framework'),
			                );
			$default_sidebars['sidebar_slug'] = array(
                          	  'sidebar-default',
                          	  'sidebar-pages',
                          	  'sidebar-shop',
                          	  'sidebar-eventscalendar',
			                  'sidebar-footer-one',
			                  'sidebar-footer-two',
			                  'sidebar-footer-three',
			                  'sidebar-footer-four'
			                );
	        $default_sidebars['sidebar_class'] = array( '', '', '', '', '', '', '', '' );
	        return $default_sidebars;
	    }

  }
}