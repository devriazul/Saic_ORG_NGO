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

class Plethora_Helper {

	public static $allowed_html_for;


# FILE MANIPULATION METHODS


	/**
	* Read file using Redux WP_Filesystem proxy
	* @return string|boolean
	*/
	static function create_plethora_dir() {

		if ( ! class_exists('Plethora_Filesystem')  ) {

			require PLE_CORE_HELPERS_DIR .'/plethora-filesystem.php';
		}

		$plethora_filesystem = Plethora_Filesystem::get_instance();
		$upload_dir          = wp_upload_dir();
		$plethora_dir        = $upload_dir && !is_wp_error( $upload_dir ) ? $upload_dir['basedir'] .'/plethora/' : false ;

		if ( $plethora_dir && ! $plethora_filesystem->execute( 'is_dir', $plethora_dir ) ) {

			return $plethora_filesystem->execute( 'mkdir', $plethora_dir );

		}

		return true;
	}

	/**
	* Write to file using Redux WP_Filesystem proxy
	* @return boolean
	*/
	static function write_to_file( $file, $content, $args = array() ) {

		if ( ! class_exists('Plethora_Filesystem')  ) {

			require PLE_CORE_HELPERS_DIR .'/plethora-filesystem.php';
		}
		$plethora_filesystem = Plethora_Filesystem::get_instance();
		$args['content'] = $content;
		return $plethora_filesystem->execute( 'put_contents', $file, $args );
	}

	/**
	* Read file using Redux WP_Filesystem proxy
	* @return string|boolean
	*/
	static function get_file_contents( $file, $args = array() ) {

		if ( ! class_exists('Plethora_Filesystem')  ) {

			require PLE_CORE_HELPERS_DIR .'/plethora-filesystem.php';
		}
		$plethora_filesystem = Plethora_Filesystem::get_instance();
		$contents = $plethora_filesystem->execute( 'get_contents', $file, $args );
		return $plethora_filesystem->execute( 'get_contents', $file, $args );
	}


# STRING CONVERSION METHODS

	/**
	 * Manages multibyte version for PHP's ucfirst() function, when there is no PHP support for this
	 * Use when the display of a tag is set dynamically.
	 */
	/*
	public static function mb_ucfirst($string, $encoding = 'utf8') {

		$strlen    = function_exists( 'mb_strlen' ) ? mb_strlen( $string, $encoding ) : strlen( $string );
		$firstChar = function_exists( 'mb_substr' ) ? mb_substr( $string, 0, 1, $encoding ) : substr($string, 0, 1 );
		$restChars = function_exists( 'mb_substr' ) ? mb_substr( $string, 1, $strlen - 1, $encoding ) : substr( $string, 1, $strlen - 1  );
		return function_exists( 'mb_strtoupper' ) ?  mb_strtoupper( $firstChar, $encoding ) . $restChars : strtoupper( $firstChar ) . $restChars ;
	}
	*/

	/**
	 * PUBLIC | Multibyte version for PHP's mb_strtolower() function
	 * Use when the display of a tag is set dynamically.
	 */
	/*
	public static function mb_strtolower($string, $encoding = 'utf8') {

		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, $encoding ) : strtolower( $string );
	}
	*/

	/**
	 * PUBLIC | Multibyte version for PHP's mb_strtolower() function
	 * Use when the display of a tag is set dynamically.
	 */
	/*
	public static function mb_strtoupper($string, $encoding = 'utf8') {

		return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string, $encoding ) : strtoupper( $string );
	}
	*/

	/**
	 * PUBLIC | Multibyte version for PHP's mb_strtolower() function
	 * Use when the display of a tag is set dynamically.
	 */
	/*
	public static function mb_stripos( $haystack, $needle, $offset = 0, $encoding = 'utf8') {

		return function_exists( 'mb_stripos' ) ? mb_stripos( $haystack, $needle, $offset, $encoding ) : strtolower( $haystack, $needle, $offset );
	}
	*/

	/**
	 * PUBLIC | Multibyte version for PHP's mb_substr() function
	 * Use when the display of a tag is set dynamically.
	 */
	/*
	public static function mb_substr( $str, $start, $length, $encoding = 'utf8') {

		return function_exists( 'mb_substr' ) ? mb_substr( $str, $start, $length, $encoding ) : substr( $str, $start, $length, $encoding );
	}
	*/

	/**
	 * Converts all spaces & dashes to underscores for the given string
	 *
	 * @param string $string String to be converted
	 * @return string Returns converted string
	 * @since 2.0
	 */
	/*
	public static function str_underscore( $string ) {

		$string = str_replace(' ', '_', $string );
		$string = str_replace('-', '_', $string );
		return $string;
	}
	*/

