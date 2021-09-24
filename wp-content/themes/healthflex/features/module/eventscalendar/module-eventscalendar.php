<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             	   (c) 2016

Events Calendar Support module extension class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Eventscalendar') && !class_exists('Plethora_Module_Eventscalendar_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/eventscalendar/module-eventscalendar.php
   */
  class Plethora_Module_Eventscalendar_Ext extends Plethora_Module_Eventscalendar {

  	public function construct_ext(){

        add_action( 'wp', array($this, 'style_fixes') );
  	}

  	public function style_fixes() {

      if ( $this->is_events_calendar( 'single' ) ) {

    		// H1 fix ( single view )
    		$this->single_view_css = $this->single_view_css . 'h1.tribe-events-single-event-title { font-weight: 900 !important; margin: -3px 0 13px 0 !important; font-size: 33px !important; -ms-word-wrap: break-word !important; word-wrap: break-word !important; }';
    		// H2 fix ( single view )
    		$this->single_view_css = $this->single_view_css . '.tribe-events-schedule h2 { font-size: 17px !important; font-weight: 500 !important; }';
    		// Editor content ( single view )
    		$this->single_view_css = $this->single_view_css . '.tribe-events-single-event-description { margin: 30px 0 20px !important; }';
        // Details, Organizer and Venue boxes fix ( single view )
        $this->single_view_css = $this->single_view_css . 'div.tribe-events-event-meta { background: #e5e5e5 !important; border-color: #e5e5e5 !important; color: #323232 !important; }';

      } elseif ( $this->is_events_calendar( 'archive' ) ) {

              // Details, Organizer and Venue boxes fix ( single view )
        $this->archive_view_css = $this->archive_view_css . 'div.datepicker { z-index: 19 !important; }';
      }
    }

    /**
    * Archive view options_config for theme options
    */
    public function archive_options_config() {

      $archive_options_config = array(
            array(
              'id'                    => 'layout',
              'theme_options'         => true,
              'theme_options_default' => 'no_sidebar',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'sidebar',
              'theme_options'         => true,
              'theme_options_default' => 'sidebar-eventscalendar',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'colorset',
              'theme_options'         => true,
              'theme_options_default' => 'foo',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'title',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'title-text',
              'theme_options'         => true,
              'theme_options_default' => esc_html__('Events Calendar', 'plethora-framework'),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'title-tax',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'subtitle',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'subtitle-text',
              'theme_options'         => true,
              'theme_options_default' => esc_html__('This is the default calendar view subtitle', 'plethora-framework'),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'subtitle-tax',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),

            array(
              'id'                    => 'intro-text',
              'theme_options'         => true,
              'theme_options_default' => esc_html__( 'This is an additional text that can be displayed in several calendar view positions.', 'plethora-framework' ),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'intro-text-action',
              'theme_options'         => true,
              'theme_options_default' => 'tribe_events_before_template',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'extraclass',
              'theme_options'         => true,
              'theme_options_default' => '',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
      );

      return $archive_options_config;
    }

    /**
    * Posts single view options_config for theme options and metabox panels
    */
    public function single_options_config() {

      $config = array(
            array(
              'id'                    => 'singleview-basic',
              'theme_options'         => true,
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'layout',
              'theme_options'         => true,
              'theme_options_default' => 'right_sidebar',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'sidebar',
              'theme_options'         => true,
              'theme_options_default' => 'sidebar-eventscalendar',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'colorset',
              'theme_options'         => true,
              'theme_options_default' => 'foo',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'singleview-content',
              'theme_options'         => true,
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'title',
              'theme_options'         => true,
              'theme_options_default' => false,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'subtitle',
              'theme_options'         => true,
              'theme_options_default' => '0',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'subtitle-text',
              'theme_options'         => false,
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => esc_html__( 'This is the default event subtitle', 'plethora-framework' ),
              ),
            array(
              'id'                    => 'event-back-to-all',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'event-notices',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'event-title',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'event-date',
              'theme_options'         => true,
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'event-nav',
              'theme_options'         => true,
              'theme_options_default' => '3',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array(
              'id'                    => 'extraclass',
              'theme_options'         => true,
              'theme_options_default' => '',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
      );

      return $config;
    }
  }
}