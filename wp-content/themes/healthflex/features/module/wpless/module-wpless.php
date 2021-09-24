<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2020

WP LESS Compiler Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Wpless') && !class_exists('Plethora_Module_Wpless_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-framework/features/module/module-wpless.php
	*/
	class Plethora_Module_Wpless_Ext extends Plethora_Module_Wpless {

		public function __construct(){

			parent::__construct();

			add_filter( 'plethora_module_wpless_core_parts', array( $this, 'add_ecwid_less' ), 99 );
			add_filter( 'plethora_module_wpless_core_parts', array( $this, 'add_rtl_less' ), 99 );
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
			$core_parts['bootstrap-custom'] = array(
				'index_header'   => 'CUSTOM BOOTSTRAP',
				'comment_header' => 'CUSTOM BOOTSTRAP',
				'comment_text'   => '',
				'less_file' 	 => 'assets/less/theme_custom_bootstrap.less',
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
			$core_parts['theme-buttons'] = array(
				'index_header'   => 'BUTTONS',
				'comment_header' => 'BUTTONS',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/buttons.less',
			);
			$core_parts['theme-body-typography'] = array(
				'index_header'   => 'BODY & TYPOGRAPHY',
				'comment_header' => 'BODY & TYPOGRAPHY',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/body.less',
			);
			$core_parts['theme-header'] = array(
				'index_header'   => 'THE HEADER, LOGO & PRIMARY MENU',
				'comment_header' => 'THE HEADER, LOGO & PRIMARY MENU',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/header.less',
			);

			$core_parts['theme-head-panel'] = array(
				'index_header'   => 'THE HEAD PANEL AREA',
				'comment_header' => 'THE HEAD PANEL AREA',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/head_panel.less',
			);
			$core_parts['theme-main-sections'] = array(
				'index_header'   => 'THE MAIN AREA & THE ROOT SECTION ELEMENT',
				'comment_header' => 'THE MAIN AREA & THE ROOT SECTION ELEMENT',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/main_sections.less',
			);
			$core_parts['theme-design-elements'] = array(
				'index_header'   => 'DESIGN ELEMENTS',
				'comment_header' => 'DESIGN ELEMENTS',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/design_elements.less',
			);
			$core_parts['theme-blog-page'] = array(
				'index_header'   => 'BLOG PAGE ELEMENTS',
				'comment_header' => 'DESIGN ELEMENTS',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/blog_page.less',
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
			$core_parts['theme-boorarap-overrides'] = array(
				'index_header'   => 'BOOTSTRAP ELEMENTS OVERRIDES',
				'comment_header' => 'BOOTSTRAP ELEMENTS OVERRIDES',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/bootstrap-extras.less',
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
			$core_parts['theme-wordpress'] = array(
				'index_header'   => 'WORDPRESS ADJUSTMENTS',
				'comment_header' => 'WORDPRESS ADJUSTMENTS',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/wordpress.less',
			);

			$this->less_core_parts = apply_filters( 'plethora_module_wpless_core_parts', $core_parts );
			return $this->less_core_parts;
		}

		public function add_ecwid_less( $core_parts ) {

			$core_parts['theme-ecwid'] = array(
				'index_header'   => 'ECWID STYLES',
				'comment_header' => 'ECWID STYLES',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/ecwid.less',
			);
			return $core_parts;
		}
		public function add_rtl_less( $core_parts ) {

			$core_parts['theme-rtl'] = array(
				'index_header'   => 'RIGHT-TO-LEFT ADJUSTMENTS',
				'comment_header' => 'RIGHT-TO-LEFT ADJUSTMENTS',
				'comment_text'   => '',
				'less_file'      => 'assets/less/includes/rtl.less',
			);
			return $core_parts;
		}
	}
}