	public static function slug_to_title( $slug, $capitalize = 'first', $remove = array() ) {

		$title = $slug;

		// Remove defined strings
		$remove = ! is_array( $remove ) ? array( $remove ) : $remove;
		foreach ( $remove as $remove_item ) {

			$title = !empty( $remove_item ) ? str_replace( $remove_item, '', $title ) : $title;
		}

		// Remove dashes, hyphens and double spaces
		$title = str_replace( '_', ' ', $title );
		$title = str_replace( '-', ' ', $title );
		$title = str_replace( '  ', ' ', $title );

		// Capitalize if necessary
		if ( $capitalize ) {

			$title = $capitalize === 'all' ? ucwords( $title ) : ucfirst( $title );
		}

		return $title;
	}

	/**
	 * Returns true if given string is a valid hex color
	 *
	 * @param string $str
	 * @return boolean
	 */
	/*
	static function is_hex_color_string( $str ) {

		if( preg_match( '/^#[a-f0-9]{6}$/i', $str ) ) {

			return true;
		}

		return false;
	}
	*/

	/**
	 * Returns valid color hex value for the given option id
	 *
	 * @param string $opt_id
	 * @param string $default_val
	 * @return string
	 */
	/*
	static function get_hex_color_optionval( $opt_id, $default_hex ) {

		$opt_hex = Plethora_Theme::option( $opt_id, $default_hex, 0, false );
		if ( self::is_hex_color_string( $opt_hex ) ) {

			return $opt_hex;

		} else {

			return $default_hex;
		}
	}
	*/

	/**
	 * Returns valid color hex value for the given option id
	 *
	 * @param string $opt_id
	 * @param string $default_val
	 * @return string
	 */
	/*
	static function get_hex_color_optionval_array( $opt_id, $opt_hex_key, $default_hex ) {

		$opt_hex = Plethora_Theme::option( $opt_id, array( $opt_hex_key => $default_hex ), 0, false );
		if ( self::is_hex_color_string( $opt_hex[$opt_hex_key] ) ) {

			return $opt_hex[$opt_hex_key];

		} else {

			return $default_hex;
		}
	}
	*/

	/**
	 * Return a formatted string. Operates as vsprintf, but with key specific variables
	 *
	 * @param string $formatted_text
	 * @param string $string_values_array
	 * @return string
	 */
	/*
	static function vsprintf_with_keys( $formatted_text, $string_values_array ) {

		$names = preg_match_all('/\{(.*?)\}/', $formatted_text, $matches, PREG_SET_ORDER);

		$values = array();
		foreach($matches as $match) {

			// check if the match is included on string_values
			if ( isset( $string_values_array[$match[1]] ) ) {

				// include match ( calling again this method, to include keys included in this match! )
				$values[] = self::vsprintf_with_keys( $string_values_array[$match[1]], $string_values_array );
				// $values[] = $string_values_array[$match[1]];

			} else {

				$values[] = '';
			}
			// $values[] = $string_values_array[$match[1]];
		}
		$formatted_text = str_replace('%', '%%', $formatted_text ); // hack to escape sprintf % mark
		$formatted_text = preg_replace('/\{(.*?)}/', '%s', $formatted_text);
		$formatted_text = vsprintf( $formatted_text, $values );
		$formatted_text = str_replace('%%', '%', $formatted_text );	// make douple percentages single again
		return $formatted_text;
	}
	*/

# ARRAY CONVERSION METHODS

	/**
	 * PUBLIC | Similar to wp_parse_args() just a bit extended to work with multidimensional arrays :)
	 */
	/*
	public static function parse_multi_args( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = self::parse_multi_args( $v, $result[ $k ] );

			} else {

				$result[ $k ] = $v;
			}
		}
		return $result;
	}
	*/


	/**
	 * INTERNAL | Converts a repeater field array into a normal loop array
	 * Useful to be used on template functions.
	 */
	/*
	public static function repeater_to_loop( $array ) {

		$return = array();
		if ( is_array( $array ) && !empty( $array['redux_repeater_data'] ) ) {

			$count = count( $array['redux_repeater_data'] );
			unset( $array['redux_repeater_data'] );

			for ($i = 0; $i < $count ; $i++) {

				$items = array()    ;
				foreach ( $array as $array_key => $array_items ) {

					$items[$array_key] = $array_items[$i];
				}

				$return[] = $items;
			}
		}
		return $return;
	}
	*/

