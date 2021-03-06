<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2019

File Description: Theme Functions file

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

class Plethora_Setup {

	public $theme_slug;         // THEME SLUG
	public $theme_name;         // THEME NAME
	public $theme_ver;          // THEME VERSION
	public $theme_plugins;      // THEME REQUIRED/RECOMMENDED PLUGINS

	function __construct() {

		// Theme Info
		$parent_dir_name = basename( dirname( __FILE__ ) ); // covers possible directory name change
		$theme = wp_get_theme( $parent_dir_name );          // always get info by parent theme directory name
		$this->theme_slug    = $theme->get( 'TextDomain' );
		$this->theme_name    = $theme->get( 'Name' );
		$this->theme_ver     = $theme->get( 'Version' );

		// Theme required / suggested plugins
		$this->theme_plugins = array(
				'plethora-featureslib' => array( 'version' => '1.7.7' ),
				'js_composer'          => array( 'version' => '6.4.2' ),
				'contact-form-7'       => array( 'version' => '4.3' ),
				'envato-market'        => array( 'version' => '2.0.1' ),
		);

		// Core DIRs
		define( 'PLE_THEME_DIR',              get_template_directory() );           // Theme folder
		define( 'PLE_THEME_INCLUDES_DIR',     PLE_THEME_DIR . '/includes' );            // Theme includes folder
		// Core URIs
		define( 'PLE_THEME_URI',              get_template_directory_uri() );       // Theme folder
		define( 'PLE_THEME_INCLUDES_URI',     PLE_THEME_URI . '/includes' );            // Theme includes folder

		// Perform some PHP version diagnostics check before anything else
		$php_version_approved = $this->approve_php_version();

		// Instantiate the theme class, if Plethora Framework is installed and PHP version diagnostics are fine
		if ( $php_version_approved ) {

			# Load Core, Plethora_Theme extension class and TGM class
			require_once( PLE_THEME_INCLUDES_DIR . '/core/helpers/plethora-tgm.php' );
			require_once( PLE_THEME_INCLUDES_DIR . '/core/plethora.php' );
			require_once( PLE_THEME_INCLUDES_DIR . '/theme.php' );


			# Create the theme class
			global $plethora_theme;
			$plethora_theme = new Plethora_Theme( $this->theme_slug, $this->theme_name, $this->theme_ver );

			# TGM configuration
			add_action( 'tgmpa_register', array( $this, 'tgm_init' ) );

			# Tasks performed after theme update
			$this->after_update();

			# Theme adjustments if the library plugin is inactive
			if ( ! Plethora_Theme::is_library_active() ) {

				// Add support for post and page post types ( necessary for content to be displayed )
				add_filter('plethora_supported_post_types', array($this, 'add_basic_posttypes_support' ));

				// Enqueue Google fonts manually
				add_action( 'wp_enqueue_scripts', array($this, 'add_google_fonts_manually' ), 5);

				// Themeconfig for PARTICLES
				Plethora_Theme::set_themeconfig( "PARTICLES", array(
						'enable'          => true,
						'color'           => "#bcbcbc",
						'opacity'         => 0.8,
						'bgColor'         => "transparent",
						'bgColorDark'     => "transparent",
						'colorParallax'   => "#4D83C9",
						'bgColorParallax' => "transparent",
				));
			}

			# Other notices/fixes here

		} else {

			# Handle frontend error message
			if ( !is_admin() ) {

				$title          = esc_html__( 'This website is under technical maintenance.', 'healthflex' ) ;
				$output         = '<h1>'. $title .'</h1>';
				$output        .= '<p>';
				$output        .= esc_html__( 'Due to a technical upgrade, our website will be out of order for a while. Please try again later.', 'healthflex' );
				$output        .= '</p>';
				$output        .= '<p>';
				$output        .= esc_html__( 'Thank you very much!', 'healthflex' ) .'<br>';
				$output        .= '</p>';
				wp_die( $output, $title );
			}
		}
	}

	/**
	* Check if PHP Version is less than 5.4
	*/
	public function approve_php_version() {

		$approve_php_version = false;

		if ( version_compare(PHP_VERSION, '5.4.0') >= 0 ) {

			$approve_php_version = true;


		} else {

			$approve_php_version = false;
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
		}

		return $approve_php_version;
	}

