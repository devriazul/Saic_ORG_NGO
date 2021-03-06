<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2013

File Description: Latest News Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_LatestNews') ) {

    /**
    * @package Plethora Framework
    */
    class Plethora_Widget_LatestNews extends WP_Widget  {

        public static $feature_title          = "Any Latest Posts";                   // FEATURE DISPLAY TITLE (STRING)
        public static $feature_description    = "Display your latest blog posts"; // Feature display description (string)
        public static $theme_option_control   = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
        public static $theme_option_default   = true;                             // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
        public static $theme_option_requires  = array();                          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct      = false;                            // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
        public static $dynamic_method         = false;                            // THIS A PARENT METHOD, FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
        public static $wp_slug =  'latestnews-widget';                            // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
        public static $assets;

        public function __construct() {

          /* LEAVE INTACT ACROSS WIDGET CLASSES */

          $id_base     = WIDGETS_PREFIX . self::$wp_slug;
          $name        = '> PL | ' . self::$feature_title;
          $widget_ops  = array(
            'classname'   => self::$wp_slug,
            'description' => self::$feature_title
            );
          $control_ops = array( 'id_base' => $id_base );

          parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT
        }

        function widget( $args, $instance ) {

            extract( $args ); // EXTRACT USER INPUT

            $category            = ( ! empty( $instance['category'] ) ) ? $instance['category'] : 'post||';
            $category            = self::is_category_tax_term( $category ) ? 'post|category|'. $category : $category; // BACKWARDS COMPATIBILITY
            $category            = explode( '|', $category );
            $post_type           = isset( $category[0] ) ? $category[0] : 'post' ;
            $tax                 = isset( $category[1] ) ? $category[1] : '' ;
            $tax_term            = isset( $category[2] ) ? $category[2] : '' ;
            $number              = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
            $display_date        = ( isset( $instance['display_date'] ) ) ? absint( $instance['display_date'] ) : 1;
            $display_excerpt     = ( isset( $instance['display_excerpt'] ) ) ? absint( $instance['display_excerpt'] ) : 1;
            $display_excerpt_len = ( isset( $instance['display_excerpt_len'] ) ) ? absint( $instance['display_excerpt_len'] ) : 10;
            if ( ! $number ){ $number = 10; }

             $ln_query_args = array(
                  'posts_per_page'      => $number,
                  'no_found_rows'       => true,
                  'post_status'         => 'publish',
                  'post_type'           => $post_type,
                  'ignore_sticky_posts' => true
            );

            if ( !empty( $tax_term ) ) {

                $ln_query_args['tax_query'] = array(
                                    array(
                                        'taxonomy' => $tax,
                                        'field'    => 'slug',
                                        'terms'    => $tax_term,
                                    ),
                );
            }

             $query = new WP_Query( $ln_query_args );
             $custom_posts = array();
             if ( $query->have_posts() ) {
                  // FORMAT POST VALUES
                  while ( $query->have_posts() ) {

                    $query->the_post();

                    $excerpt = '';
                    $excerpt = $query->post->post_excerpt;
                    if ( empty( $user_excerpt ) ) {

                      $excerpt = apply_filters( 'get_the_excerpt', $query->post->post_excerpt, get_the_id() );
                      $excerpt = wp_trim_words( $excerpt, $display_excerpt_len, '...' );
                    }
                    $thumbnail = false;
                    $thumbnail_url = '';
                    if (  has_post_thumbnail() ) {
                      $thumbnail     = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ) );
                      $thumbnail_url = $thumbnail[0];
                    }
                    $custom_posts[] = array(
                        'title'           => get_the_title(),
                        'permalink'       => get_permalink(),
                        'thumbnail'       => $thumbnail,
                        'thumbnail_url'   => esc_url( $thumbnail_url ),
                        'content'         => $excerpt,
                        'display_date'    => ( $display_date == 0 || empty( $display_date ) ? '' : 1 ),
                        'display_excerpt' => ( $display_excerpt == 0 || empty( $display_excerpt ) ? '' : 1 ),
                        'date'            => get_the_date( 'M j' ),
                    );

                  }
                  wp_reset_postdata();
              }

              // PREPARE DATA FROM MUSTACHE TEMPLATE
              $widget_atts = array(
                'before_widget' => $before_widget,
                'title'         => apply_filters('widget_title', $instance['title'], $instance, WIDGETS_PREFIX . self::$wp_slug ),
                'posts'         => $custom_posts,
                'after_widget'  => $after_widget,
              );

              echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => __FILE__) );

        }

        function update( $new_instance, $old_instance ) {

               $instance                        = $old_instance;
               $instance['title']               = strip_tags($new_instance['title']);
               $instance['category']            = strip_tags($new_instance['category']);
               $instance['number']              = (int) $new_instance['number'];
               $instance['display_date']        = (int) $new_instance['display_date'];
               $instance['display_excerpt']     = (int) $new_instance['display_excerpt'];
               $instance['display_excerpt_len'] = (int) $new_instance['display_excerpt_len'];
               $alloptions                      = wp_cache_get( 'alloptions', 'options' );
               if ( isset($alloptions['widget_latestnews_entries']) ){  delete_option('widget_latestnews_entries');  }
               return $instance;

        }

        function form( $instance ) {

             $title               = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
             $category            = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
             $category            = self::is_category_tax_term( $category ) ? 'post|category|'. $category : $category;
             $number              = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
             $display_date        = isset( $instance['display_date'] ) ? absint( $instance['display_date'] ) : 1;
             $display_excerpt     = isset( $instance['display_excerpt'] ) ? absint( $instance['display_excerpt'] ) : 1;
             $display_excerpt_len = isset( $instance['display_excerpt_len'] ) ? absint( $instance['display_excerpt_len'] ) : 10;
             ?>
             <p>
               <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'plethora-framework' ); ?></label>
               <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
             </p>
             <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Filter by post type, category, tag or custom taxonomy:', 'plethora-framework' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
                   <?php
                    $post_types = Plethora_Theme::get_supported_post_types( array( 'output' => 'objects', 'exclude' => array( 'page' ) ) ); ?>
                    <?php
                    foreach ( $post_types as $type ) { ?>
                        <option id="<?php echo esc_attr( $type->name ) ?>" value="<?php echo esc_attr( $type->name ); ?>||"<?php echo ( $category === $type->name.'||' )  ? ' selected="selected"' : ''; ?>><?php echo sprintf( esc_html__( 'All %s', 'plethora-framework' ), ( ( $type->name  === 'post' ) ? ucfirst( $type->label ) : ucfirst( $type->label ) .' '. esc_html__( 'Posts', 'plethora-framework' ) ) ); ?></option>

                    <?php }

                    foreach ( $post_types as $type ) {

                        $taxonomies = get_object_taxonomies( $type->name, 'objects' ); ?>
                            <?php
                            foreach ( $taxonomies as $tax => $tax_obj ) {
                                if ( $tax === 'post_format' || $tax === 'alphabetical' ) { continue; }
                                ?>
                                <optgroup label="<?php echo $type->label; ?> > <?php echo $tax_obj->label; ?>"> <?php

                                $terms = get_terms( $tax, array( 'hide_empty' => false ) );
                                foreach ( $terms as $term ) {
                                    echo '<option id="' . esc_attr( $type->name ) . '-' . esc_attr( $tax ) . '-' . esc_attr( $term->slug ) . '" value="' . esc_attr( $type->name ) . '|' . esc_attr( $tax ) . '|' . esc_attr( $term->slug ) . '"'. ( $category === esc_attr( $type->name ). '|' . esc_attr( $tax ) . '|' . esc_attr( $term->slug )   ? ' selected="selected"' : '' ) .'>' . $term->name . '</option>';
                                }
                            }
                        ?>
                        </optgroup>

                    <?php
                    }
                   ?>
               </select>
             </p>
             <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show ( max 20 ):', 'plethora-framework' ); ?></label>
              <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>"  min="1" max="20" style="width:55px;" />
            </p>
             <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'display_date' ) ); ?>"><?php esc_html_e( 'Display Date:', 'plethora-framework' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_date' ) ); ?>">
                    <option value="0"><?php esc_html_e( 'No', 'plethora-framework' ); ?></option>
                    <option value="1" <?php echo ( $display_date == 1 ? ' selected="selected"' : '' ) ?>><?php esc_html_e( 'Yes', 'plethora-framework' ); ?></option>
                </select>
             </p>
             <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'display_excerpt' ) ); ?>"><?php esc_html_e( 'Display Excerpt:', 'plethora-framework' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>">
                    <option value="0"><?php esc_html_e( 'No', 'plethora-framework' ); ?></option>
                    <option value="1" <?php echo ( $display_excerpt == 1 ? ' selected="selected"' : '' ) ?>><?php esc_html_e( 'Yes', 'plethora-framework' ); ?></option>
                </select>
             </p>
             <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'display_excerpt_len' ) ); ?>"><?php echo esc_html__( 'Excerpt Length ( max 55 words ):', 'plethora-framework' ); ?></label>
                <input style="width:55px;margin-bottom:0; padding-bottom:0;" id="<?php echo esc_attr( $this->get_field_id( 'display_excerpt_len' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_excerpt_len' ) ); ?>" type="number"  min="1" max="55" value="<?php echo esc_attr( $display_excerpt_len ); ?>" />
                <span style="display:block; color:darkred; margin-top:0; padding-top:0; font-size:11px;"> <?php echo esc_html__( 'IMPORTANT: excerpt words limit is applied only on automatic excerpts. User defined post excerpts will not be affected.', 'plethora-framework' ); ?></span>
             </p>
            <?php
        }

        public function is_category_tax_term( $term ) {

            if ( empty( $this->category_terms  ) ) { // avoid duplicate queries

                $this->category_terms = get_terms( 'category', array( 'hide_empty' => false ) );
            }

            foreach ( $this->category_terms as $term_obj ) {

                if ( !empty( $term ) && !is_array( $term ) && ( $term === $term_obj->slug || $term === $term_obj->name ) ) {

                    return true;
                }
            }
            return false;

        }
    }
}