	/**
	 * INTERNAL | Converts a loop array into a repeater field value
	 * Useful to set default values in an easier way
	 */
	/*
	public static function loop_to_repeater( $array ) {

		$return = array();
		if ( is_array( $array ) && !empty( $array ) ) {

			foreach ( $array as $repeater_item ) {

				$return['redux_repeater_data'][] = array( 'title' => '' );

				foreach ( $repeater_item as $field_key => $field_val ) {

					$return[$field_key][] = $field_val;
				}
			}
		}
		return $return;
	}
	*/

	/**
	 * Removes given prefix from all array keys and returns the converted array
	 *
	 * @param array $array Array with keys/values to be converted
	 * @param string $prefix Prefix text that needs to be removed
	 * @return string Returns converted string
	 * @since 2.0
	 */
	/*
	public static function arr_remove_key_prefix( $array, $prefix ) {

		$converted_array = array();
		if ( is_array( $array ) && !empty( $prefix ) ) {

			$prefix_len = strlen( $prefix );
			foreach ( $array as $key => $val ) {

				if ( substr( $key, 0, $prefix_len ) === $prefix ) {

					$key = substr( $key, $prefix_len, strlen( $key ) );
				}

				$converted_array[$key] = $val;
			}
		}
		return $converted_array;
	}
	*/

	/**
	 * Converts all spaces & dashes to underscores for the keys/values of the given array of strings
	 *
	 * @param array $array Array with keys/values to be converted
	 * @param sting $what What to convert ( 'all'|'values'|'keys' )
	 * @return array Returns array with converted strings
	 * @since 2.0
	 */
	/*
	public static function arr_underscore( $array = array(), $what = 'all' ) {

		$converted_array = array();
		if ( is_array( $array ) && !empty( $array ) ) {

			foreach ( $array as $key => $val ) {

				switch ( $what ) {
					case 'keys':
						$key = self::str_underscore( $key );
						break;

					case 'values':
						$value = self::str_underscore( $value );
						break;

					default:
						$key   = self::str_underscore( $key );
						$value = self::str_underscore( $value );
						break;
				}

				$converted_array[$key] = $val;
			}
		}
		return $converted_array;
	}
	*/


# HTML RENDERING METHODS

	/**
	* Render Mustache Widget Template
	* @return string
	*/
	/*
	public static function renderMustache( $options ){

		Plethora_Theme::log_deprecated_method( debug_backtrace() );

		$defaults = array(
			"data"     => '',
			"file"     => '',
			"override" => false,
			"module"   => false,
			'force_template_part' => false
		);

		$options = ( isset($options) && is_array($options) )? array_replace_recursive ( $defaults , $options ) : $defaults;

		$pattern       = '/(.*)-(.*).php$/';    // GRAB FEATURE NAME: 'widget-', 'shortode-', etc. FROM FILENAME: 'shortcode-entry.php'
		$full_pathname = ( $options['override'] ) ? "shortcode-" . wp_basename( $options['file'] ) : wp_basename( $options['file'] );

		if ( ! class_exists('Mustache_Engine') ){  require_once PLE_FLIB_LIBS_DIR . '/mustache/mustache.php';  }

		if ( preg_match( $pattern, $full_pathname, $matches, PREG_OFFSET_CAPTURE) || $options['force_template_part'] ){

			$mustache = new Mustache_Engine;
			ob_start();
			if ( $options['force_template_part'] ) {
				$slug = $options['force_template_part'][0];
				$name = isset( $options['force_template_part'][1] ) ? $options['force_template_part'][1] : '';
				Plethora_Helper::get_template_part( $slug, $name );

			} else {

				$feature_dir  = ( $options['module'] ) ? "modules" : $matches[1][0] . "s"; // TURN SINGLE INTO PLURAL: 'widget' => 'widgets', 'shortcode' => 'shortcodes'
				$feature_file = ( $options['module'] ) ? $matches[1][0] . "-" . $matches[2][0] : $matches[2][0];
				Plethora_Helper::get_template_part( "templates/" . $feature_dir . "/" . $feature_file, 'mustache' );
			}

			$mustache_tmpl = ob_get_contents();
			ob_end_clean();

			ob_start();
			echo trim( $mustache->render( $mustache_tmpl, $options['data'] ) );
			return ob_get_clean();
		}
	}
	*/

