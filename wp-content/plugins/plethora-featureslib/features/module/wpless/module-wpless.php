<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M			           (c) 2017

WP LESS Compiler Module Base Class
Please reference to this on https://github.com/oyejorge/less.php

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

class Plethora_Module_Wpless{

	public static $feature_title         = "WP Less Module";                       // FEATURE DISPLAY TITLE
	public static $feature_description   = "Dynamic LESS stylesheet compilation";  // FEATURE DISPLAY DESCRIPTION
	public static $theme_option_control  = false;                                  // FEATURE CONTROLLED IN THEME OPTIONS PANEL
	public static $theme_option_default  = true;                                   // DEFAULT ACTIVATION OPTION STATUS
	public static $theme_option_requires = array();                                // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                                   // DYNAMIC CLASS CONSTRUCTION?
	public static $dynamic_method        = false;                                  // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

	public $wpless;
	public $stylesheet_path;
	public $stylesheet_url;
	public $stylesheet_version;
	public $stylesheet_minified;
	public $stylesheet_contents;
	public $stylesheet_contents_min;
	public $cache_dir;
	public $less_variables = array();

	public function __construct(){

		// Set normal/minified stylesheets info
		$upload_dir                = wp_upload_dir();
		$this->stylesheet_path     = $upload_dir['basedir'] .'/plethora/style.css';
		$this->stylesheet_path_min = $upload_dir['basedir'] .'/plethora/style.min.css';
		$this->stylesheet_url      = $upload_dir['baseurl'] .'/plethora/style.css';
		$this->stylesheet_url_min  = $upload_dir['baseurl'] .'/plethora/style.min.css';
		$this->cache_dir           = $upload_dir['basedir'] .'/cache/';
		$this->stylesheet_version  = get_option( 'plethora_wpless_stylesheet_ver', time() );

		// Compose stylesheets for the following occasions
		add_action( 'init', array( $this, 'update_stylesheet' ), 9999999);														// files missing or in dev mode
		add_action( 'redux/options/'.THEME_OPTVAR.'/reset', array( $this, 'update_stylesheet' ), 9999999);				// theme options reset
		add_action( 'redux/options/'.THEME_OPTVAR.'/section/reset', array( $this, 'update_stylesheet' ), 9999999);		// theme option section reset
		add_action( 'redux/options/'.THEME_OPTVAR.'/saved', array( $this, 'update_stylesheet' ), 9999999);				// theme options save
		add_action( 'plethora_demo_import_finished', array( $this, 'update_stylesheet' ), 9999999);								// demo import finished
		add_action( 'after_switch_theme', array( $this, 'update_stylesheet' ), 10, 0 );										// theme switching
		add_action( 'activated_plugin', array( $this, 'update_stylesheet' ), 10, 0 );										// any plugin activation

		// Enqueue stylesheet
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylesheet'), 100);

		// Add an update warning
          $notice  = sprintf( esc_html__( 'After a theme update, it\'s always good practice to visit the %1$sTheme Options panel%2$s and just click once on the %1$sSave Changes%2$s button. ', 'plethora-framework' ), '<strong>', '</strong>' );
          $notice .= '<br>'. sprintf( esc_html__( 'Also, if this is a production website, please make sure that the %1$sTheme Options > Advanced > Developer Tools > Development Mode%2$s is disabled.', 'plethora-framework' ), '<strong>', '</strong>' );
		  $args = array(
			'theme_update_only' => true,
			'title'             => wp_get_theme( THEME_SLUG )->get('Name') .' '. wp_get_theme( THEME_SLUG )->get('Version') .' update notice',
			'notice'            => $notice,
			'type'              => 'warning',
			'links'             => array(
				array(
				  'href'        => admin_url( 'admin.php?page=plethora_options' ),
				  'anchor_text' => esc_html__( 'Go to theme options', 'plethora-framework' ),
				),
			)
		  );
		Plethora_Theme::add_admin_notice( 'module_wpless_update_warning2', $args );

	}

	/**
	* Enqueues normal or minified version of the theme stylesheet
	*/
	public function enqueue_stylesheet() {

		$stylesheet_url = Plethora_Theme::is_developermode() ? $this->stylesheet_url : $this->stylesheet_url_min;
		wp_register_style( 'plethora-dynamic-style', $stylesheet_url, array(), $this->stylesheet_version, 'all' );
		wp_enqueue_style( 'plethora-dynamic-style' );
	}

