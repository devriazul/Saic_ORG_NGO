<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Description: Inlcudes theme and third party configuration methods.

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

if ( !class_exists('Plethora_Theme') && class_exists('Plethora') ) {

  class Plethora_Theme extends Plethora {

    function __construct( $slug = 'healthflex', $name = 'Plethora Boilerplate', $ver = '1.0.0' ) {

      # SET BASIC VARIABLES
      $this->theme_slug = $slug;
      $this->theme_name = $name;
      $this->theme_version = $ver;

      // Parent/Child Theme URIs
      define( 'PLE_THEME_ASSETS_URI',       PLE_THEME_URI . '/assets' );              // Theme assets folder
      define( 'PLE_THEME_JS_URI',           PLE_THEME_ASSETS_URI . '/js' );           // Assets JavaScript folder
      define( 'PLE_THEME_FEATURES_URI',     PLE_THEME_URI . '/features' );            // Theme features folder
      define( 'PLE_THEME_TEMPLATES_URI',    PLE_THEME_URI . '/templates' );           // Theme template parts folder
      define( 'PLE_CHILD_URI',              get_stylesheet_directory_uri() );     // Child theme folder
      define( 'PLE_CHILD_ASSETS_URI',       PLE_CHILD_URI . '/assets' );              // Child theme assets folder
      define( 'PLE_CHILD_JS_URI',           PLE_CHILD_ASSETS_URI . '/js' );           // Child theme assets JavaScript folder
      define( 'PLE_CHILD_FEATURES_URI',     PLE_CHILD_URI . '/features' );            // Child theme includes folder
      define( 'PLE_CHILD_TEMPLATES_URI',    PLE_CHILD_URI . '/templates' );           // Child theme template parts folder

      // Parent/Child Theme DIRs
      define( 'PLE_THEME_ASSETS_DIR',       PLE_THEME_DIR . '/assets' );              // Theme assets folder
      define( 'PLE_THEME_JS_DIR',           PLE_THEME_ASSETS_DIR . '/js' );           // Theme assets JavaScript folder
      define( 'PLE_THEME_FEATURES_DIR',     PLE_THEME_DIR . '/features' );            // Theme features folder
      define( 'PLE_THEME_TEMPLATES_DIR',    PLE_THEME_DIR . '/templates' );           // Theme template parts folder
      define( 'PLE_CHILD_DIR',              get_stylesheet_directory() );         // Child theme folder
      define( 'PLE_CHILD_ASSETS_DIR',       PLE_CHILD_DIR . '/assets' );              // Child theme assets folder
      define( 'PLE_CHILD_JS_DIR',           PLE_CHILD_ASSETS_DIR . '/js' );           // Child theme assets JavaScript folder
      define( 'PLE_CHILD_FEATURES_DIR',     PLE_CHILD_DIR . '/features' );            // Child theme includes folder
      define( 'PLE_CHILD_TEMPLATES_DIR',    PLE_CHILD_DIR . '/templates' );           // Child theme template parts folder


      # PRE-FRAMEWORK HOOKS ( theme actions/filters that must be declared before PF load )
      $this->framework_hooks();

      # LOAD FRAMEWORK
      $this->load_framework();

      # LOAD THEME CONFIGURATION ( theme actions/filters that must be declared after PF load )
      $this->load_theme();

      # CREATE TEMPLATE
      global $plethora_template;
      $plethora_template = new Plethora_Template();
    }

    /**
     * All framework related hooks should be set here!
     * NOTICE: don't use Plethora_WP intermediary methods!
     * @since 1.0
     *
     */
    public function framework_hooks() {

      // Replace 'skincolored_section' with 'primary_section' color sets
      add_filter( 'plethora_module_style_color_sets', array( $this, 'modify_color_sets' ) );

      // Remove 'no_sidebar_narrow' option from page layouts
      add_filter( 'plethora_module_style_page_layouts', array( $this, 'remove_page_layouts' ) );

      // Remove 2-3 and 3-4 stretch ratios
      add_filter( 'plethora_module_style_stretchy_ratios', array( $this, 'remove_stretchy_ratios' ) );

      // CF7 element replacements
      add_filter( 'wpcf7_form_elements', array('Plethora_Theme', 'wpcf7_form_elements') );        // CF7 form markup & styling

      // Minor fix for soundclound embeds
      add_filter('oembed_dataparse', array( 'Plethora_Theme', 'soundcloud_oembed_filter'), 90, 3 );        // FIX for oEMBEDS ( soundcloud ) to comply with W3C validation
    }


    /**
    * CORE UPDATE COMPATIBILITY METHOD
    * Replaces 'primary_section' with  'skincolored_section' color set
    * Hooked on 'plethora_module_style_color_sets' filter
    */
    public function modify_color_sets( $color_sets ) {

      if ( isset( $color_sets['primary']['value'] )  ) {

        $color_sets['primary']['value'] = 'skincolored_section';
      }

      return $color_sets;
    }

    /**
    * CORE UPDATE COMPATIBILITY METHOD
    * Removes 'no_sidebar_narrow' out of the default page layouts
    * Hooked on 'plethora_module_style_page_layouts' filter
    */
    public function remove_page_layouts( $page_layouts ) {

      if ( isset( $page_layouts['no_sidebar_narrow'] )  ) {

          unset( $page_layouts['no_sidebar_narrow'] );
      }

      return $page_layouts;
    }

    /**
    * CORE UPDATE COMPATIBILITY METHOD
    * Removes 2-3 and 3-4 stretch ratios out of the default
    * Hooked on 'plethora_module_style_stretchy_ratios' filter
    */
    public function remove_stretchy_ratios( $ratios ) {

      if ( isset( $ratios['2-3'] )  ) {

          unset( $ratios['2-3'] );
      }

      if ( isset( $ratios['3-4'] )  ) {

          unset( $ratios['3-4'] );
      }

      return $ratios;
    }

    /**
    * Fix Contact Form 7 default markup and styling
    * @since 1.0
    *
    */
    static function wpcf7_form_elements( $content ) {
      // global $wpcf7_contact_form;

      $content = preg_replace( "/wpcf7-text/", "wpcf7-form-control form-control", $content );
      $content = preg_replace( "/wpcf7-email/", "wpcf7-form-control form-control", $content );
      $content = preg_replace( "/form-controlarea/", "wpcf7-form-control form-control", $content );
      $content = preg_replace( "/wpcf7-submit/", "wpcf7-submit btn btn-primary", $content );
      return $content;
    }

    /**
     * Fix oEmbed W3C validation issues
     *
     * @since 1.0
     *
     */
    static function soundcloud_oembed_filter( $return, $data, $url ) {

      $style = '';

      if ( strpos($return, 'frameborder="0"') !== FALSE ){

        $style .= 'border:none;';
        $return = str_replace('frameborder="0"', '', $return);

      } elseif ( strpos($return, 'frameborder="no"') !== FALSE ) {

        $style .= 'border:none;';
        $return = str_replace('frameborder="no"', '', $return);
      }

      if ( strpos($return, 'scrolling="no"') !== FALSE ){

        $style .= 'overflow:hidden;';
        $return = str_replace('scrolling="no"', 'style="'. esc_attr( $style ) .'"', $return);

      } else {

        $return = str_replace('<iframe', '<iframe style="'. esc_attr( $style ) .'"', $return);
      }

      return $return;
    }

    /**
     * Load theme configuration
     *
     * @since 1.0
     *
     */
    public function load_theme() {

    /*** BASIC CONFIGURATION >>> ***/
        // THEME SUPPORTS
        add_theme_support( 'post-thumbnails', array( 'post', 'page', 'profile', 'terminology' ) );         // ADD POST THUMBNAILS
        add_theme_support( 'title-tag' );                                                    // ADD POST THUMBNAILS
        add_theme_support( 'post-formats', array( 'image', 'video', 'audio', 'link' ) );     // POST FORMATS SUPPORT
        add_theme_support( 'automatic-feed-links' );                                         // AUTOMATIC FEED LINKS SUPPORT
        add_action( 'admin_notices', array( $this, 'admin_notice_wpless'), 20 );                 // PRODUCE A NOTICE IF WP LESS IS NOT PRESENT

        if ( ! isset( $content_width ) ) {  $content_width = 960; }                                       // SET $content_width VARIABLE
    /*** <<< END BASIC CONFIGURATION ***/

    /*** SCRIPT REGISTRATION & ENQUEUES >>> ***/
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets'), 1 );             // Theme assets registration ( register early )
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets'), 30 );             // Declare ALL assets ( scripts & styles ) - always enqueue on 30
        // Add svg modal workaround using Plethora_Theme::svgloader_modal() method
        add_action('wp_enqueue_scripts', array('Plethora_Theme', 'svgloader_modal'), 999);  // Must trigger this on wp_enqueue_scripts with latest priority
    /*** <<< SCRIPT REGISTRATION & ENQUEUES ***/
    }

//////////////// BASIC CONFIGURATION  ------> START

    public function admin_notice_wpless() {

      if ( ! class_exists('Plethora_Module_Wpless_Ext') && !empty( $_GET['page'] ) && $_GET['page'] === THEME_OPTIONSPAGE ) {

        $message = esc_html__('Custom functionality ( post type, taxonomies, shortcodes, widgets ) and styling related theme options ( color sets, typography, etc. ) WILL NOT be enabled until you activate the', 'healthflex');
        $message .= ' <strong>';
        $message .= 'Plethora Features Library';
        $message .= ' </strong> plugin';
        echo '<div class="error"><p>'. $message .'</p></div>';
      }
    }
//////////////// BASIC CONFIGURATION  <------ END
//////////////// SCRIPT REGISTRATION & ENQUEUES ------> START
    /**
    * Register global assets files
    */
    public function register_assets(){

      // If production mode is on, then add to script files the .min suffix
      // $min_suffix = Plethora_Theme::is_developermode() ? '.min' : '';
      $min_suffix = '.min';

      # ASSET REGISTRATIONS
        // Register SCRIPTS used only in this theme ( remember...cross-theme scripts have been already registered )
        wp_register_script( 'boostrap', PLE_THEME_ASSETS_URI . '/js/libs/bootstrap'. $min_suffix .'.js',   array( 'jquery' ),  '', TRUE );
        wp_register_script( ASSETS_PREFIX . '-particles', PLE_THEME_ASSETS_URI . '/js/libs/particlesjs/particles' . $min_suffix . '.js',   array(),  '', TRUE );
        wp_register_script( ASSETS_PREFIX . '-init', PLE_THEME_ASSETS_URI . '/js/theme.js',   array(),  '', TRUE );
        // Register STYLES used only in this theme ( remember...cross-theme styles have been already registered )
        wp_register_style( ASSETS_PREFIX .'-custom-bootstrap',  PLE_THEME_ASSETS_URI . '/css/theme_custom_bootstrap.css');
        if ( class_exists( 'Plethora_Module_Wpless_Ext' ) ) {

          wp_register_style( ASSETS_PREFIX .'-style', get_stylesheet_uri(), array( ASSETS_PREFIX .'-dynamic-style' ) );  // LESS dynamic style.css

        } else {

          wp_register_style( ASSETS_PREFIX .'-default-style', PLE_THEME_ASSETS_URI.'/css/default_stylesheet.css', array( ASSETS_PREFIX .'-custom-bootstrap' ) ); // Default static style.css
        }
    }

    /**
    * Enqueue global assets files
    */
    public function enqueue_assets(){

      // If production mode is on, then add to script files the .min suffix
      // $min_suffix = Plethora_Theme::is_developermode() ? '.min' : '';
      $min_suffix = '.min'; // PLETODO: Kostas should check the .min suffix functionality sometime before pack

      # ASSET ENQUEUES
        // Enqueue SCRIPTS
        wp_enqueue_script( ASSETS_PREFIX . '-modernizr' );
        wp_enqueue_script( 'boostrap' );
        wp_enqueue_script( 'easing' );
        wp_enqueue_script( 'wow-animation-lib' );
        wp_enqueue_script( 'conformity' );
        wp_enqueue_script( ASSETS_PREFIX . '-particles' );
        wp_enqueue_script( 'parallax' );
        wp_enqueue_script( ASSETS_PREFIX . '-init' );
        // Enqueue STYLES
        wp_enqueue_style( 'animate');          // Animation library
        if ( class_exists( 'Plethora_Module_Wpless_Ext' ) ) {

          wp_enqueue_style( ASSETS_PREFIX .'-style');            // LESS dynamic style.css

        } else {

          wp_enqueue_style( ASSETS_PREFIX .'-custom-bootstrap'); // Custom Bootstrap Base
          wp_enqueue_style( ASSETS_PREFIX .'-default-style' );  // Default static style.css
        }

      # WP AJAX COMMENTS ( ajax handler for threaded comments...suggested by WP )
        $thread_comments = get_option('thread_comments');
        if ( is_singular() && comments_open() && $thread_comments ) {

          wp_enqueue_script( 'comment-reply' );
        }
    }
//////////////// SCRIPT REGISTRATION & ENQUEUES <------ END

//////////////// THIRD PARTY CONFIGURATION METHODS ----> START
    /**
     * Will check if SVGLOADER script is about to load. If so, it will add the desired markup method
     */
    public static function svgloader_modal() {

      if ( wp_script_is( 'svgloader' ) ) {

        add_filter('plethora_wrapper_main_open', array( 'Plethora_Theme', 'addSVGloaderModal' ) );
      }
    }

    // ADD SVG LOADER REQUiRED MARKUP FOR THE AJAX LOADING ONLY WHEN SVGLOADER IS ENQUEUED FOR LOADING
    public static function addSVGloaderModal( $wrapper_main_open ) {

      $wrapper_main_open =
      '<span class="progress_ball"><i class="fa fa-refresh"></i></span>

      <div class="loader-modal"></div>
       <div id="loader" data-opening="m -5,-5 0,70 90,0 0,-70 z m 5,35 c 0,0 15,20 40,0 25,-20 40,0 40,0 l 0,0 C 80,30 65,10 40,30 15,50 0,30 0,30 z" class="pageload-overlay">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewbox="0 0 80 60" preserveaspectratio="none">
          <path d="m -5,-5 0,70 90,0 0,-70 z m 5,5 c 0,0 7.9843788,0 40,0 35,0 40,0 40,0 l 0,60 c 0,0 -3.944487,0 -40,0 -30,0 -40,0 -40,0 z"></path>
        </svg>
      </div>' . $wrapper_main_open;

        return $wrapper_main_open;
    }
//////////////// THIRD PARTY CONFIGURATION METHODS <---- END
  }
}