	/**
	 * Load a template part into a template (other than header, sidebar, footer).
	 * More on http://codex.wordpress.org/Function_Reference/get_template_part
	 */
	/*
	static function get_template_part( $slug, $name = '' ) {

		Plethora_Theme::log_deprecated_method( debug_backtrace() );

		$display_name   = !empty($name) ? $name .'.php' : '';
		$display_slug   = empty( $display_name ) ? $slug .'.php' : $slug .'-';
		$current_filter = function_exists( 'current_filter' ) ? current_filter() : '';
		$display_hook   = !empty( $current_filter )  ? '|| Added as WP action hook @ '. $current_filter : '';
		get_template_part( $slug, $name );
	}
	*/

	/**
	 * PUBLIC | Returns html tag opening part OR self-closing tag, according to given arguments
	 * Use when the display of a tag is set dynamically. Used mostly
	 * for template part inner containers.
	 */
	/*
	public static function get_html_tag_open( $args ) {

		$default_args = array(
			'tag'          => 'div',   // Any non self-closing html tag ( default: 'div' )
			'class'        => '',      // Tag class attribute value
			'id'           => '',      // Tag id attribute value
			'attrs'        => array(), // Any other tag attribute(s), in $name => $value array
			'self_closing' => false,   // Set to true, if this is a self-closing tag
		);
		$args = wp_parse_args( $args, $default_args);
		extract($args);

		if ( empty( $tag ) ) { return ''; } // return empty string if not tag is given

		$return  =  '<'. esc_attr( $tag );
		$return .=  ( ! empty( $class ) ) ? ' class="'. esc_attr( $class ) .'"' : '';
		$return .=  ( ! empty( $id ) ) ? ' id="'. esc_attr( $id ) .'"' : '';
		foreach ( $attrs as $attr_name => $attr_val ) {

			$return .=  ( ! empty( $attr_val ) ) ? ' '. $attr_name .'="'. esc_attr( $attr_val ) .'"' : '';
		}
		$return .=  ( $self_closing ) ? '/>' : '>';
		return $return;
	}
	*/

	/**
	 * PUBLIC | Returns html tag closing part according to given argument
	 * Use when the display of a tag is set dynamically. Used mostly
	 * for template part inner containers.
	 */
	/*
	public static function get_html_tag_close( $tag = 'div') {

		if ( empty( $tag ) ) { return ''; } // return empty string if not tag is given
		$return =  '</'. esc_attr( $tag ) .'>';
		return $return;
	}
	*/

	/**
	 * PUBLIC | Strips given content from HTML comments
	 */
	/*
	public static function remove_html_comments( $content ) {

		return preg_replace('/<!--(.|\s)*?-->/', '', $content);
	}
	*/

# WP INTERMEDIARY METHODS

	/**
	 * Alias method of WordPress function: wp_read_audio_metadata
	 * Makes sure that the source file of wp_read_audio_metadata is loaded
	 */
	/*
	public static function wp_read_audio_metadata( $file ) {

		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		return wp_read_audio_metadata( $file );
	}
	*/

	/**
	 * Returns post information according to the given slug and post type
	 *
	 * @param string $slug The post slug
	 * @param string $post_type The post type
	 * @param string $return 'object' to return post object, 'id' to return the post ID
	 * Makes sure that the source file of wp_read_audio_metadata is loaded
	 */
	/*
	public static function get_post_by_slug( $slug, $post_type = 'post', $return = 'object' ) {

		$post   = false;
		$return = $return === 'object' ? 'object' : 'id';
		$args   = array( 'post_type'=> $post_type, 'posts_per_page' => -1 );
		global $get_post_by_slug_posts;
		$get_post_by_slug_posts[$post_type]  = empty( $get_post_by_slug_posts[$post_type] ) ? get_posts( $args ) : $get_post_by_slug_posts[$post_type] ;
		foreach ( $get_post_by_slug_posts[$post_type] as $post_obj ) {

			if ( $post_obj->post_name === $slug ) {

				$post = $return === 'id' ? $post_obj->ID : $post_obj;
				break;
			}
		}
		// Need to use this admin/frontend separation to reset query. If we use only wp_reset_postdata, it will cause
		// problems on main query loops that call this per post iteration. If we use only rewind_posts, this will cause problems
		// in the frontend ( duplicating posts )...that's why we should apply them in separate.
		if ( is_admin() ) {

			rewind_posts();

		} else {

			wp_reset_postdata();
		}
		return $post;
	}
	*/

# OTHER MISCELLANEOUS METHODS

	/**
	 * PUBLIC | Returns allowed html configuration for several content elements,
	 * ready to use as an wp_kses() function argument
	 */
	/*
	public static function normalize_container_element( $element ) { ... COPY FROM PFL v2 ... }
	*/

