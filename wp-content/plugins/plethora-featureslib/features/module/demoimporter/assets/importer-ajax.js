//// INIT SCRIPTS START
(function($){

	"use strict";

	var $doc = $(document);

	/*
	Demo import panel display init
	*/
	$doc.on( 'click', '.start_import', function( event ) {
		$.ajax({
		    url: pdi.ajaxurl, 
		    type: 'post',
		    data: 'demo_slug='+ this.id +'&action=pdi_demo_panel&pdi_nonce='+ pdi.pdi_nonce , // Notice the action name here! This is the basis on which WP calls your process_my_ajax_call() function.
		    cache: false,
		    beforeSend: function ( ) {

				$('.og-grid').css( 'visibility', 'hidden' );
				$('.og-grid').css( 'display','none' );
				$('.pdi .loader').css( 'visibility', 'visible' );
		    },
		    success: function ( response ) {
				
				$('.pdi .loader').css( 'visibility', 'hidden' );
				$('.pdi-workpanel').prepend( response );
		    },
		    error: function ( response ) {

				$('.pdi .loader').css( 'visibility', 'hidden' );
				$('.og-grid').css( 'visibility', 'visible' );
				$('.og-grid').css( 'display','initial' );
				alert( 'It seems that for some reason, the import procedure cannot start right now. Please try again later.' );
		    }
		});
	})	
	//// INIT SCRIPTS END

	//// FUNCTIONS START

	/*
	Import demo init
	*/
	$doc.on( 'click', '.import_init', function( event ) {
		event.preventDefault();
		var $import_button = $(this);
		var import_button_url = $import_button.attr( 'href' );
		$import_button
			.attr( 'href', '#' )
			.removeClass('import_init')
			.blur();
		var imports_counter = 0;
		$doc.queue('imports', queue_import_events( 'in_progress', imports_counter++, import_button_url ) );
		$.each( pdi.actions, function( import_type, data ) {
			
			if ( $('#'+ import_type ).length && import_type == 'attachment' ) {

				if ( parseInt( pdi.attachments_count[$import_button.attr('id')] ) > 0 ) { 

					for (var post_import_id = 0; post_import_id < pdi.attachments_count[$import_button.attr('id')] ; post_import_id++) { 

						imports_counter++;
						$doc.queue('imports', queue_import( $import_button.attr('id'), import_type, data, imports_counter, post_import_id ) );
					}
				}

			} else if ( $('#'+ import_type ).length && import_type != 'attachment' ) {

				imports_counter++;
				$doc.queue('imports', queue_import( $import_button.attr('id'), import_type, data, imports_counter ) );
			}			
		});

		$doc.queue('imports', queue_import_events( 'finished', imports_counter++, import_button_url ) );
		$doc.dequeue('imports');
		
	})	

	/*
	Adds an import side event to execution queue
	*/
	function queue_import_events( event_action, next, import_button_url ){

	    return function(next){

			switch( event_action ) {

			    case 'in_progress':

					$('.import_button')
						.html('')	
						.addClass('loadingbutton')
						.css('background-color', '#C11313');
			        next();
			        break;

			    case 'finished':

					$('.import_button')
						.html('Import Finished!<br>Click To Check Your Site!')
						.css('background-color', '#0dbf1a')
						.removeClass('loadingbutton')
						.attr( 'href', import_button_url );
			        next();
			        break;
			} 
	    }
	}

	/*
	Adds an import action to execution queue
	*/
	function queue_import( demo_slug, import_type, data, next, post_import_key ){

	    return function(next){

			if ( import_type == 'attachment') {

				do_import_single_post( demo_slug, import_type, data, post_import_key, next )

			} else {

				do_import( demo_slug, import_type, data, next );
			}
	    }
	}

	/*
	Sends an ajax request to initiate import
	*/
	function do_import( demo_slug, import_type, data, next ) {

		$.ajax({
		    url: pdi.ajaxurl, 
		    async: true,
		    type: 'post',
		    data: 'demo_slug='+ demo_slug +'&action=pdi_import&response_method='+ data.response_method +'&pdi_nonce='+ pdi.pdi_nonce , // Notice the action name here! This is the basis on which WP calls your process_my_ajax_call() function.
		    cache: false,
		    beforeSend: function ( ) {

		    	$( '.'+ import_type +'.pdi-status' )
		    		.addClass( 'loading' )
					.html( '' );
		    },
		    success: function ( response ) {

		    	$( '.'+ import_type +'.pdi-status')
		    		.removeClass( 'loading' )
					.addClass( 'success' )
					.html( data.success_notice );
		    	next();

		    },
		    error: function ( response ) {

		    	$( '.'+ import_type +'.pdi-status')
		    		.removeClass( 'loading' )
					.addClass( 'failure' )
					.html( data.error_notice );

		    }
		});
	}

	/*
	Sends an ajax request to initiate post import
	*/
	function do_import_single_post( demo_slug, import_type, data, post_import_key, next ) {

		$.ajax({
		    url: pdi.ajaxurl, 
		    async: true,
		    type: 'post',
		    data: 'demo_slug='+ demo_slug +'&action=pdi_import&response_method='+ import_type +'&post_import_key='+ post_import_key +'&pdi_nonce='+ pdi.pdi_nonce , // Notice the action name here! This is the basis on which WP calls your process_my_ajax_call() function.
		    cache: false,
		    beforeSend: function ( ) {

		    	$( '.'+ import_type +'.pdi-status .placeholder' )
		    		.html( ' ' )
		    		.addClass( 'loading' );
		    	$( '.a'+ post_import_key )
		    		.addClass( 'strong' )
		    		.removeClass( 'hide' )
		    		.prepend( data.beforeSend_notice + '<br>' );
		    },
		    success: function ( response ) {

		    	// $( '.a'+ post_import_key ).removeClass( 'loading' );
		    	$( '.'+ import_type +'.pdi-status .placeholder' ).removeClass( 'loading' );
		    	
		    		$( '.a'+ post_import_key ).addClass( 'hide' );

		    	next();

		    },
		    error: function ( response ) {

				$( '.a'+ post_import_key ).append( data.error_notice );
				next();
		    }
		});
	}

	//// FUNCTIONS END
}(jQuery));
