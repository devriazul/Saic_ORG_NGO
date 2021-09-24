<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Heading Group Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Shortcode_Headinggroup') && !class_exists('Plethora_Shortcode_Headinggroup_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/shortcode/headinggroup/shortcode-headinggroup.php
   */
  class Plethora_Shortcode_Headinggroup_Ext extends Plethora_Shortcode_Headinggroup { 

      /** 
      * Configure parameters displayed for Healthflex
      * @return array
      */
      public function params_config() {

          $params_config = array(
              array( 
                'id'         => 'content', 
                'default'    => '<h2>'.esc_html( 'Your H2 Title', 'healthflex' ).'</h2>',
                'field_size' => '',
                ),
              array( 
                'id'         => 'subtitle', 
                'default'    => '',
                'field_size' => '',
                ),
              array( 
                'id'         => 'subtitle_color', 
                'default'    => '',
                'field_size' => '',
                ),
              array( 
                'id'         => 'type', 
                'default'    => 'fancy',
                'field_size' => '6',
                ),
              array( 
                'id'         => 'align', 
                'default'    => 'text-center',
                'field_size' => '6',
                ),
              array( 
                'id'         => 'subtitle_position', 
                'default'    => 'bottom',
                'field_size' => '6',
                ),
              array( 
                'id'         => 'extra_class', 
                'default'    => '',
                'field_size' => '6',
                ),
              array( 
                'id'         => 'css', 
                'default'    => '',
                'field_size' => '',
                ),
          );

          return $params_config;
      }
  }
}