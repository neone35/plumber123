( function( api, $ ) {

	api.controlConstructor['siteorigin-measurement'] = api.Control.extend( {
		ready: function () {
			var control = this;
			var container = control.container;

			var updateValue = function() {
				control.setting.set(
					container.find( '.amount' ).val() +
					container.find( '.measurement' ).val()
				);

				console.log( control.setting.get() );
			};

			container.find('.amount').keyup( updateValue );
			container.find('.measurement').change( updateValue );
		},
	} );

} )( wp.customize, jQuery );