	/**
	* Updates normal/minifed version of the theme stylesheet
	* Hooked @ 'init'
	* Hooked @ 'redux/options/'.THEME_OPTVAR.'/reset'
	* Hooked @ 'redux/options/'.THEME_OPTVAR.'/section/reset'
	* Hooked @ 'redux/options/'.THEME_OPTVAR.'/saved'
	* Hooked @ 'plethora_demo_import_finished'
	* Hooked @ 'after_switch_theme'
	* Hooked @ 'activated_plugin'
	*/
	public function update_stylesheet() {

		$current_filter  = current_filter();
		// Set $recompile to true, if we are on developer mode and recompile is defined to true
		$recompile = $current_filter === 'init' ? Plethora_Theme::is_developermode() : false;

		// Set $recompile to true, if trigger transient is set to true
		if( $current_filter === 'init' && get_transient( 'plethora_module_wpless_trigger_recompile' ) ) { // transient is set to true only during PFL activation

			$recompile = true;
			delete_transient( 'plethora_module_wpless_trigger_recompile' ); // we don't need this anymore
		}

		// Set $recompile to true, if this is one of the hooks triggered by WP events or theme options / related modules.
		$allowed_actions = array(
			'redux/options/'.THEME_OPTVAR.'/reset',
			'redux/options/'.THEME_OPTVAR.'/section/reset',
			'redux/options/'.THEME_OPTVAR.'/saved',
			'plethora_demo_import_finished',
			'after_switch_theme',
			'activated_plugin'
		);
		if( in_array( $current_filter, $allowed_actions )  ) {

			$recompile = true;
		}

		// Recompile if $recompile is true OR if stylesheets files are missng
		if ( ( $recompile || ( !file_exists( $this->stylesheet_path ) || !file_exists( $this->stylesheet_path_min ) ) ) && ! get_transient( 'plethora_module_wpless_already_recompiled' ) ) { // transient use to avoid multiple compilation during load ( mostly for 'activated_plugin' hook )

			// Make sure a plethora folder is set under uploads dir
			if ( Plethora_Helper::create_plethora_dir() ) {
				$this->load_parser();
				$plethora_wpless_stylesheet_ver = time();
				update_option( 'plethora_wpless_stylesheet_ver', $plethora_wpless_stylesheet_ver );

				// Minified version first
				$stylesheet_contents  = $this->get_stylesheet_intro("minified");
				$stylesheet_contents .= $this->get_stylesheet_contents( true );
				Plethora_Helper::write_to_file( $this->stylesheet_path_min, $stylesheet_contents );
				// Unminified version first
				$stylesheet_contents  = $this->get_stylesheet_intro();
				$stylesheet_contents .= $this->get_stylesheet_contents();
				Plethora_Helper::write_to_file( $this->stylesheet_path, $stylesheet_contents );
				set_transient( 'plethora_module_wpless_already_recompiled', true, 5 ); // 5 seconds should be enough for a page load
			}
		}
	}

	/**
	* Loads class file and registers the parser class
	*/
	protected function load_parser() {

		// require_once PLE_FLIB_FEATURES_DIR .'/module/wpless/parser/Autoloader.php';
		require_once PLE_FLIB_FEATURES_DIR .'/module/wpless/parser-new/Autoloader.php';
		Less_Autoloader::register();
	}