	/**
	 * PUBLIC | Returns allowed html configuration for several content elements,
	 * ready to use as an wp_kses() function argument
	 */
	/*
	public static function allowed_html_for( $for, $to_display = false ) {

		if ( !empty( self::$allowed_html_for[$for] ) ) {

			$allowed_html = self::$allowed_html_for[$for];

		} else {

			$allowed_html = array();

			switch ( $for ) {

				case 'post':
					$allowed_html = wp_kses_allowed_html( 'post' );
					break;

				case 'post_with_iframe':
				case 'post_plus':
					$allowed_html = wp_kses_allowed_html( 'post' );
					$allowed_html['iframe'] = array('class' => array(), 'style' => array(), 'height' => array(), 'name' => array(), 'sandbox' => array(), 'src' => array(), 'srcdoc' => array(), 'width' => array(), 'seamless' => array() );
					// add some more special data attributes
					$allowed_html['div'] = array_merge( $allowed_html['div'] , array( 'data' => array(), 'data-audio' => array() ) );
					$allowed_html['a'] = array_merge( $allowed_html['a'] , array( 'download' => array() ) );
					$allowed_html['source'] =  array( 'id' => array(), 'src' => array(), 'type' => array() );
					break;

				case 'heading':
					$allowed_html['a']      = array( 'href' => array(), 'id' => array(), 'class' => array(), 'title' => array(), 'style' => array() );
					$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
					$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
					$allowed_html['br']     = array();
					$allowed_html['em']     = array();
					$allowed_html['strong'] = array();
					$allowed_html['b']      = array();
					break;

				case 'paragraph':
					$allowed_html['a']      = array( 'href' => array(), 'id' => array(), 'class' => array(), 'title' => array(), 'style' => array(), 'width' => array(), 'height' => array() );
					$allowed_html['img']    = array( 'class' => array(), 'title' => array(), 'style' => array(), 'src' => array(), 'width' => array(), 'height' => array(), 'alt' => array()  );
					$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
					$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
					$allowed_html['br']     = array();
					$allowed_html['em']     = array();
					$allowed_html['strong'] = array();
					$allowed_html['b']      = array();
					break;

				case 'button':
					$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
					$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
					$allowed_html['em']     = array();
					$allowed_html['strong'] = array();
					$allowed_html['b']      = array();
					break;

				case 'link':
					$allowed_html['img']    = array( 'class' => array(), 'title' => array(), 'style' => array(), 'src' => array(), 'width' => array(), 'height' => array(), 'alt' => array()  );
					$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
					$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
					$allowed_html['em']     = array();
					$allowed_html['strong'] = array();
					$allowed_html['b']      = array();
					break;

				case 'iframe':
					$allowed_html['iframe'] = array('class' => array(), 'style' => array(), 'height' => array(), 'name' => array(), 'sandbox' => array(), 'src' => array(), 'srcdoc' => array(), 'width' => array(), );
					break;
			}

			self::$allowed_html_for[$for] = $allowed_html;
		}

		if ( $to_display ) {

			$display_allowed_tags = esc_html__( 'Allowed HTML tags: ', 'plethora-framework' );
			$count = 0;
			foreach ( $allowed_html as $tag => $attrs ) {

				$count++;
				$display_allowed_tags .= $count === 1 ? '<strong>'. $tag .'</strong>' : ' | <strong>'. $tag .'</strong>';
			}

			$allowed_html = $display_allowed_tags;
		}

		return $allowed_html;
	}
	*/

	/**
	* Returns escaped button attributes values, ready to be used in a frontend template file
	*
	* @param array $option The option that holds the raw button values
	* @param array $merge_with Merge button options with the given array
	* @return array Array with button options or the $merge_with array containing the button options
	* @since 2.0
	*/
	/*
	public static function prepare_button( $opt, $merge_with = array() ) {

		$button = array();
		$button['anchor_text'] = isset( $opt['button_text'] ) ? $opt['button_text'] : '';
		$button['attr_title']  = $button['anchor_text'];
		$button['attr_href']   = isset( $opt['button_link'] ) ? $opt['button_link'] : '';
		$button['attr_target'] = isset( $opt['button_link_target'] ) ? $opt['button_link_target'] : '';
		$button['attr_id']     = isset( $opt['button_id'] ) ? $opt['button_id'] : '';
		$button['attr_class']  = isset( $opt['button_style'] ) ? ' '. $opt['button_style'] : '';
		$button['attr_class'] .= isset( $opt['button_size'] ) ? ' '. $opt['button_size'] : '';
		$button['attr_class'] .= isset( $opt['button_color'] ) ? ' '. $opt['button_color'] : '';
		$button['attr_class'] .= isset( $opt['button_text_align'] ) ? ' '. $opt['button_text_align'] : '';
		$button['attr_class'] .= isset( $opt['button_extraclass'] ) ? ' '. $opt['button_extraclass'] : '';
		$button['attr_class']  = trim( $button['attr_class'] );
		$button['wrapper_attr_class'] = isset( $opt['button_placement'] ) ? $opt['button_placement'] : '';
		if ( !empty( $merge_with ) ) {

			$button = array_merge( $merge_with, $button );
		}
		return $button;
	}
	*/

