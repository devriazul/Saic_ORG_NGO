<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

WooCommerce Plugin Support Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Woocommerce') && !class_exists('Plethora_Module_Woocommerce_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-woocommerce.php
   */
  class Plethora_Module_Woocommerce_Ext extends Plethora_Module_Woocommerce {


	public static $feature_title         = "WooCommerce Support Module";							// Feature display title  (string)
	public static $feature_description   = "Adds support for WooCommerce plugin to your theme";	// Feature display description (string)
	public static $theme_option_control  = true;													// Will this feature be controlled in theme options panel ( boolean )
	public static $theme_option_default  = true;											// Default activation option status ( boolean )
	public static $theme_option_requires = array();									// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;												// Dynamic class construction ? ( boolean )
	public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

	public function __construct() {

		if ( class_exists('woocommerce') ) {
		// WooCommerce support
	        add_action( 'after_setup_theme', array( $this, 'support' ) );										// Primary WC support declaration
			add_action( 'plethora_module_wpless_core_parts', array( $this, 'enqueue_less' ), 20);  // Style enqueing - keep priority to 20 to make sure that it will be loaded after Woo defaults
	        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20);  // Style enqueing - keep priority to 20 to make sure that it will be loaded after Woo defaults
	        add_filter( 'plethora_supported_post_types', array( $this, 'add_product_to_supported_post_types'), 10, 2 ); // declare frontend support manually ( this is mandatory, since there is not Plethora_Posttype_Product class )
	        add_filter( 'plethora_this_page_id', array( $this, 'get_this_page_id') ); // add static page id support
	        add_filter( 'plethora_static_archive_page', array( $this, 'add_static_archive_page'), 10, 3 ); // add static page id support

	        self::remove_hooks();																							// Remove hooks that will be replaced later

		// Options & Metabox
			add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 10);			// Theme Options // Archive
			add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 120);			// Theme Options // Single Post
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));								// Metabox // Single Post
		// Wrappers
			add_action( 'plethora_wrapper_column_class', array( $this, 'wrapper_column_class'), 10 );			// Main Wrapper Start
		// Catalog controls ( before loop )
	        add_action( 'woocommerce_before_main_content', array( $this, 'catalog_breadcrumbs' ), 5);			// Catalog: Breadcrums
			add_filter( 'woocommerce_show_page_title', array( $this, 'catalog_title_display' ) );						// Catalog: Title display
			add_filter( 'woocommerce_page_title', array( $this, 'catalog_title' ) );						// Catalog: Title display
			add_action( 'woocommerce_archive_description', array( $this, 'catalog_categorydescription' ), 1);	// Catalog: Category description display
			add_action( 'woocommerce_before_shop_loop', array( $this, 'catalog_resultscount'), 1);				// Catalog: Results count display
			add_action( 'woocommerce_before_shop_loop', 	array( 'Plethora_Module_Woocommerce', 'catalog_orderby'), 1);				// Catalog: order by field
		// Catalog controls ( on loop )
	        add_filter( 'loop_shop_per_page', array( $this, 'catalog_perpage' ), 20);							// Loop: Products per page
	        add_filter( 'loop_shop_columns', array( $this, 'catalog_columns' ));								// Loop: Columns
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'catalog_rating' ), 1);		// Loop: Rating display
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'catalog_price' ), 1);			// Loop: Price display
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'catalog_addtocart' ), 1);			// Loop: Add-to-cart display
			add_action( 'woocommerce_before_shop_loop_item_title',array( $this, 'catalog_salesflash' ), 1);	// Loop: Sales flash icon display
		// Single product controls
	        add_action( 'woocommerce_before_main_content', array( $this, 'single_breadcrumbs' ), 5);			// Single: Breadcrums
	        add_action( 'woocommerce_before_single_product_summary',array( $this, 'single_salesflash' ), 1);	// Single: Sales flash icon display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_title' ) , 1);				// Single: Title display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_rating' ), 1 );			// Single: Rating display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_price' ), 1 );				// Single: Price display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_addtocart' ), 1 );			// Single: add-to-cart display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_meta' ), 1 );				// Single: Meta display
			add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_description' ), 98 );			// Single: Description tab display
			add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_reviews' ), 98 );				// Single: Reviews tab display
			add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_attributes' ), 98 );				// Single: Additional info tab display
			add_action( 'get_header', array( $this, 'single_related' ), 20 );
	        add_filter( 'woocommerce_output_related_products_args', array( $this, 'single_related_config' ));	// Single: Related products status
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );		// Single: Upsell products ( remove default )
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_upsell'), 15); 		// Single: Upsell products display ( "You May Also Like...")

			# CONFLICTS!!!
				add_action( 'admin_enqueue_scripts', array( $this, 'deregister_redux_select2' ), 90 );
		}
	}

	// ok
	public function support() {

		$support             = array( 'woocommerce' );
		$support['zoom']     = Plethora_Theme::option( METAOPTION_PREFIX .'product-gallery-zoom', 1 ) == 1 ? 'wc-product-gallery-zoom' : '';
		$support['lightbox'] = Plethora_Theme::option( METAOPTION_PREFIX .'product-gallery-lightbox', 1 ) == 1 ? 'wc-product-gallery-lightbox' : '';
		$support['slider']   = Plethora_Theme::option( METAOPTION_PREFIX .'product-gallery-slider', 1 ) == 1 ? 'wc-product-gallery-slider' : '';
		$support             = array_filter( $support );
		foreach ( $support as $supported ) {

			add_theme_support( $supported );
		}
	}

	public function add_product_to_supported_post_types( $supported, $args ) {

      // Add this only when the call asks for plethora_only post types
      if ( $args['plethora_only'] ) {

        $supported['product'] = $args['output'] === 'objects' ? get_post_type_object( 'product' ) : 'product' ;
      }

      return $supported;
	}

	public function remove_hooks() {

		// Remove global wrappers and sidebar
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
		// Disable stylesheet enqueues
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	}

	/**
	* Will add static page id for shop page
	* Hooked at 'plethora_this_page_id'
	*/
	public static function get_this_page_id( $page_id ) {

		if ( self::is_shop_catalog() ) {

		    $page_id = get_option( 'woocommerce_shop_page_id' );
		}

		return $page_id;
	}

		/**
		 * Enqueue Woocommerce stylesheet
		 * Hooked on 'wp_enqueue_scripts' action
		 */
		public function enqueue_less( $less_parts ) {

			$less_parts['woocommerce'] = array(
				'index_header'   => esc_html__( 'WOOCOMMERCE STYLES', 'plethora-framework' ),
				'comment_header' => esc_html__( 'WOOCOMMERCE STYLES', 'plethora-framework' ),
				'comment_header' => esc_html__( 'WOOCOMMERCE STYLES', 'plethora-framework' ),
				'comment_text'   => 'WooCommerce related styling ( active only when WooCommerce is activated )',
				'less_file'      => 'assets/less/includes/woocommerce.less',
			);
			return $less_parts;
		}


	/**
	* Add the static shop page id to supported static archives
	*/
	public static function add_static_archive_page( $return, $post_type, $args ) {

		if ( $post_type === 'product' ) {

		    $page_id = get_option( 'woocommerce_shop_page_id' );
			if ( $args['output'] === 'object' ) {

			  $return = get_post( $page_id );

			} else {

			  $return = $page_id;
			}
		}

		return $return;
	}

	public function enqueue() {

		wp_register_style( 'plethora-woocommerce', PLE_THEME_ASSETS_URI . '/css/woocommerce.css');
        wp_enqueue_style( 'plethora-woocommerce' );
	}

	public static function wrapper_column_class( $classes ) {

		if ( is_product() || self::is_shop_catalog() ) {

			$classes[] = 'plethora-woo';
		}

		if ( self::is_shop_catalog() ) {

			$classes[] = 'plethora-woo-shop-grid-'. Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-columns', 4);

		} elseif ( is_product() ) {

			$classes[] = 'plethora-woo-related-grid-'. Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
		}

		return $classes;
	}

	public function archive_themeoptions( $sections ) {

		$page_for_shop	= get_option( 'woocommerce_shop_page_id', 0 );
		$desc_1 = esc_html__('These options affect ONLY shop catalog display.', 'plethora-framework');
		$desc_2 = esc_html__('These options affect ONLY shop catalog display...however it seems that you', 'plethora-framework');
		$desc_2 .= ' <span style="color:red">';
		$desc_2 .= esc_html__('have not set a shop page yet!', 'plethora-framework');
		$desc_2 .= '</span>';
		$desc_2 .= esc_html__('You can go for it under \'WooCommerce > Settings > Products > Display\'.', 'plethora-framework');
		$desc = $page_for_shop === 0 || empty($page_for_shop) ? $desc_2 :  $desc_1 ;
		$desc .= '<br>'. esc_html__('If you are using a speed optimization plugin, don\'t forget to <strong>clear cache</strong> after options update', 'plethora-framework');

	    $sections[] = array(
			'title'      => esc_html__('Shop', 'plethora-framework'),
			'heading'      => esc_html__('SHOP OPTIONS', 'plethora-framework'),
			'desc'      => $desc,
			'subsection' => true,
			'fields'     => array(
		            array(
		                'id'        =>  METAOPTION_PREFIX .'archiveproduct-layout',
		                'title'     => esc_html__( 'Catalog Layout', 'plethora-framework' ),
		                'default'   => 'right_sidebar',
		                'type'      => 'image_select',
						'options' => array(
								'full'         => ReduxFramework::$_url . 'assets/img/1c.png',
								'right_sidebar'         => ReduxFramework::$_url . 'assets/img/2cr.png',
								'left_sidebar'         => ReduxFramework::$_url . 'assets/img/2cl.png',
			                )
		            ),
					array(
						'id'=> METAOPTION_PREFIX .'archiveproduct-sidebar',
						'required'=> array( METAOPTION_PREFIX .'archiveproduct-layout','!=', 'full' ),
						'type' => 'select',
						'data' => 'sidebars',
						'multi' => false,
						'title' => esc_html__('Catalog Sidebar', 'plethora-framework'),
						'default'  => 'sidebar-shop',
					),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-perpage',
						'type'        => 'slider',
						'title'       => esc_html__('Products Displayed Per Page', 'plethora-framework'),
					    "default" => 12,
					    "min" => 4,
					    "step" => 4,
					    "max" => 240,
					    'display_value' => 'text'
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-title',
						'type'    => 'switch',
						'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
						'desc'    => esc_html__('Will display title on content view', 'plethora-framework'),
						'default' => 0,
						),
					array(
						'id'       => METAOPTION_PREFIX .'archiveproduct-title-text',
						'type'     => 'text',
						'title'    => esc_html__('Default Title', 'plethora-framework'),
						'default'  => esc_html__('Shop Title', 'plethora-framework'),
						'translate' => true,
						),
					array(
						'id'       => METAOPTION_PREFIX .'archiveproduct-subtitle-text',
						'type'     => 'text',
						'title'    => esc_html__('Default Subtitle', 'plethora-framework'),
						'desc'    => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'),
						'default'  => esc_html__('Shop subtitle here', 'plethora-framework'),
						'translate' => true,
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-columns',
						'type'        => 'slider',
						'title'       => esc_html__('Products Grid Columns', 'plethora-framework'),
					    "default" => 3,
					    "min" => 2,
					    "step" => 1,
					    "max" => 4,
					    'display_value' => 'text'
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-categorydescription',
						'type'    => 'button_set',
						'title'   => esc_html__('Category Description', 'plethora-framework'),
						'desc'   => esc_html__('By default, category description ( if exists ) is displayed right after shop title.', 'plethora-framework'),
						"default" => 'hide',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-breadcrumbs',
						'type'    => 'button_set',
						'title'   => esc_html__('Breadcrumbs ( Catalog View )', 'plethora-framework'),
						"default" => 'hide',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-resultscount',
						'type'    => 'button_set',
						'title'   => esc_html__('Results Count Info', 'plethora-framework'),
						"default" => 'hide',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-orderby',
						'type'    => 'button_set',
						'title'   => esc_html__('Order Dropdown Field', 'plethora-framework'),
						"default" => 'hide',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-rating',
						'type'    => 'button_set',
						'title'   => esc_html__('Ratings ( Catalog View )', 'plethora-framework'),
						"default" => 'hide',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-price',
						'type'    => 'button_set',
						'title'   => esc_html__('Prices ( Catalog View )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-addtocart',
						'type'    => 'button_set',
						'title'   => esc_html__('"Add To Cart" Button ( Catalog View )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'archiveproduct-salesflash',
						'type'    => 'button_set',
						'title'   => esc_html__('"Sale!" Icon ( Catalog View )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
	        )
	    );

		return $sections;
	}

	public function single_themeoptions( $sections ) {

		$page_for_shop	= get_option( 'woocommerce_shop_page_id', 0 );
		$desc_1 = __('It seems that you <span style="color:red">have not set a shop page yet!</span>. You can go for it under <strong>WooCommerce > Settings > Products > Display</strong>.<br>', 'plethora-framework');
		$desc = $page_for_shop === 0 || empty($page_for_shop) ? $desc_1 :  '' ;
        $desc .=  esc_html__('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework');
    	$sections[] = array(
			'title'      => esc_html__('Single Product', 'plethora-framework'),
            'heading' => esc_html__('SINGLE PRODUCT POSTS OPTIONS', 'plethora-framework'),
			'subsection' =>  true,
			'desc' =>  $desc,
			'fields'     => array(

	            array(
	                'id'        =>  METAOPTION_PREFIX .'product-layout',
	                'title'     => esc_html__( 'Product Post Layout', 'plethora-framework' ),
	                'default'   => 'right_sidebar',
	                'type'      => 'image_select',
	                'customizer'=> array(),
					'options' => array(
							'full'          => ReduxFramework::$_url . 'assets/img/1c.png',
							'right_sidebar' => ReduxFramework::$_url . 'assets/img/2cr.png',
							'left_sidebar'  => ReduxFramework::$_url . 'assets/img/2cl.png',
		                )
	            ),
				array(
					'id'=> METAOPTION_PREFIX .'product-sidebar',
					'required'=> array( METAOPTION_PREFIX .'product-layout','!=', 'full' ),
					'type' => 'select',
					'data' => 'sidebars',
					'multi' => false,
					'title' => esc_html__('Product Post Sidebar', 'plethora-framework'),
					'default'  => 'sidebar-shop',
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-wootitle',
					'type'    => 'switch',
					'title'   => esc_html__('Display WooCommerce Title', 'plethora-framework'),
					'desc'   => esc_html__('Display the classic WooCommerce product title next to product image', 'plethora-framework'),
					'default'  => 1,
					'options' => array(
									1 => esc_html__('Display', 'plethora-framework'),
									0 => esc_html__('Hide', 'plethora-framework'),
								),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-breadcrumbs',
					'type'    => 'button_set',
					'title'   => esc_html__('Breadcrumbs ( Product Page )', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-gallery-zoom',
					'type'    => 'button_set',
					'title'   => esc_html__('Gallery / Zoom', 'plethora-framework'),
					"default" => 1,
					'options' => array(
							1 => esc_html__('Enable', 'plethora-framework'),
							0 => esc_html__('Disable', 'plethora-framework'),
						),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-gallery-lightbox',
					'type'    => 'button_set',
					'title'   => esc_html__('Gallery / Lightbox', 'plethora-framework'),
					"default" => 1,
					'options' => array(
							1 => esc_html__('Enable', 'plethora-framework'),
							0 => esc_html__('Disable', 'plethora-framework'),
						),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-gallery-slider',
					'type'    => 'button_set',
					'title'   => esc_html__('Gallery / Slider', 'plethora-framework'),
					"default" => 1,
					'options' => array(
							1 => esc_html__('Enable', 'plethora-framework'),
							0 => esc_html__('Disable', 'plethora-framework'),
						),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-rating',
					'type'    => 'button_set',
					'title'   => esc_html__('Ratings ( Product Page )', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-price',
					'type'    => 'button_set',
					'title'   => esc_html__('Price  ( Product Page )', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-addtocart',
					'type'    => 'button_set',
					'title'   => esc_html__('"Add To Cart" Button ( Product Page )', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-meta',
					'type'    => 'button_set',
					'title'   => esc_html__('Product Categories', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-sale',
					'type'    => 'button_set',
					'title'   => esc_html__('"Sale" Icon ( Product Page )', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-tab-description',
					'type'    => 'button_set',
					'title'   => esc_html__('Description Tab', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-tab-reviews',
					'type'    => 'button_set',
					'title'   => esc_html__('Reviews Tab', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-tab-attributes',
					'type'    => 'button_set',
					'title'   => esc_html__('Additional Information Tab', 'plethora-framework'),
					'descr'   => esc_html__('Remember that this tab is NOT displayed by defaul if product has no attributes', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-related',
					'type'    => 'button_set',
					'title'   => esc_html__('Related Products', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-upsell',
					'type'    => 'button_set',
					'title'   => esc_html__('Upsell Products', 'plethora-framework'),
					"default" => 'display',
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),

				array(
					'id'            => METAOPTION_PREFIX .'product-related-number',
					'type'          => 'slider',
					'title'         => esc_html__('Related/Upsell Products Max Results', 'plethora-framework'),
					"default"       => 3,
					"min"           => 2,
					"step"          => 1,
					"max"           => 36,
					'display_value' => 'text'
					),

				array(
					'id'      => METAOPTION_PREFIX .'product-related-columns',
					'type'        => 'slider',
					'title'       => esc_html__('Related/Upsell Products Columns', 'plethora-framework'),
				    "default" => 3,
				    "min" => 2,
				    "step" => 1,
				    "max" => 4,
				    'display_value' => 'text'
					),
			),
		);

		return $sections;
	}

	// ok
	public function single_metabox( $metaboxes ) {

    	$sections_content = array(
	        'title' => esc_html__('Content', 'plethora-framework'),
            'heading' => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
	        'icon_class'    => 'icon-large',
			'icon'       => 'el-icon-lines',
	        'fields'        => array(
				// The 'layout' option for some reason was not displaying a default value on metabox
				// Therefore, unlike the rest configuration, here we HAD TO declare a 'metabox_default' attribute value
	            array(
	                'id'        =>  METAOPTION_PREFIX .'product-layout',
	                'title'     => esc_html__( 'Product Post Layout', 'plethora-framework' ),
	                'type'      => 'image_select',
	                'default'   => 'right_sidebar',
	                'customizer'=> array(),
					'options' => array(
							'full'         => ReduxFramework::$_url . 'assets/img/1c.png',
							'right_sidebar'         => ReduxFramework::$_url . 'assets/img/2cr.png',
							'left_sidebar'         => ReduxFramework::$_url . 'assets/img/2cl.png',
		                )
	            ),
				array(
					'id'=> METAOPTION_PREFIX .'product-sidebar',
					'required'=> array( METAOPTION_PREFIX .'product-layout','!=', 'full' ),
					'type' => 'select',
					'data' => 'sidebars',
					'multi' => false,
					'title' => esc_html__('Product Post Sidebar', 'plethora-framework'),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-wootitle',
					'type'    => 'switch',
					'title'   => esc_html__('Display WooCommerce Title', 'plethora-framework'),
					'desc'    => esc_html__('Display the classic WooCommerce product title next to product image', 'plethora-framework'),
					'options' => array(
									1 => esc_html__('Display', 'plethora-framework'),
									0 => esc_html__('Hide', 'plethora-framework'),
								),
					),
				array(
					'id'       => METAOPTION_PREFIX .'product-subtitle-text',
					'type'     => 'text',
					'title'    => esc_html__('Subtitle', 'plethora-framework'),
					'desc'    => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'),
					'translate' => true,
					),

				array(
					'id'      => METAOPTION_PREFIX .'product-breadcrumbs',
					'type'    => 'button_set',
					'title'   => esc_html__('Breadcrumbs ( Product Page )', 'plethora-framework'),
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-rating',
					'type'    => 'button_set',
					'title'   => esc_html__('Ratings ( Product Page )', 'plethora-framework'),
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-price',
					'type'    => 'button_set',
					'title'   => esc_html__('Price  ( Product Page )', 'plethora-framework'),
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-addtocart',
					'type'    => 'button_set',
					'title'   => esc_html__('"Add To Cart" Button ( Product Page )', 'plethora-framework'),
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-meta',
					'type'    => 'button_set',
					'title'   => esc_html__('Product Categories', 'plethora-framework'),
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-sale',
					'type'    => 'button_set',
					'title'   => esc_html__('"Sale" Icon ( Product Page )', 'plethora-framework'),
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-tab-description',
					'type'    => 'button_set',
					'title'   => esc_html__('Description Tab', 'plethora-framework'),
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-tab-reviews',
					'type'    => 'button_set',
					'title'   => esc_html__('Reviews Tab', 'plethora-framework'),
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-tab-attributes',
					'type'    => 'button_set',
					'title'   => esc_html__('Additional Information Tab', 'plethora-framework'),
					'descr'   => esc_html__('Remember that this tab is NOT displayed by defaul if product has no attributes', 'plethora-framework'),
					'options' => array(
							'display'		=> esc_html__('Display', 'plethora-framework'),
							'hide'	=> esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-related',
					'type'    => 'button_set',
					'title'   => esc_html__('Related Products', 'plethora-framework'),
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),
				array(
					'id'      => METAOPTION_PREFIX .'product-upsell',
					'type'    => 'button_set',
					'title'   => esc_html__('Upsell Products', 'plethora-framework'),
					'options' => array(
							'display' => esc_html__('Display', 'plethora-framework'),
							'hide'   => esc_html__('Hide', 'plethora-framework'),
							),
					),

				array(
					'id'            => METAOPTION_PREFIX .'product-related-number',
					'type'          => 'slider',
					'title'         => esc_html__('Related/Upsell Products Max Results', 'plethora-framework'),
					"min"           => 2,
					"step"          => 1,
					"max"           => 36,
					'display_value' => 'text'
					),

				array(
					'id'            => METAOPTION_PREFIX .'product-related-columns',
					'type'          => 'slider',
					'title'         => esc_html__('Related/Upsell Products Columns', 'plethora-framework'),
					"min"           => 2,
					"step"          => 1,
					"max"           => 4,
					'display_value' => 'text'
					),
			)
        );

		$sections = array();
		$sections[] = $sections_content;
		if ( has_filter( 'plethora_metabox_singleproduct') ) {

			$sections = apply_filters( 'plethora_metabox_singleproduct', $sections );
		}

	    $metaboxes[] = array(
	        'id'            => 'metabox-single-product',
	        'title'         => esc_html__( 'Product Options', 'plethora-framework' ),
	        'post_types'    => array( 'product' ),
	        'position'      => 'normal', // normal, advanced, side
	        'priority'      => 'default', // high, core, default, low
	        'sections'      => $sections,
	    );

    	return $metaboxes;
	}

	public static function map_native_shortcodes_to_vc() {

		Plethora_Shortcode::vc_map( self::shortcode_map_recent_products() );
		Plethora_Shortcode::vc_map( self::shortcode_map_featured_products() );
		Plethora_Shortcode::vc_map( self::shortcode_map_product() );
		Plethora_Shortcode::vc_map( self::shortcode_map_products() );
		Plethora_Shortcode::vc_map( self::shortcode_map_add_to_cart() );
		Plethora_Shortcode::vc_map( self::shortcode_map_product_page() );
		Plethora_Shortcode::vc_map( self::shortcode_map_product_category() );
		Plethora_Shortcode::vc_map( self::shortcode_map_product_categories() );
		Plethora_Shortcode::vc_map( self::shortcode_map_sale_products() );
		Plethora_Shortcode::vc_map( self::shortcode_map_best_selling_products() );
		Plethora_Shortcode::vc_map( self::shortcode_map_top_rated_products() );
		Plethora_Shortcode::vc_map( self::shortcode_map_product_attribute() );
		Plethora_Shortcode::vc_map( self::shortcode_map_related_products() );
	}

		public static function catalog_title_display( $display = true ) {

		$display = true;
		if ( self::is_shop_catalog() ) {

			$title = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => false ) );

			if ( empty( $title ) ) {

				$display = false;
			}
		}
		return $display;
	}

	public static function catalog_title() {

		$title = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => false ) );
		return $title;
	}

	public static function catalog_categorydescription() {
		$category_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-categorydescription', 'display' );
		if ( $category_display == 'hide') {
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
		}
	}

	public static function catalog_perpage() {
		$products_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-perpage', 12, 0, false);
		return $products_per_page;
	}

	public static function catalog_columns() {

		$products_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-columns', 4, 0, false);
		return $products_per_page;
	}

	public static function catalog_breadcrumbs() {

		if ( self::is_shop_catalog() ) {
			$breadcrumbs = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-breadcrumbs', 'display');
			if ( $breadcrumbs == 'hide') {
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
			}
		}
	}

	public static function catalog_resultscount() {

		$resultscount_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-resultscount', 'display' );
		if ( $resultscount_display == 'hide' ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
		}
	}

	public static function catalog_orderby() {

		$orderby_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-orderby', 'display', 0, false);
		if ( $orderby_display == 'hide') {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
		}
	}

	public static function catalog_rating() {

		$rating_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-rating', 'display' );
		if ( $rating_display == 'hide') {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
		}
	}

	public static function catalog_price() {

		$price_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-price', 'display' );
		if ( $price_display == 'hide') {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
		}
	}

	public static function catalog_addtocart() {

		$cart_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-addtocart', 'display' );
		if ( $cart_display == 'hide' ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}
	}

	public static function catalog_salesflash() {

		$salesflash_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-salesflash', 'display' );
		if ( $salesflash_display == 'hide' ) {
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
		}
	}

	public static function single_breadcrumbs() {

		if ( is_product() ) {
			$breadcrumbs = Plethora_Theme::option( METAOPTION_PREFIX .'product-breadcrumbs', 'display');
			if ( $breadcrumbs == 'hide') {
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
			}

		}
	}

	public static function single_title() {

		$title_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-wootitle', 'display' );
		if ( ! $title_display ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		}
	}

	public static function single_rating() {

		$rating_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-rating', 'display' );
		if ( $rating_display == 'hide') {
			remove_action( 'woocommerce_single_product_summary', 	 'woocommerce_template_single_rating', 10 );
		}
	}

	public static function single_salesflash() {

		$salesflash_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-sale', 'display' );
		if ( $salesflash_display == 'hide') {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		}
	}

	public static function single_price() {

		$price_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-price', 'display' );
		if ( $price_display == 'hide') {
			remove_action( 'woocommerce_single_product_summary', 	 'woocommerce_template_single_price', 10 );
		}
	}

	public static function single_addtocart() {

		$cart_status = Plethora_Theme::option( METAOPTION_PREFIX .'product-addtocart', 'display');
		if ( $cart_status == 'hide') {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
	}

	public static function single_meta() {

		$meta_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-meta', 'display');
		if ( $meta_display == 'hide') {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		}
	}

	public static function single_tab_description( $tabs ) {

		$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-description', 'display');
		if ( $tab_display == 'hide') {
		    unset( $tabs['description'] );
	    }
	    return $tabs;
	}

	public static function single_tab_reviews( $tabs ) {

		$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-reviews', 'display');
		if ( $tab_display == 'hide') {
		    unset( $tabs['reviews'] );
	    }
	    return $tabs;
	}

	public static function single_tab_attributes( $tabs ) {

		$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-attributes', 'display');
		if ( $tab_display == 'hide') {
		    unset( $tabs['additional_information'] );
	    }
	    return $tabs;
	}

	/**
	 * Single product related products box display filter, connected with Plethora control option
	 * Hooked on 'init'
	 */
	public static function single_related() {

		$related = Plethora_Theme::option( METAOPTION_PREFIX .'product-related', 'display');

		if ( $related !== 'display' ) {

			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
	}

	public static function single_related_config( $args ) {

		$posts_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-number', 4);
		$columns 		= Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
		$args['posts_per_page'] = $posts_per_page;
		$args['columns'] 		= $columns;
		return $args;
	}

	public static function single_upsell() {
		$upsell_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-upsell', 'display');
		$upsell_results = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-number', 4);
		$upsell_columns = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
		if ( $upsell_display == 'display' ) {
			woocommerce_upsell_display( $upsell_results, $upsell_columns );
		}
	}

    // Just a helper to avoid writing all these conditionals
    public static function is_shop_catalog(){

    	if (  is_shop() || ( is_shop() && is_search() ) || is_product_category() || is_product_tag() ) {

    		return true;
    	}
		return false;
    }

  }
}