( function( api, $ ) {

	api.controlConstructor['siteorigin-text-select'] = api.Control.extend({
		ready: function () {
			var control = this;
			var container = control.container;

			container.find('select').change( function(){
				var $$ = $(this);
				container.find( 'input[type=text]' ).val( $$.val() );
			} );
		}
	});

} )( wp.customize, jQuery );