	/**
	* Returns escaped social link attributes values, ready to be used in a frontend template file
	*
	* @param array $option The option that holds the raw social link values
	* @param array $merge_with Merge social options with the given array
	* @return array Array with social link options or the $merge_with array containing the button options
	* @since 2.0
	*/
	/*
	public static function prepare_social( $items_opt, $styling_opt = array(), $merge_with = array() ) {

		$social = array();
		$social['anchor_text'] = isset( $items_opt['social_text'] ) ? $items_opt['social_text'] : '';
		$social['attr_title']  = $social['anchor_text'];
		$social['attr_href']   = isset( $items_opt['social_link'] ) ? $items_opt['social_link'] : '';
		$social['attr_target'] = isset( $items_opt['social_link_target'] ) ? $items_opt['social_link_target'] : '';
		$social['attr_id']     = isset( $items_opt['social_id'] ) ? $items_opt['social_id'] : '';
		$social['attr_class']  = isset( $items_opt['social_extraclass'] ) ? 'social' .' '. $items_opt['social_extraclass'] : 'social';
		$social['icon_class']  = isset( $items_opt['social_icon'] ) ? ' '. $items_opt['social_icon'] : '';
		// extra styling
		$social['attr_class'] .= isset( $styling_opt['social_style'] ) ? ' '. $styling_opt['social_style'] : '';
		$social['attr_class'] .= isset( $styling_opt['social_size'] ) ? ' '. $styling_opt['social_size'] : '';
		$social['attr_class'] .= isset( $styling_opt['social_color'] ) ? ' '. $styling_opt['social_color'] : '';
		$social['attr_class'] .= isset( $styling_opt['social_extraclass'] ) ? ' '. $styling_opt['social_extraclass'] : '';
		$social['attr_class']  = trim( $social['attr_class'] );
		if ( !empty( $merge_with ) ) {

			$social = array_merge( $merge_with, $social );
		}
		return $social;
	}
	*/

