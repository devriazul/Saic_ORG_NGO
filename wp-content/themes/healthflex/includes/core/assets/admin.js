/*
 * jQuery throttle / debounce - v1.1 - 3/7/2010
 * http://benalman.com/projects/jquery-throttle-debounce-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function(b,c){var $=b.jQuery||b.Cowboy||(b.Cowboy={}),a;$.throttle=a=function(e,f,j,i){var h,d=0;if(typeof f!=="boolean"){i=j;j=f;f=c}function g(){var o=this,m=+new Date()-d,n=arguments;function l(){d=+new Date();j.apply(o,n)}function k(){h=c}if(i&&!h){l()}h&&clearTimeout(h);if(i===c&&m>e){l()}else{if(f!==true){h=setTimeout(i?k:l,i===c?e-m:e)}}}if($.guid){g.guid=j.guid=j.guid||$.guid++}return g};$.debounce=function(d,e,f){return f===c?a(d,e,false):a(d,f,e!==false)}})(this);

/**
 * Theme Options > Advanced: Blinking Indication for misplaced <script> tags on Custom JS & Google Analytics text areas
 * Dependencies 	 : jQuery.debounce()
 * Created by        : Kostas Minaidis
 * Date              : 27 Oct 2017
 */
jQuery(function($){

	"use strict";

	$("#ple-customjs-textarea, #ple-analyticsscript-textarea, #ple-customcss-textarea")
	.on("change keyup paste", $.debounce(300, function(e){
		if ( !$.debounce ) return;
		var $this = $(this);
		if ( $this.val().match(/<\/?script>|<\/?style>/i) ){
			$this.parent().find(".description.field-desc").addClass('blink_me');
		} else {
			$this.parent().find(".description.field-desc").removeClass('blink_me');
		}
	}));

});