	/**
	* Stylesheet Intro: returns intro comment section for the stylesheet
	*/
	public function get_stylesheet_intro( $options = '' ) {

		if ( $options == "minified" ){
			$intro  = "/*\n";
			$intro .= "Theme Name: ". THEME_DISPLAYNAME ."\n";
			$intro .= "Version: ". THEME_VERSION ."\n";
			$intro .= "Author: ". THEME_AUTHOR ."\n";
			$intro .= "*/\n";
			return $intro;
		} else {
			$intro  = "/*\n";
			$intro .= " ______ _____   _______ _______ _______ _______ ______ _______  \n";
			$intro .= "|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   | \n";
			$intro .= "|    __/       |    ___| |   | |       |   -   |      <       | \n";
			$intro .= "|___|  |_______|_______| |___| |___|___|_______|___|__|___|___| \n";
			$intro .= "\n";
			$intro .= "P L E T H O R A T H E M E S . C O M                    (c) ".date('Y')."\n";
			$intro .= "Theme Name: ". THEME_DISPLAYNAME ."\n";
			$intro .= "Version: ". THEME_VERSION ."\n";
			$intro .= "Author: ". THEME_AUTHOR ."\n";
			$intro .= "\n";
			$intro .= "==================== STYLESHEET INDEX =========================\n";
			$intro .= "\n";
			$count = 0;
			$less_parts = $this->get_less_parts();
			foreach ( $less_parts as $less_part ) {

				$intro .= $this->get_stylesheet_intro_header( $less_part, $count );
			}
			$intro .= "\n";
			$intro .= "===============================================================\n";
			$intro .= "*/\n";
		}
		return $intro;
	}

	/**
	* Stylesheet Intro: returns intro section header for the given less part
	* @param $less_part ( array ) : less part configuration
	* @param $count ( int ) : part count, passed by reference
	*/
	function get_stylesheet_intro_header( $less_part, &$count = 0 ) {

		$return = '';
		$header = $less_part['index_header'];
		$content   = !empty( $less_part['less_file'] ) ? $this->get_less_part_path( $less_part ) : $less_part['less_string'];
		if ( !empty( $header ) && ( !empty( $content ) ) ) {

			$count++;
			$theme_slug      = !is_null( $less_part['theme'] ) && $less_part['theme'] === 'child' ? THEME_SLUG .'-child/' : THEME_SLUG .'/';
			$override_status = empty( $less_part['override'] ) ? '( cannot override this file on child theme )' : '';
			$file_safe       = str_replace( get_template_directory(), '', $less_part['less_file'] );
			$file_safe       = str_replace( get_stylesheet_directory(), '', $file_safe );
			$file_info       = !empty( $less_part['less_file'] ) ? ' | file: ' . $theme_slug . ltrim( $file_safe, "/" )  : '';
			return sprintf("%02d", $count) .'. '. strtoupper( $header ) . $file_info . $override_status ."\n";
		}

		return $return;
	}

	/**
	* Stylesheet main contents: returns full CSS content
	* @param $count ( int ) : true for minified contents output
	*/
	public function get_stylesheet_contents( $minified = false ) {

		// Checked if this is saved to variable, to avoid additional parsing
		if ( ! $minified && !empty( $this->stylesheet_contents ) ) { return $this->stylesheet_contents; }
		if ( $minified && !empty( $this->stylesheet_contents_min ) ) { return $this->stylesheet_contents_min; }

		$stylesheet_contents     = '';
		$options['compress']     = $minified ? true : false;
		$options['relativeUrls'] = false;
		$parser                  = new Less_Parser( $options );
		$less_parts              = $this->get_less_parts();
		$count                   = 0;
		try{

			$parser->SetImportDirs( $this->get_import_directory() );
			$parser->ModifyVars( $this->get_less_variables() );
			foreach ( $less_parts as $less_part ) {

				$this->parse_less_part( $parser, $less_part, $count, $minified );
			}
			$stylesheet_contents = $parser->getCss();

		} catch(Exception $e){

			error_log( 'Impossible to compile the main stylesheet. '. $e->getTraceAsString() );
		}
		// save to variable, to avoid additional parsing
		if ( ! $minified ) { $this->stylesheet_contents = $stylesheet_contents; }
		if (  $minified  ) { $this->stylesheet_contents_min = $stylesheet_contents; }

		return $stylesheet_contents;
	}

	/**
	* Stylesheet main contents: parses full css content for the given part
	* Created for use within the less part loop, all params are passed by reference
	* @param $parser ( object ) : the parser object, initiated before parts loop
	* @param $less_part ( array ) : less part configuration
	* @param $count ( int ) : part count
	*/
	public function parse_less_part( &$parser, &$less_part, &$count, $minified = false ) {

		$less_file_dir       = isset( $less_part['less_file_dir'] ) ? $less_part['less_file_dir'] : site_url();
		$less_part_path = $this->get_less_part_path( $less_part );

		// Parse comments section ( only if we have a ruleset string or file )
		if ( !$minified && ( !empty( $less_part_path ) || !empty( $less_part['less_string'] ) ) ) {

			$less_part_comment = $this->get_less_part_comment( $less_part, $count );
			$parser->parse( $less_part_comment );
		}
		// Parse direct ruleset string section
		if ( !empty( $less_part['less_string'] ) ) {

			$parser->parse( $less_part['less_string'] );
		}

		// Parse file section
		if ( ! empty( $less_part_path ) ) {

			$parser->parseFile( $less_part_path, $less_file_dir );
		}
	}