	/**
	* Returns escaped social link attributes values, ready to be used in a frontend template file
	*
	* @param array $option The option that holds the raw social link values
	* @param array $merge_with Merge social options with the given array
	* @return array Array with social link options or the $merge_with array containing the button options
	* @since 2.0
	*/
	/*
	public static function prepare_track( $track_opts, $tracklist_opts, $merge_with = array() ) {

		if ( empty( $track_opts['audio_track_name'] ) ) { return array(); }

		$opt      = self::arr_remove_key_prefix( $tracklist_opts['config'], 'tracklist_' );
		$val      = self::arr_remove_key_prefix( $track_opts, 'audio_track_' );
		$services = !empty( $tracklist_opts['audio_market_services'] ) ? $tracklist_opts['audio_market_services'] : array();

		//temp
		$opt['previews_title']            = esc_html__( 'Preview this track', 'plethora-framework' );
		$opt['previews_playing_title']    = esc_html__( 'Playing now: ', 'plethora-framework' );
		$opt['previews_play_icon_class']  = 'fa fa-play-circle';
		$opt['previews_pause_icon_class'] = 'fa fa-pause-circle';
		$opt['downloads_title']           = esc_html__( 'Download this track', 'plethora-framework' );
		$opt['downloads_icon_class']      = 'fa fa-download';
		$opt['videos_title']              = esc_html__( 'Watch the video clip', 'plethora-framework' );
		$opt['videos_icon_class']         = 'fa fa-film';
		$opt['carts_icon_class']          = 'fa fa-cart-plus';


		$track['artist']                     = !empty( $opt['artists'] ) && !empty( $val['artist'] ) ? wp_kses( $val['artist'], Plethora_Helper::allowed_html_for( 'button' ) )  : '';
		$track['album_prefix']               = !empty( $opt['albums'] ) && !empty( $val['album'] ) ? esc_html__( 'Album: ', 'plethora-framework' ) : '';
		$track['album']                      = !empty( $opt['albums'] ) && !empty( $val['album'] ) ? wp_kses( $val['album'], Plethora_Helper::allowed_html_for( 'button' ) ) : '';
		$track['cover_large']                = !empty( $tracklist_opts['cover_large'] ) ? esc_url( $tracklist_opts['cover_large'] ) : '';
		$track['cover_thumb']                = !empty( $tracklist_opts['cover_thumb'] ) ? esc_url( $tracklist_opts['cover_thumb'] ) : '';
		// $track['album']                   = !empty( $tracklist_opts['album'] ) ? wp_kses( $tracklist_opts['album'], Plethora_Helper::allowed_html_for( 'button' ) ) : '';
		$track['title']                      = wp_kses( $val['name'], Plethora_Helper::allowed_html_for( 'button' ) );
		$track['title_attr']                      = esc_attr( $val['name'] );
		$track['duration']                   = $opt['durations'] && !empty( $val['duration'] ) ? wp_kses( $val['duration'], Plethora_Helper::allowed_html_for( 'button' ) ) : '' ;
		$file_type                           =  !empty( $val['preview_url'] ) ? pathinfo( wp_basename( $val['preview_url'] ), PATHINFO_EXTENSION) : '' ;
		$track['preview_attr_src']           = $opt['previews'] && !empty( $val['preview_url'] ) && in_array( $file_type, array( 'mp3', 'wav', 'ogg' ) ) ? esc_url( $val['preview_url'] ) : '' ;
		$track['preview_not_available']		 = empty( $track['preview_attr_src'] ) ? true : false ;
		$track['preview_attr_type']          = $file_type === 'mp3' ? 'audio/mpeg' : 'audio/'. $file_type;
		$track['preview_attr_title']         = $opt['previews'] && !empty( $opt['previews_title'] ) ? esc_attr( $opt['previews_title'] ) : '' ;
		$track['preview_attr_playing_title'] = $opt['previews'] && !empty( $opt['previews_playing_title'] ) ? esc_attr( $opt['previews_playing_title'] ) : '' ;
		$track['previews_play_icon_class']   = $opt['previews'] && !empty( $opt['previews_play_icon_class'] ) ? esc_attr( $opt['previews_play_icon_class'] ) : '' ;
		$track['previews_pause_icon_class']  = $opt['previews'] && !empty( $opt['previews_pause_icon_class'] ) ? esc_attr( $opt['previews_pause_icon_class'] ) : '' ;
		$track['download_attr_href']         = $opt['downloads'] && !empty( $val['download_url'] ) ? esc_url( $val['download_url'] ) : '' ;
		$track['download_attr_title']        = $opt['downloads'] && !empty( $opt['downloads_title'] ) ? esc_attr( $opt['downloads_title'] ) : '' ;
		$track['download_icon_class']        = $opt['downloads'] && !empty( $opt['downloads_icon_class'] ) ? esc_attr( $opt['downloads_icon_class'] ) : '' ;
		$track['video_attr_href']            = $opt['embeds'] && !empty( $val['embedded_video'] ) ? esc_url( $val['embedded_video'] )  : '' ;
		$track['video_attr_title']           = $opt['embeds'] && !empty( $opt['videos_title'] ) ? esc_attr( $opt['videos_title'] ) : '' ;
		$track['video_attr_target']          = '_blank' ;
		$track['video_icon_class']           = $opt['embeds'] && !empty( $opt['videos_icon_class'] ) ? esc_attr( $opt['videos_icon_class'] ) : '' ;
		$track['service_links']              = array();
		if ( $opt['services'] && !empty( $services ) ) {

			foreach ( $services as $service ) {

				$key = $service['service']['social_id'];
				if ( !empty( $val['services_'. $key ] ) ) {

					$track['service_links'][] = array(
						'attr_href'   => esc_url( $val['services_'. $key ] ),
						'attr_title'  => esc_attr( $service['service']['social_text'] ),
						'attr_target' => esc_attr( $service['service']['social_link_target'] ),
						'icon_class'  => esc_attr( $service['service']['social_icon'] ),
					);
				}
			}
		}

		// WooCommerce
		$track['cart_post_id']             = $val['cart_post_id'];
		$track['cart_attr_href']             = '';
		$track['cart_attr_title']            = '';
		$track['cart_attr_data_quantity']    = '';
		$track['cart_attr_data_product_id']  = '';
		$track['cart_attr_data_product_sku'] = '';
		$track['cart_attr_class']            = '';
		$track['cart_attr_price']            = '';
		$track['cart_attr_price_currency']   = '';
		$track['cart_icon_class']            = '';
		if ( $opt['carts'] && $val['cart_post_id'] && function_exists( 'wc_get_product' ) ) {

			$product = wc_get_product( $val['cart_post_id'] );
			if ( is_object( $product ) && $product->get_status() === 'publish'  ) {

				$track['cart_attr_href']             = esc_url( $product->add_to_cart_url() );
				$track['cart_attr_data_quantity']    = '1';
				$track['cart_attr_data_product_id']  = $val['cart_post_id'];
				$track['cart_attr_data_product_sku'] = esc_attr( $product->get_sku() );;
				$track['cart_attr_class']            = 'add_to_cart_button ajax_add_to_cart';
				$track['cart_attr_price']            = esc_attr( $product->get_price() );
				$track['cart_attr_price_currency']   = esc_attr( get_woocommerce_currency_symbol() );
				$track['cart_attr_title']            = esc_attr( $product->add_to_cart_text() .' / '. esc_html__( 'Price: ', 'plethora-framework') . strip_tags( wc_price( $product->get_price() ) ) );
				$track['cart_icon_class']            = !empty( $opt['carts_icon_class'] ) ? esc_attr( $opt['carts_icon_class'] ) : '' ;
			}
		}

		if ( !empty( $merge_with ) ) {

			$track = array_merge( $merge_with, $track );
		}
		return $track;
	}
	*/

