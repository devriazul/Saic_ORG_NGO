<?php
/**
 * Column template
 */
// Get attributes sent by set_query_var
$atts = get_query_var( 'shortcode_atts' );
// Extract $atts to variables
if ( is_array( $atts ) ) { extract($atts); }

$output = '';
$output .= '            <div class="' . esc_attr( implode( ' ', $class ) ) . '" style="' . esc_attr( implode( ' ', $style ) ) . '">'. "\n";
$output .= '                <div class="wpb_wrapper">'. "\n";
if( !empty( $heading ) ) {
    $output .= '                <h3 class="col_header '. esc_attr( $heading['align'] ) .'">'. wp_kses( $heading['title'], Plethora_Theme::allowed_html_for( 'heading' ) ) .'</h3>'. "\n";
}
$output .= Plethora_Shortcode::remove_wpautop( $content );
$output .= '                </div>'. "\n";
$output .= '            </div>'. "\n";

echo $output;