	/**
	* Stylesheet main contents: returns comment section for the given part
	* @param $less_part ( array ) : less part configuration
	* @param $count ( int ) : part count ( pass by reference )
	*/
	public function get_less_part_comment( $less_part, &$count = 0 ) {

		$header = $less_part['comment_header'];
		$desc   = $less_part['comment_text'];
		$comment = '';
		$comment_cols = 100;
		if (! empty( $header ) || ! empty( $desc ) ) {

			$comment = "\n/*\n";
			if ( ! empty( $header ) ) {

				$count++;
				$header            = sprintf("%02d", $count) .'. '. $header;
				$header_count      = strlen( $header );
				$header_dash_count = ceil( ( $comment_cols - $header_count ) / 2 );

				$comment .= $this->get_dashes_line( $header_dash_count ) .'  ';
				$comment .= $header;
				$comment .= '  ' . $this->get_dashes_line( $header_dash_count ) ."\n";
			}
			if ( ! empty( $desc ) ) {

				$dashes_line_count = strlen( $comment ) - 6;
				$comment .= $desc ."\n";
				$comment .= $this->get_dashes_line( $dashes_line_count ) ."\n";
			}
			$comment .= "*/\n";
		}
		return $comment;
	}

	public function get_dashes_line( $num ) {

		$dashes_line = '';
		for ( $i = 0; $i < $num + 1 ; $i++ ) {

			$dashes_line .= '-';
		}
		return $dashes_line;
	}

	/**
	* Stylesheet main contents: returns import directory configuration
	*/
	public function get_import_directory() {

		return array( get_template_directory() .'/' => '/' );
	}

	/**
	* Stylesheet main contents: returns file path directory for given less part
	*/
	public function get_less_part_path( &$less_part ) {

		$less_file = $less_part['less_file'];
		$less_part['theme'] = 'parent';
		if ( empty( $less_file ) ) { return ''; }

		$child_dir_path  = get_stylesheet_directory() .'/'. $less_file;
		$parent_dir_path = get_template_directory() .'/'. $less_file;
		// check child theme location first
		if ( $less_part['override'] && is_child_theme() && file_exists( $child_dir_path ) ) {

			$less_part['theme'] = 'child';
			return $child_dir_path;

		// check parent theme location
		} elseif ( file_exists( $parent_dir_path ) ) {

			return $parent_dir_path;

		// check if $less_file is an actual path
		} elseif ( file_exists( $less_file ) ) {

			$less_part['theme'] = null;
			return $less_file;
		}

		return '';
	}

	/**
	* Stylesheet main contents: returns less parts configuration
	*/
	function get_less_parts() {

		if ( !empty( $this->less_parts ) ) { return $this->less_parts; }

		$core_parts     = $this->get_less_core_parts();
		$feature_parts  = $this->get_less_feature_parts();
		$less_parts_raw = array_merge( $core_parts, $feature_parts );
		$less_parts     = array();
		foreach ( $less_parts_raw as $less_part ) {

			$less_parts[] = wp_parse_args( $less_part, array(
					'index_header'   => '',
					'comment_header' => '',
					'comment_text'   => '',
					'less_string'    => '',
					'less_file'      => '',
					'override'       => true,
					'less_file_dir'  => false,
				)
			);
		}

		$this->less_parts = $less_parts;
		return $this->less_parts;
	}

