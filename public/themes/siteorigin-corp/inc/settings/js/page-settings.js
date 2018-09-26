( function( $ ){

	var api = wp.customize;
	api.bind( 'preview-ready', function() {
		api.preview.bind('active', function(){
			api.preview.send( 'page-settings', soTemplateSettings.page );
		});
	} );

} )( jQuery );