	/**
	* Admin notice for PHP versions earlier than 5.4
	*/
	public function php_version_notice() {

		if ( isset( $_GET['plethora_php_version_notice'] ) && sanitize_key( $_GET['plethora_php_version_notice'] ) === 'hide' ) {

			set_transient( 'plethora_php_version_notice2', 'hide', HOUR_IN_SECONDS );
		}

		$notice_status = get_transient( 'plethora_php_version_notice2' );

		if ( $notice_status !== 'hide' ) {

			$plethora_link  = 'http://plethorathemes.com/blog/dropping-support-for-php-5-3-x/';
			$wp_link        = 'https://wordpress.org/about/requirements/';
			$output         = '<h4 style="margin:0 0 10px;">'. esc_html__( 'Your installation is running under PHP ', 'healthflex' ) ;
			$output        .= '<strong>'. PHP_VERSION .'</strong> '.'</h4>';
			$output        .= esc_html__( 'To continue working with this theme, you have to upgrade your PHP to 5.4 or newer version.', 'healthflex' ) .'<br>';
			$output        .= esc_html__( 'Unfortunately we cannot ignore the fact that this PHP version is considered obsolete, non secure and with poor overall performance.', 'healthflex' ) .'<br>';
			$output        .= '<strong>'. esc_html__( 'Please help us to deliver high quality and secure products...contact your host and ask for a switch to PHP 5.4 or newer.', 'healthflex' ) .'</strong>' .'<br>';
			$output        .= esc_html__( 'This is a simple procedure that any decent hosting company should provide hassles-free. This restriction will disappear after switching to PHP 5.4 or newer.', 'healthflex' );
			$output        .= '<p>';
			$output        .= '<a href="'. esc_url( $plethora_link ) .'" target="_blank"><strong>'. esc_html__( 'Read more on our blog', 'healthflex' ) .'</strong></a> | ';
			$output        .= '<a href="'. esc_url( $wp_link ) .'" target="_blank"><strong>'. esc_html__( 'Read more on WordPress recommended host configuration', 'healthflex' ) .'</strong></a> | ';
			$output        .= '<a href="'.admin_url( '/') .'?plethora_php_version_notice=hide"><strong>'. esc_html__( 'Dismiss this notice', 'healthflex' ) .'</strong></a>';
			$output        .= '</p>';
			echo '<div class="notice notice-error is-dismissible"><p>'. $output .'</p></div>';
		}
	}