	/**
	* Stylesheet main contents: returns core less parts configuration
	* 'index_header': header for the intro section ( string )
	* 'comment_header': header for the comment section displayed right before each part CSS ( string )
	* 'comment_text': description for the comment section displayed right before each part CSS ( string )
	* 'less_file' / 'less_string': the LESS content source, file or simple string
	*/
	public function get_less_core_parts() {

		if ( !empty( $this->less_core_parts ) ) { return $this->less_core_parts; }

		$core_parts['bootstrap-custom'] = array(
			'index_header'   => 'CUSTOM BOOTSTRAP',
			'comment_header' => 'CUSTOM BOOTSTRAP',
			'comment_text'   => '',
			'less_file' 	 => 'assets/less/theme_custom_bootstrap.less',
			'override'  	 => false,
		);
		$core_parts['bootstrap-variables'] = array(
			'index_header'   => 'BOOTSTRAP VARIABLES',
			'comment_header' => 'BOOTSTRAP VARIABLES',
			'comment_text'   => 'Bootstrap\'s original variables.',
			'less_file' 	 => 'assets/twitter-bootstrap/less/variables.less',
			'override'  	 => false,
		);
		$core_parts['bootstrap-mixins'] = array(
			'index_header'   => 'BOOTSTRAP MIXINS',
			'comment_header' => 'BOOTSTRAP MIXINS',
			'comment_text'   => 'Bootstrap\'s original mixins.',
			'less_file' 	 => 'assets/twitter-bootstrap/less/mixins.less',
			'override'  	 => false,
		);
		$core_parts['bootstrap-tables'] = array(
			'index_header'   => 'BOOTSTRAP TABLES',
			'comment_header' => 'BOOTSTRAP TABLES',
			'comment_text'   => 'Bootstrap\'s original tables.',
			'less_file' 	 => 'assets/twitter-bootstrap/less/tables.less',
			'override'  	 => false,
		);
		$core_parts['theme-variables'] = array(
			'index_header'   => 'THEME VARIABLES',
			'comment_header' => 'THEME VARIABLES',
			'comment_text'   => 'Contains all LESS variables, most of them controlled by the theme options panel',
			'less_file' 	 => 'assets/less/theme_variables.less',
		);
		$core_parts['theme-helpers'] = array(
			'index_header'   => 'THEME MIXINS GENERAL HELPER STYLES & CLASSES',
			'comment_header' => 'THEME MIXINS GENERAL HELPER STYLES & CLASSES',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/helpers.less',
		);
		$core_parts['theme-body-typography'] = array(
			'index_header'   => 'BODY & TYPOGRAPHY',
			'comment_header' => 'BODY & TYPOGRAPHY',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/body_and_typography.less',
		);
		$core_parts['theme-header'] = array(
			'index_header'   => 'THE HEADER, LOGO & PRIMARY MENU',
			'comment_header' => 'THE HEADER, LOGO & PRIMARY MENU',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/header.less',
		);
		$core_parts['theme-main-sections'] = array(
			'index_header'   => 'THE MAIN AREA',
			'comment_header' => 'THE MAIN AREA',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/main_sections.less',
		);
		$core_parts['theme-buttons'] = array(
			'index_header'   => 'BUTTONS',
			'comment_header' => 'BUTTONS',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/buttons.less',
		);
		$core_parts['theme-forms'] = array(
			'index_header'   => 'FORMS',
			'comment_header' => 'FORMS',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/forms.less',
		);
		$core_parts['theme-design-elements'] = array(
			'index_header'   => 'DESIGN ELEMENTS',
			'comment_header' => 'DESIGN ELEMENTS',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/design_elements.less',
		);
		$core_parts['theme-post-types'] = array(
			'index_header'   => 'POST TYPES',
			'comment_header' => 'POST TYPES',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/post_types.less',
		);
		$core_parts['theme-sidebar-widgets'] = array(
			'index_header'   => 'SIDEBAR AND WIDGETS',
			'comment_header' => 'SIDEBAR AND WIDGETS',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/sidebar_and_widgets.less',
		);
		$core_parts['theme-footer'] = array(
			'index_header'   => 'FOOTER WIDGETIZED AREA',
			'comment_header' => 'FOOTER WIDGETIZED AREA',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/footer.less',
		);
		$core_parts['theme-misc'] = array(
			'index_header'   => 'MISC STYLES',
			'comment_header' => 'MISC STYLES',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/misc.less',
		);
		$core_parts['theme-responsive'] = array(
			'index_header'   => 'RESPONSIVE STATES',
			'comment_header' => 'RESPONSIVE STATES',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/responsive.less',
		);
		$core_parts['theme-vc'] = array(
			'index_header'   => 'VISUAL COMPOSER ADJUSTMENTS',
			'comment_header' => 'VISUAL COMPOSER ADJUSTMENTS',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/visual_composer_adjustments.less',
		);
		$core_parts['theme-wordpress'] = array(
			'index_header'   => 'WORDPRESS ADJUSTMENTS',
			'comment_header' => 'WORDPRESS ADJUSTMENTS',
			'comment_text'   => '',
			'less_file'      => 'assets/less/includes/wordpress.less',
		);

		$this->less_core_parts = apply_filters( 'plethora_module_wpless_core_parts', $core_parts );
		return $this->less_core_parts;
	}