	/**
	 * Return categories in title->value array. Based on WP get_categories. Used mostly on shortcode features.
	 * Check http://codex.wordpress.org/Function_Reference/get_categories for further documentation
	 *
	 * @param $user_args, $taxonomy, $fieldtitle, $fieldvalue
	 * @return array
	 * @since 1.0
	 *
	 */
	/*
	static function categories( $user_args = array(), $fieldtitle = 'name', $fieldvalue = 'cat_ID'  ) {

		// Default arguments
		$default_args = array(
			'type'                     => '',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false

		);

		// Merge default and user given arguments
		$args = array_merge($default_args, $user_args);

		// Get the categories
		$categories = get_categories( $args );

		// Return values in array, according to $fieldtitle and $fieldvalue variables
		$return = Array();

		foreach ( $categories as $category ) {

			$return[$category->$fieldtitle] = $category->$fieldvalue;
		}

		ksort($return);
		return $return;
	}
	*/

	/*
	public static function get_reduxoption_image_src( $value, $size = 'full' ) {

		$image_id = !empty( $value['id'] ) ? $value['id'] : 0;
		$image_src = $value['url'];
		if ( $image_id ) {

			$image_src_by_id = wp_get_attachment_image_src( $image_id, $size );
			$image_src = !empty( $image_src_by_id[0] ) ? $image_src_by_id[0] : $value['url'];
		}
		return $image_src;
	}
	*/

	/*
	public static function get_colorsets(){
		$colorsets_obj = Plethora_Theme::get_feature_instance( 'module', 'colorsets' );
		$color_sets_json = json_encode( $colorsets_obj->less_variables() );
	    echo '"<script>var themeConfigAdmin=themeConfigAdmin||{}; themeConfigAdmin.color_sets=' . $color_sets_json.';console.log(themeConfigAdmin);</script>"';
	}
	*/

	/**
	 * Export Colorset values as a JSON object. Always gets hooked before the closing body tag.
	 * @param $[options] Either empty for front end use or 'admin' for use in the admin area.
	 */
	/*
	public static function export_colorsets( $options="" ){

		if ( $options=="admin" && !defined('THEME_CONFIG_ADMIN_COLORSETS') ){

			define( 'THEME_CONFIG_ADMIN_COLORSETS', true );
			add_action('admin_footer', array( 'Plethora_Helper', 'get_colorsets' ) );

		} elseif ( !defined('THEME_CONFIG_COLORSETS') ) {

			define( 'THEME_CONFIG_COLORSETS', true );
			add_action('wp_footer', array( 'Plethora_Helper', 'get_colorsets' ) );

		}
	}
	*/

	/**
	 * Export Colorset values as a JSON object. Always gets hooked before the closing body tag.
	 * @param $[options] Either empty for front end use or 'admin' for use in the admin area.
	 */
	/*
	public static function is_ajax() {

		if ( defined('DOING_AJAX') && DOING_AJAX ) { return true; }

		return false;
	}
	*/

}