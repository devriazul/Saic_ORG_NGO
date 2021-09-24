/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.icons = redux.field_objects.icons || {};

    redux.field_objects.icons.addIcon = function( state ) {
        if (!state.id) {
            return state.text;
        }
        return "<span><i class='" + state.id + "'></i>" + "&nbsp;&nbsp;" + state.text + "</span>";
    };

    redux.field_objects.icons.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( '.redux-container-icons:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;

                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                el.find( 'select.redux-icons-item' ).each(
                    function() {

                        var default_params = {
                            width: 'resolve',
                            triggerChange: true,
                            templateSelection: redux.field_objects.icons.addIcon,
                            templateResult: redux.field_objects.icons.addIcon,
                            allowClear: true
                        };

                        if ( $( this ).siblings( '.select2_params' ).size() > 0 ) {
                            var select2_params = $( this ).siblings( '.select2_params' ).val();
                            select2_params = JSON.parse( select2_params );
                            default_params = $.extend( {}, default_params, select2_params );
                        }

                        if ( $( this ).hasClass( 'icon' ) ) {
                            default_params = $.extend(
                                {}, {
                                    escapeMarkup: function( m ) {
                                        return m;
                                    }
                                }, default_params
                            );
                        }

                        $( this ).select2( default_params );

                        if ( $( this ).hasClass( 'select2-sortable' ) ) {
                            default_params = {};
                            default_params.bindOrder = 'sortableStop';
                            default_params.sortableOptions = {placeholder: 'ui-state-highlight'};
                            $( this ).select2Sortable( default_params );
                        }

                        $( this ).on(
                            "change", function() {
                                redux_change( $( $( this ) ) );
                                $( this ).select2SortableOrder();
                            }
                        );
                    }
                );
            }
        );
    };


})( jQuery );