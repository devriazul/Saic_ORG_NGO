<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2013-2018

Timeline shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_EmbedLite') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_EmbedLite extends Plethora_Shortcode { 

		public static $feature_title         = "Lightweight Video Embed Shortcode";       
		public static $feature_description   = "";                              
		public static $theme_option_control  = true;                            
		public static $theme_option_default  = true;                            
		public static $theme_option_requires = array();    
		public static $dynamic_construct     = true;       
		public static $dynamic_method        = false;      
		public $wp_slug                      = 'embedlite';
		public static $assets                = array();
		public static $instance;

		/** 
		* Returns class instance
		* @return object
		*/
		public static function get_instance() {
		
			if ( empty( self::$instance ) ) {
			
				$class = get_called_class();
				self::$instance = new $class;
			
			}
		
			return self::$instance;
		}
		
		private function __clone(){}    // No clones
		private function __wakeup(){}   // No unserializing
		public function __construct(){ // No duplicate instances
		
			add_action( 'init', array( $this, 'init' ) );

		}

		/** 
		* Map shortcode
		* 
		* Hooked @ 'init'
		* @return void
		*/
		public function init() {

			// Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
			$map = array( 
				'base'              => SHORTCODES_PREFIX . $this->wp_slug,
				'name'              => esc_html__('Video Embed Lite', 'plethora-framework'),
				'description'       => esc_html__('Display a lightweight embedded video', 'plethora-framework'),
				'class'             => 'embedlite_shortcode',
				'weight'            => 1,
				'admin_enqueue_js'  => array(), 
				'admin_enqueue_css' => array(),
				'icon'              => $this->vc_icon(), 
				// 'custom_markup'  => $this->vc_custom_markup( 'Profiles Grid' ), 
				'params'            => $this->params(), 
			);
			// Add the shortcode
			$this->add( $map );

			// INSERT JAVASCRIPT
			if ( function_exists('vc_add_params') ) {
				
				// Enqueue JS
				add_action( 'admin_enqueue_scripts', array( $this, 'load_script' ) );

			}

		}

		public function load_script() { 

	        wp_register_script( 'embedlite', PLE_FLIB_FEATURES_URI.'/shortcode/embedlite/js/embedlite.js', false, '1.0.0' );
		    wp_enqueue_script( 'embedlite' );

		}
		
		/** 
		* Returns shortcode settings (compatible with Visual composer)
		*
		* @return array
		* @since 1.0
		*
		*/
		public function params_index() {

			# SCRIPT LOADER
	        $params_index['ple_script_loader'] = array(
	                      "param_name"  => "post_type",
	                      "type"        => "ple_script_loader",
    					  "value"       => "ple_embedlite_init()",
	                      "args"        => array( 'init' => 'ple_embedlite_init()' ),
	                      'admin_label' => false,
	                  );
			$params_index['video_url'] = array(
				"param_name" => "video_url",                                  
				"type"       => "textfield",                                        
				"heading"    => esc_html__( 'Video URL', 'plethora-framework' ),
				"holder"     => "h2",
				"class"		 => "embedlite_video_url"
			);
			$params_index['thumb_res'] = array(
				"param_name"  => "thumb_res",                                  
				"type"        => "dropdown",                                        
				"heading"     => esc_html__("Thumbnail resolution", 'plethora-framework'),      
				"value"       => array(
					"High"     => "high",
					"Standard" => "standard",
					"Medium"   => "medium"
				)
			);
			$params_index['embedlite_video_provider'] = array(
				"param_name"  => "embedlite_video_provider",        
				"type"        => "dropdown",                                        
				"heading"     => esc_html__("Video Provider", 'plethora-framework'),      
				"value"       => array(
					""			  => "unknown",
					"YouTube"     => "youtube",
					"Vimeo" 	  => "vimeo",
					"DailyMotion" => "dailymotion"
				)
			);
			$params_index['embedlite_video_id'] = array(
				"param_name"  => "embedlite_video_id",                                  
				"type"        => "textfield",                                        
				"heading"     => esc_html__("Video ID", 'plethora-framework'),      
				"value"       => array(
					"High"     => "high",
					"Standard" => "standard",
					"Medium"   => "medium"
				)
			);
			$params_index['el_class'] = array(
				"param_name"  => "el_class",                                  
				"type"        => "textfield",                                        
				"heading"     => esc_html__("Extra CSS Class", 'plethora-framework'),      
				"admin_label" => false,                                              
			);
			$params_index['id'] = array( 
				'param_name'  => 'id',
				'type'        => 'textfield',
				'heading'     => esc_html__( 'ID', 'plethora-framework' ),
				'description' => esc_html__( 'Create a unique or global ID for this element. Useful if you want to customize this via Javascript', 'plethora-framework' ),
			);
			// DESIGN OPTIONS TAB STARTS >>>>
			$params_index['css'] = array(
				'param_name' => 'css',
				'type'       => 'css_editor',
				'group'      => esc_html__('Design Options', 'plethora-framework'),                                              
				'heading'    => esc_html__('Design Options', 'plethora-framework'),
			);
			// <<<< DESIGN OPTIONS TAB ENDS

			return $params_index;
		}

		public function getYouTubeVideo($options){

			$video_url                = $options['video_url'];
			$thumb_res                = $options['thumb_res'];
			$embedlite_video_provider = $options['video_type'];
			$embedlite_video_id       = $options['video_id'];
			$thumbnail 				  = "";
			$video_url 				  = "";

			switch ($thumb_res) {
				case 'standard':
					$thumbnail = "sddefault.jpg";
					break;
				case 'medium':
					$thumbnail = "mqdefault.jpg";
					break;
				case 'high':
					$thumbnail = "hqdefault.jpg";
					break;
				default:
					$thumbnail = "hqdefault.jpg";
					break;
			}

			$thumbnail = "https://i.ytimg.com/vi/" . $embedlite_video_id . "/" . $thumbnail;
			$video_url = "https://www.youtube.com/embed/" . $embedlite_video_id . "?autoplay=1&autohide=1&border=0&wmode=opaque&enablejsapi=1";

			return array( 
				'video_url'  => $video_url, 
				'thumbnail'  => $thumbnail,
				'video_type' => $embedlite_video_provider,
				'video_id'   => $embedlite_video_id
				// 1.jpg         // Key Frame Points
			);

		}

		public function getDailyMotionVideo($options){

			$video_url                = $options['video_url'];
			$thumb_res                = $options['thumb_res'];
			$embedlite_video_provider = $options['video_type'];
			$embedlite_video_id       = $options['video_id'];
			$thumbnail 				  = "";
			$video_url 				  = "";

			$thumbnail = "https://www.dailymotion.com/thumbnail/video/" . $embedlite_video_id;
			$video_url = "//www.dailymotion.com/embed/video/" . $embedlite_video_id . "?autoplay=1";

			return array( 
				'video_url'  => $video_url, 
				'thumbnail'  => $thumbnail,
				'video_type' => $embedlite_video_provider,
				'video_id'   => $embedlite_video_id
			);

		}

		public function getVimeoVideo($options){

			$video_url                = $options['video_url'];
			$thumb_res                = $options['thumb_res'];
			$embedlite_video_provider = $options['video_type'];
			$embedlite_video_id       = $options['video_id'];
			$thumbnail 				  = "";
			$video_url 				  = "";

			// VIMEO THUMBNAIL
			$transient_name = "embedlite_vimeo_thumbnail_" . $embedlite_video_id;
			if ( false === ( $hash = get_transient( $transient_name ) ) ) {
				$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/" . $embedlite_video_id . ".php"));
			     set_transient( $transient_name, $hash, MONTH_IN_SECONDS );	// 2 * WEEK_IN_SECONDS
			}

			switch ($thumb_res) {
				case 'standard':
					$thumbnail = $hash[0]['thumbnail_small'];
					break;
				case 'medium':
					$thumbnail = $hash[0]['thumbnail_medium'];
					break;
				case 'high':
					$thumbnail = $hash[0]['thumbnail_large'];
					break;
				default:
					$thumbnail = $hash[0]['thumbnail_large'];
					break;
			}

			$video_url = "https://player.vimeo.com/video/" . $embedlite_video_id . "?autoplay=1&loop=0&autopause=0";

			return array( 
				'video_url'  => $video_url, 
				'thumbnail'  => $thumbnail,
				'video_type' => $embedlite_video_provider,
				'video_id'   => $embedlite_video_id
			);

		}

		/** 
		* Returns shortcode content
		*
		* @return array
		* @since 1.0
		*
		*/
		public function content( $atts, $content ) {

			// Extract user input
			$atts = shortcode_atts( $this->get_default_param_values(), $atts );
			extract( $atts );

			$videoParams = array(
				'video_url'  => $video_url,
				'thumb_res'  => $thumb_res,
				'video_type' => $embedlite_video_provider,
				'video_id'   => $embedlite_video_id
			);
			$template_opts = array();

			switch ($embedlite_video_provider) {
				case 'youtube':
					$template_opts = $this->getYouTubeVideo($videoParams);
					break;
				case 'vimeo':
					$template_opts = $this->getVimeoVideo($videoParams);
					break;
				case 'dailymotion':
					$template_opts = $this->getDailyMotionVideo($videoParams);
					break;
				default:
					break;
			}

			$template_opts['iframe_attributes'] = 'frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen';

		  	if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' )  ) {

				$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), $this->wp_slug, $atts );
				$shortcode_id = trim( esc_attr( $id ) ); 
				$shortcode_id = ( strlen($shortcode_id) )? " id='$shortcode_id'" : "";
				$template_opts['shortcode_id'] = $shortcode_id;
				$template_opts['extra_class']  = esc_attr( $atts['el_class'] ) .' '. esc_attr( $css_class );

		  	} 

	        return Plethora_WP::renderMustache( array( "data" => $template_opts, "file" => __FILE__ ) );

	   	}

	}
endif;