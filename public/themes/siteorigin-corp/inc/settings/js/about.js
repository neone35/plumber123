jQuery( function( $ ){
	// TODO start by preloading all the video thumbnails

	// Now turn the images into a slideshow
	var $thumbs = $('.about-video .about-video-image');
	if( $thumbs.length > 1 ) {
		$thumbs.first().addClass('about-video-current');
		$thumbs.not('.about-video-current' ).hide();

		var waitTime = 5000;

		var nextFrame = function(){
			var $next = $thumbs.filter( '.about-video-current' ).next();
			if( ! $next.length ) {
				$next = $thumbs.first();
				waitTime += 1000;
			}

			var $nc = $next.clone().hide();
			$('.about-video-images' ).append( $nc );

			$nc.fadeIn( 2000, function(){
				$thumbs.removeClass('about-video-current' ).hide();
				$next.addClass('about-video-current' ).show();
				$nc.remove();
			} );

			// Increase the wait time and trigger the next frame load
			waitTime += 750;
			waitTime = Math.min( waitTime, 8500 );

			setTimeout( nextFrame, waitTime );
		};
		setTimeout( nextFrame, waitTime );
	}
} );
