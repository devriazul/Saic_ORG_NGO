<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_icons' ) ) {
    class ReduxFramework_icons {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 1.0.0
         */
        public function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;

                // Set extension dir & url
                if ( empty( $this->extension_dir ) ) {
                    $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                    $this->extension_url = plugin_dir_url( __FILE__ ) ;
                }
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        public function render() {
            $sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

            if ( ! empty( $sortable ) ) { // Dummy proofing  :P
                $this->field['multi'] = true;
            }

            if ( empty( $this->field['args'] ) ) {
                $this->field['args'] = array();
            }

            if ( empty( $this->field['group'] ) ) {
                $this->field['group'] = array();
            }

            if ( empty( $this->field['options'] ) ) {
                $this->field['options'] = array();
            }

            $this->field['class'] = "icon";

            if ( ! empty( $this->field['options'] ) ) {
                $multi = ( isset( $this->field['multi'] ) && $this->field['multi'] ) ? ' multiple="multiple"' : "";

                if ( ! empty( $this->field['width'] ) ) {
                    $width = ' style="' . $this->field['width'] . '"';
                } else {
                    $width = ' style="width: 40%;"';
                }

                $nameBrackets = "";
                if ( ! empty( $multi ) ) {
                    $nameBrackets = "[]";
                }

                $placeholder = ( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) : __( 'Select an item', 'redux-framework' );

                if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                    $select2_params = json_encode( $this->field['select2'] );
                    $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

                    echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
                }

                if ( isset( $this->field['multi'] ) && $this->field['multi'] && isset( $this->field['sortable'] ) && $this->field['sortable'] && ! empty( $this->value ) && is_array( $this->value ) ) {
                    $origOption             = $this->field['options'];
                    $this->field['options'] = array();

                    foreach ( $this->value as $value ) {
                        $this->field['options'][ $value ] = $origOption[ $value ];
                    }

                    if ( count( $this->field['options'] ) < count( $origOption ) ) {
                        foreach ( $origOption as $key => $value ) {
                            if ( ! in_array( $key, $this->field['options'] ) ) {
                                $this->field['options'][ $key ] = $value;
                            }
                        }
                    }
                }

                $sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

                echo '<select ' . $multi . ' id="' . $this->field['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $nameBrackets . '" class="redux-icons-item ' . $this->field['class'] . $sortable . '"' . $width . ' rows="6">';
                echo '<option></option>';

                foreach ( $this->field['options'] as $k => $v ) {

                    if (is_array($v)) {
                        echo '<optgroup label="' . $k . '">';

                        foreach($v as $opt => $val) {
                            $this->make_option($opt, $val, $k);
                        }

                        echo '</optgroup>';

                        continue;
                    }

                    $this->make_option($k, $v);
                }
                //foreach

                echo '</select>';
            } else {
                echo '<strong>' . __( 'No items of this type were found.', 'redux-framework' ) . '</strong>';
            }
        } //function

        private function make_option($id, $value, $group_name = '') {
            if ( is_array( $this->value ) ) {
                $selected = ( is_array( $this->value ) && in_array( $id, $this->value ) ) ? ' selected="selected"' : '';
            } else {
                $selected = selected( $this->value, $id, false );
            }

            echo '<option value="' . $id . '"' . $selected . '>' . $value . '</option>';
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        public function enqueue() {
            wp_enqueue_style( 'select2-css' );

            wp_enqueue_script(
                'redux-field-icons-js',
                $this->extension_url . 'field_icons.js',
                array( 'jquery', 'select2-js', 'redux-js' ),
                time(),
                true
            );
        } //function
    } //class
}