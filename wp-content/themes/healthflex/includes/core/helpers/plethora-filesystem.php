<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

All helper methods used on all our codebase.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

class Plethora_Filesystem {

	protected static $instance = null;
	protected static $direct = null;
	private $creds = array();
	public $fs_object = null;
	public $parent = null;
	public $ftp_form;

	public function __construct() {

		add_action('admin_menu', array( $this, 'admin_filesystem_page' ), 9999 );
	}

	// add the admin options page
	function admin_filesystem_page() {
		add_submenu_page(
			'plethora_options',
			esc_html__( 'Filesystem check', 'plethora-framework' ),
			esc_html__( 'Filesystem check', 'plethora-framework' ),
			'manage_options',
			'plethora_filesystem_creds',
			array( $this, 'get_credential_form' ),
			 9999
		);
	}

	function get_credential_form() {

		echo '<h1>Plethora Filesystem</h1>';


		if ( ! empty( $this->ftp_form ) ) {

			echo '<p>';
			echo esc_html__( 'Plethora filesystem is not working as expected', 'plethora-framework' );
			echo '</p>';
			echo trim( $ftp_form );

		} else {

			echo '<p>';
			echo esc_html__( 'Plethora filesystem is working as expected!', 'plethora-framework' );
			echo '</p>';
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function execute( $action, $file = '', $params = array() ) {

		// Setup the filesystem with creds
		require_once ABSPATH . '/wp-admin/includes/template.php';
		require_once ABSPATH . '/wp-includes/pluggable.php';
		require_once ABSPATH . '/wp-admin/includes/file.php';

		$base = 'themes.php?page=plethora_filesystem_creds';
		$url  = wp_nonce_url( $base, 'plethora_filesystem_creds' );
		$this->filesystem_init( $url, 'direct', dirname( $file ) );

		return $this->do_action( $action, $file, $params );
	}



	public function filesystem_init( $form_url, $method = '', $context = false, $fields = null ) {

		global $wp_filesystem;

		if ( ! empty( $this->creds ) ) { return true;  }

		ob_start();

		/* first attempt to get credentials */
		if ( false === ( $this->creds = request_filesystem_credentials( $form_url, $method, false, $context ) ) ) {
			$this->creds            = array();
			$this->ftp_form = ob_get_contents();
			ob_end_clean();

			/**
			 * if we comes here - we don't have credentials
			 * so the request for them is displaying
			 * no need for further processing
			 **/

			return false;
		}

		/* now we got some credentials - try to use them*/
		if ( ! WP_Filesystem( $this->creds ) ) {

			$this->creds = array();
			/* incorrect connection data - ask for credentials again, now with error message */
			request_filesystem_credentials( $form_url, '', true, $context );
			$this->ftp_form = ob_get_contents();
			ob_end_clean();

			return false;
		}

		return true;
	}

	public static function load_direct() {

		if ( self::$direct === null ) {

			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
			self::$direct = new WP_Filesystem_Direct( array() );
		}
	}

	public function do_action( $action, $file = '', $params = '' ) {

		if ( ! empty ( $params ) ) { extract( $params ); }

		if ( ! isset( $params['chmod'] ) || ( isset( $params['chmod'] ) && empty( $params['chmod'] ) ) ) {

			if ( defined( 'FS_CHMOD_FILE' ) ) {

				$chmod = FS_CHMOD_FILE;

			} else {

				$chmod = 0644;
			}
		}

		$res = false;
		if ( ! isset( $recursive ) ) {

			$recursive = false;
		}

		global $wp_filesystem;

		// Do unique stuff
		if ( $action == 'is_dir' ) {

			$res = $wp_filesystem->is_dir( $file, $recursive );

		} elseif ( $action == 'mkdir' ) {

			if ( defined( 'FS_CHMOD_DIR' ) ) {

				$chmod = FS_CHMOD_DIR;

			} else {

				$chmod = 0755;
			}

			$res = wp_mkdir_p( $file );

		} elseif ( $action == 'rmdir' ) {

			$res = $wp_filesystem->rmdir( $file, $recursive );

		} elseif ( $action == 'copy' && ! isset( $this->filesystem->killswitch ) ) {

			if ( isset( $this->ftp_form ) && ! empty( $this->ftp_form ) ) {

				$res = copy( $file, $destination );
				if ( $res ) {
					chmod( $destination, $chmod );
				}

			} else {

				$res = $wp_filesystem->copy( $file, $destination, $overwrite, $chmod );
			}

		} elseif ( $action == 'move' && ! isset( $this->filesystem->killswitch ) ) {

			$res = $wp_filesystem->copy( $file, $destination, $overwrite );

		} elseif ( $action == 'delete' ) {

			$res = $wp_filesystem->delete( $file, $recursive );

		} elseif ( $action == 'rmdir' ) {

			$res = $wp_filesystem->rmdir( $file, $recursive );

		} elseif ( $action == 'dirlist' ) {

			if ( ! isset( $include_hidden ) ) {
				$include_hidden = true;
			}
			$res = $wp_filesystem->dirlist( $file, $include_hidden, $recursive );

		} elseif ( $action == 'put_contents' && ! isset( $this->filesystem->killswitch ) ) {

			// Write a string to a file
			if ( isset( $this->ftp_form ) && ! empty( $this->ftp_form ) ) {
				self::load_direct();
				$res = self::$direct->put_contents( $file, $content, $chmod );
			} else {
				$res = $wp_filesystem->put_contents( $file, $content, $chmod );
			}

		} elseif ( $action == 'chown' ) {

			// Changes file owner
			if ( isset( $owner ) && ! empty( $owner ) ) {
				$res = $wp_filesystem->chmod( $file, $chmod, $recursive );
			}

		} elseif ( $action == 'owner' ) {

			// Gets file owner
			$res = $wp_filesystem->owner( $file );

		} elseif ( $action == 'chmod' ) {

			if ( ! isset( $params['chmod'] ) || ( isset( $params['chmod'] ) && empty( $params['chmod'] ) ) ) {
				$chmod = false;
			}

			$res = $wp_filesystem->chmod( $file, $chmod, $recursive );

		} elseif ( $action == 'get_contents' ) {

			// Reads entire file into a string
			if ( isset( $this->ftp_form ) && ! empty( $this->ftp_form ) ) {
				self::load_direct();
				$res = self::$direct->get_contents( $file );
			} else {
				$res = $wp_filesystem->get_contents( $file );
			}

		} elseif ( $action == 'get_contents_array' ) {

			// Reads entire file into an array
			$res = $wp_filesystem->get_contents_array( $file );

		} elseif ( $action == 'object' ) {

			$res = $wp_filesystem;

		} elseif ( $action == 'unzip' ) {

			$unzipfile = unzip_file( $file, $destination );
			if ( $unzipfile ) {
				$res = true;
			}
		}

		if ( ! $res ) {

			if ($action == 'dirlist') {

				if ( empty( $res ) || $res == false || $res == '' ) {

					return;
				}

				if ( is_array( $res ) && empty( $res ) ) {

					return;
				}

				if ( ! is_array( $res ) ) {

					if ( count( glob( "$file*" ) ) == 0 ) {
						return;
					}
				}
			}

			$this->killswitch = true;
			Plethora_Theme::add_admin_notice( 'plethora_filystem_issue', array(
					'condition'         => true,
					'theme'             => array( THEME_SLUG => '1.0.0' ),
					'theme_update_only' => false,
					'title'             => __( 'File Permission Issues', 'plethora-framework' ),
					'notice'            => '<strong>' . __( 'File Permission Issues', 'plethora-framework' ) . '</strong><br/>' . sprintf( __( 'We were unable to modify required files. Please ensure that <code>%1s</code> has the proper read-write permissions, or modify your wp-config.php file to contain your FTP login credentials as <a href="%2s" target="_blank">outlined here</a>.', 'plethora-framework' ), trailingslashit( WP_CONTENT_DIR ) . '/uploads/', 'https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants' ),
					'type'              => 'error',
					'dismiss_text'      => esc_html__( 'Dismiss this notice', 'plethora-framework' ),
					'links'             => array(),
				)
			);
		}

		return $res;
	}
}