	/**
	* Stylesheet main contents: returns features less parts configuration
	*/
	public function get_less_feature_parts() {

		if ( !empty( $this->less_feature_parts ) ) { return $this->less_feature_parts; }

		$feature_parts = array();
		$controllers   = Plethora_Theme::get_controllers();
		foreach ( $controllers as $controller ) {

			$features = Plethora_Theme::get_features( array( 'controller' => $controller['slug'] ) );
			foreach ( $features as $feature ) {

				if ( $feature['theme_option_status'] ) {

					$path = $this->get_feature_less_file_location( $feature );

					if ( !empty( $path ) ) {

						$slug                 = 'feature-'. $feature['controller'] .'-'. $feature['slug'];
						$comment_header       = strtoupper( $feature['feature_title'] );
						$comment_text         = 'Styles for the '. $feature['feature_title'] .'';
						$feature_parts[$slug] = array(
							'index_header'   => $comment_header,
							'comment_header' => $comment_header,
							'comment_text'   => $comment_text,
							'less_file'      => $path,
						);
					}
				}
			}
		}
		$this->less_feature_parts = apply_filters( 'plethora_module_wpless_feature_parts', $feature_parts );
		return $this->less_feature_parts;
	}

	/**
	* Stylesheet main contents: returns feature file location
	*
	* Checks whether a feature file exists under the core LESS directory and returns path accordingly
	*
	* @param $feature ( array ) : feature configuration
	*/
	public function get_feature_less_file_location( $feature ) {

		$controller             = $feature['controller'];
		$slug                   = $feature['slug'];
		$file                   = $feature['controller'] .'-'. $feature['slug'] .'.less';
		$rel_file_path_assets   = 'assets/less/includes/elements/'.  $file ;
		$rel_file_path_features = 'features/'. $controller .'/'. $slug .'/'. $file ;

		$assets_file   = Plethora_Theme::get_file_hierarchy_info( $rel_file_path_assets, 'dir_path' );

		if ( !empty( $assets_file ) ) {

			return $assets_file;
		}

		$features_file = Plethora_Theme::get_file_hierarchy_info( $rel_file_path_features, 'dir_path' );
		if ( !empty( $features_file ) ) {

			return $features_file;
		}

		return '';
	}

	/**
	* Set LESS variables configuration
	*
	* @param $vars ( array ) : LESS variabless in $key => $value array
	* @return void
	* @since 2.0.0 New method added
	*/
	public function set_less_variables( $vars ) {

		$less_variables = $this->less_variables;
		foreach ( $vars as $key => $value ) {

			$less_variables[$key] = $value;
		}
		$this->less_variables = $less_variables;
	}

	/**
	* Returns LESS variables configuration
	*
	* @return array LESS variables in key => value pairs
	* @since 2.0.0 New method added
	*/
	public function get_less_variables() {

		$less_variables = method_exists('Plethora_Themeoptions', 'less_variables') ? Plethora_Themeoptions::less_variables( array() ) : array();
		$less_variables['wp-site-url']            = '\''. site_url() .'\'';
		$less_variables['wp-theme-url']               = '\''. get_template_directory_uri() .'\'';
		$less_variables['wp-content-url']         = '\''. content_url() .'\'';
		$less_variables['wp-content-themes-url']  = '\''. content_url( 'themes') .'\'';
		$less_variables['wp-content-plugins-url'] = '\''. content_url( 'plugins') .'\'';
		return apply_filters( 'plethora_module_wpless_variables', $less_variables );
	}
}