<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Features Teaser shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Teaserbox_Ext') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_Teaserbox_Ext extends Plethora_Shortcode_Teaserbox {

		/**
		* Configure parameters displayed
		* Will be displayed all items from params_index() with identical 'id'
		* This method should be used for extension class overrides
		*
		* @return array
		*/
		public function params_config() {

			$params_config = array(
				array(
					'id'         => 'title',
					'default'    => '',
					'field_size' => '',
					),
				array(
					'id'         => 'subtitle',
					'default'    => '',
					'field_size' => '',
					),
				array(
					'id'         => 'content',
					'default'    => '',
					'field_size' => '',
					),
				array(
					'id'         => 'teaser_link',
					'default'    => '#',
					'field_size' => '6',
					),
				array(
					'id'         => 'link_title',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'boxed_styling',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'media_type',
					'default'    => 'image',
					'field_size' => '6',
					),
				array(
					'id'         => 'video_url',
					'default'    => '',
					'field_size' => '',
					),
				array(
					'id'         => 'icon',
					'default'    => 'fa fa-th',
					'field_size' => '6',
					),
				array(
					'id'         => 'image',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'image_hover_effect',
					'default'    => 'disabled',
					'field_size' => '6',
					),
				array(
					'id'         => 'media_colorset',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'media_ratio',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'text_colorset',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'text_boxed_styling',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'text_align',
					'default'    => 'text-center',
					'field_size' => '6',
					),
				array(
					'id'         => 'button_display',
					'default'    => '1',
					'field_size' => '6',
					),
				array(
					'id'         => 'button_text',
					'default'    => esc_html__( 'More', 'healthflex' ),
					'field_size' => '6',
					),
				array(
					'id'         => 'button_style',
					'default'    => 'btn-default',
					'field_size' => '6',
					),
				array(
					'id'         => 'same_height',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'el_class',
					'default'    => '',
					'field_size' => '6',
					),
				array(
					'id'         => 'css',
					'default'    => '',
					'field_size' => '',
					)
			);
			return $params_config;
		}

		 /**
		 * Returns shortcode content OR content template
		 *
		 * @return array
		 * @since 1.0
		 *
		 */
		 public function content( $atts, $content = null ) {

			// EXTRACT USER INPUT
			extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

			// Prepare final values that will be used in template
			$image       = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
			$image       = isset($image[0]) ? $image[0] : '';
			$teaser_link = !empty($teaser_link) ? self::vc_build_link($teaser_link) : array();
			$video_frame = !empty( $video_url ) ? wp_oembed_get( $video_url ) : '';
			//$button_link = !empty($button_link) ? self::vc_build_link($button_link) : '#';
			$button_display = isset( $atts['button'] ) ? $atts['button'] : $button_display;
			// Place all values in 'shortcode_atts' variable
			$shortcode_atts = array (
				'content'            => $content,
				'title'              => $title,
				'subtitle'           => $subtitle,
				'link_title'         => !empty( $link_title ) ? 1 : '',
				'icon'               => esc_attr( $icon ),
				'image'              => esc_url( $image ),
				'image_hover'        => ( $image_hover_effect == "enabled" )? "image_hover" : "",
				'media_colorset'     => $media_colorset,
				'media_ratio'        => $media_ratio,
				'video_frame'        => $video_frame,
				'text_colorset'      => $text_colorset,
				'text_align'         => $text_align,
				'button_text'        => $button_text,
				'button_style'       => $button_style,
				// 'button_size'     => $button_size,
				'boxed_styling'      => $boxed_styling,
				'same_height'        => $same_height,
				'text_boxed_styling' => $text_boxed_styling,
				'figure_classes'     => 'figure ' . $media_colorset . ' ' . ( ( preg_match( "/boxed/", $boxed_styling ) && $media_ratio == "boxed" ) ? "" : $media_ratio ),
				'el_class'           => esc_attr( $el_class ),
				'css'                => function_exists('vc_shortcode_custom_css_class') ? esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ) : '',
														 );

			if ( $media_type === 'image' && $image !== "" ) {

				$shortcode_atts["media_type_image"] = TRUE;

			} elseif ( $media_type === 'icon' && $icon !== "" ) {

				$shortcode_atts["media_type_icon"] = TRUE;
			}

			if ( $media_ratio !== '' ) {

				$shortcode_atts["aplied_media_ratio"] = TRUE;

			} else {

				$shortcode_atts["no_media_ratio"] = TRUE;
			}

			if ( !empty( $teaser_link['url'] ) ) {

				$shortcode_atts["teaser_link_url"]    = esc_url( $teaser_link['url'] );
				$shortcode_atts["teaser_link_title"]  = esc_attr( trim( $teaser_link['title']) );
				$shortcode_atts["teaser_link_target"] = esc_attr( trim( $teaser_link['target']) );

			}

			if ( $button_display == 1 ){

				$shortcode_atts["button"]        = TRUE;
				//$shortcode_atts["btn_url"]       = isset($button_link['url']) ? esc_url($button_link['url']) : '#';
				//$shortcode_atts["btn_urltitle"]  = isset($button_link['title']) ? ' title="'. esc_attr( $button_link['title'] ) .'"' : '';
				//$shortcode_atts["btn_urltarget"] = isset($button_link['target']) ?' target="'. esc_attr( $button_link['target'] ) .'"' : '';

			}

			return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

		}
	}

 endif;