	/**
	* Initiates TGM class
	* Hooked @ 'tgmpa_register'
	*/
	public function tgm_init() {
		$fixed_notice = '<div><small>';
		$fixed_notice .= sprintf( esc_html__( 'This notice is produced by the %s theme', 'healthflex' ), 'Healthflex' );
		$fixed_notice .= '</small></div>';
		if ( is_admin() ) { // no need if not in admin
			$config            = array(
				'domain'            => $this->theme_slug,           // Text domain - likely want to be the same as your theme.
				'default_path'      => '',                          // Default absolute path to pre-packaged plugins
				'menu'              => 'install-required-plugins',  // Menu slug
				'has_notices'       => true,                        // Show admin notices or not
				'is_automatic'      => false,                       // Automatically activate plugins after installation or not
				'message'           => '',                          // Message to output right before the plugins table
				'strings'           => array(
					'page_title'                      => esc_html__( 'Install Required Plugins', 'healthflex' ),
					'menu_title'                      => esc_html__( 'Install Plugins', 'healthflex' ),
					'installing'                      => esc_html__( 'Installing Plugin: %s', 'healthflex' ), // %1$s = plugin name
					'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'healthflex' ),
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'healthflex' ),
					'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'healthflex' ),
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'healthflex' ), // %1$s = plugin name(s)
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'healthflex' ),
					'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s. Note that recommended plugins are usually associated with some features display, however they are not necessary if you don\'t plan to use them.', 'The following recommended plugins are currently inactive: %1$s. Note that recommended plugins are usually associated with some features display, however they are not necessary if you don\'t plan to use them.', 'healthflex' ), // %1$s = plugin name(s)
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'healthflex'), // %1$s = plugin name(s)
					'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'healthflex' ), // %1$s = plugin name(s)
					'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'healthflex' ), // %1$s = plugin name(s)
					'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'healthflex' ),
					'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'healthflex' ),
					'return'                          => esc_html__( 'Return to Required Plugins Installer', 'healthflex' ),
					'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'healthflex' ),
					'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'healthflex' ), // %1$s = dashboard link
					'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
				)
			);

			$plugins = $this->tgm_get_plugins();

			if ( !empty( $plugins ) ) {

				tgmpa( $plugins, $config );
			}
		}
	}

	/**
	* Includes all TGM plugins index, along with their configuration
	* It returns an array merged with $this->theme_plugins variable config,
	* and it's ready for TGM class initiation
	*/
	public function tgm_get_plugins() {

		// REQUIRED: Plethora Features Library
		$plugins['plethora-featureslib'] = array(
				'name'               => 'Plethora Features Library',     // PLUGIN NAME
				'slug'               => 'plethora-featureslib',                // PLUGIN SLUG (Typically: folder name)
				'source'             => PLE_THEME_DIR . '/includes/plugins/plethora-featureslib.zip', // PLUGIN SOURCE
				'required'           => true, // If false, the plugin is only 'recommended' instead of required
		);

		// REQUIRED: WPBakery Visual Composer
		$plugins['js_composer'] = array(
				'name'     => 'WPBakery Visual Composer',
				'slug'     => 'js_composer',
				'source'   => PLE_THEME_DIR . '/includes/plugins/js_composer.zip',
				'required' => true,
		);

		// SUGGESTED: Contact Form 7
		$plugins['contact-form-7'] = array(
				'name'     => 'Contact Form 7',
				'slug'     => 'contact-form-7',
				'required' => false,
		);

		// SUGGESTED: Envato Market
		$plugins['envato-market'] = array(
				'name'     => esc_html__( 'Envato Market', 'healthflex' ),
				'slug'     => 'envato-market',
				'source'   => PLE_THEME_DIR . '/includes/plugins/envato-market.zip',
				'required' => false,
		);

		$tgm_plugins = array();
		$theme_plugins = apply_filters( 'plethora_theme_plugins', $this->theme_plugins );
		foreach ( $plugins as $plugin_slug => $plugin_tgm_config ) {

			if ( !empty( $theme_plugins[$plugin_slug]['version'] ) ) {

				$plugin_tgm_config['version'] = $theme_plugins[$plugin_slug]['version'];
				$tgm_plugins[] = $plugin_tgm_config;
			}
		}
		return $tgm_plugins;
	}


	/**
	* The method compares theme saved version with this one running.
	* If different, it executes all actions set right after theme update
	*
	* @since 1.0
	*
	*/
	public function after_update() {

	  $theme_version_db = get_option( OPTNAME_THEME_VER, false );
	  if ( $theme_version_db && version_compare( $this->theme_ver, $theme_version_db ) !== 0 ) {

		// Recovers TGM notices, even if the user has dismissed this.
		// MUST be done on every theme update, to make sure the current user gets a notice about the Plethora Framework plugin update
		$deleted = delete_metadata( 'user', null, 'tgmpa_dismissed_notice_tgmpa', null, true );

		## START: Add any theme update actions should be hooked here!
		do_action( 'plethora_after_update' );
		## FINISH

		// After done with all actions, we update saved theme version ( for version switches only )
		$is_updated = update_option( OPTNAME_THEME_VER, $this->theme_ver );

	  } elseif ( ! $theme_version_db ) {

		// Create saved theme version ( for version switches only )
		$is_saved = update_option( OPTNAME_THEME_VER, $this->theme_ver );
		// Initial theme version
		$is_initial = update_option( 'plethora_theme_ver_installed_initial', $this->theme_ver );
	  }
	}

	/**
	* Enqueue Google fonts manually
	*/
	public function add_google_fonts_manually() {

		wp_enqueue_style( 'roboto', 'http://fonts.googleapis.com/css?family=Raleway:400,300,700,800,900,500', false );
		wp_enqueue_style( 'lato', 'http://fonts.googleapis.com/css?family=Lato:300,300italic,700,700italic,900,900italic', false );
	}


	/**
	* Add support for post/page if the library plugin is inactive
	*/
	public function add_basic_posttypes_support( $posttypes ) {

	  $posttypes[] = 'post';
	  $posttypes[] = 'page';
	  array_unique($posttypes);
	  return $posttypes;
	}
}

$setup = new Plethora_Setup;