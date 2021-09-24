<?php
/**
 * Redux Typography AJAX Class
 *
 * @class Redux_Core
 * @version 4.0.0
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_AJAX_Typography', false ) ) {

	/**
	 * Class Redux_AJAX_Typography
	 */
	class Redux_AJAX_Typography extends Redux_Class {

		/**
		 * Redux_AJAX_Typography constructor.
		 *
		 * @param object $parent RedusFramework object.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );
			add_action( 'wp_ajax_redux_update_google_fonts', array( $this, 'google_fonts_update_ajax' ) );
		}

		/**
		 * Update google font array via AJAX call.
		 */
		public function google_fonts_update_ajax() {
			if ( ! isset( $_POST['nonce'] ) || ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'redux_update_google_fonts' ) ) ) {
				die( 'Security check' );
			}

			if ( isset( $_POST['data'] ) && 'automatic' === $_POST['data'] ) {
				update_option( 'auto_update_redux_google_fonts', true );
			}

			if ( ! Redux_Functions_Ex::activated() ) {
				Redux_Functions_Ex::set_activated();
			}

			$fonts = Redux_Helpers::google_fonts_array( true );

			if ( ! empty( $fonts ) && ! is_wp_error( $fonts ) ) {
				echo wp_json_encode(
					array(
						'status' => 'success',
						'fonts'  => $fonts,
					)
				);
			} else {
				$err_msg = '';

				if ( is_wp_error( $fonts ) ) {
					$err_msg = $fonts->get_error_code();
				}

				echo wp_json_encode(
					array(
						'status' => 'error',
						'error'  => $err_msg,
					)
				);
			}

			die();
		}
	}
}
