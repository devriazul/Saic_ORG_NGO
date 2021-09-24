// REFERENCE: https://gist.github.com/yangshun/9892961

(function($){ 

	function parseVideo(url) {
	    // - Supported YouTube URL formats:
	    //   - http://www.youtube.com/watch?v=My2FRPA3Gf8
	    //   - http://youtu.be/My2FRPA3Gf8
	    //   - https://youtube.googleapis.com/v/My2FRPA3Gf8
	    // - Supported Vimeo URL formats:
	    //   - http://vimeo.com/25451551
	    //   - http://player.vimeo.com/video/25451551
	    // - Also supports relative URLs:
	    //   - //player.vimeo.com/video/25451551
	    // - DailyMotion
	    //  - http://www.dailymotion.com/video/x6ga7eg

		var match = url.match(/(http:\/\/|https:\/\/|)(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|dailymotion\.com)\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);
		var type  = "unknown";
		if ( match ){
		    if ( match[3].indexOf('youtu') > -1 ) {
		        type = 'youtube';
		    } else if ( match[3].indexOf('vimeo') > -1 ) {
		        type = 'vimeo';
		    } else if ( match[3].indexOf('dailymotion') > -1 ) {
		    	type = 'dailymotion';
		    }
		}
	    return {
			type: type,
			id  : match && match[6] || ""
	    };
	}

	var init = function(){

		$("input.video_url").on("keyup", function(e){

			var videoProvider = "unknown";
			var videoID       = "";
			var videoURL      = $(e.currentTarget).val();
			var videoObj      = parseVideo(videoURL);

			videoProvider = videoObj.type;
			videoID       = videoObj.id;

			$(".embedlite_video_provider").val(videoProvider);
			$(".embedlite_video_id").val(videoID);

		});

	}

	window.ple_embedlite_init = init;

}(jQuery));	

