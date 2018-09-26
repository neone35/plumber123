
/* globals ajaxurl, wp */

( function( api, $, _ ) {

	api.controlConstructor['siteorigin-widget-setting'] = api.Control.extend( {
		ready: function () {
			var control = this;
			var container = control.container;

			container.find( '.so-widget-form' ).hide();

			container.find('.so-edit-widget, .so-widget-form .so-widget-close').click( function( e ){
				e.preventDefault();

				// We can show a modal here at some point, for now we just direct users to SiteOrigin
				container.find( '.so-widget-form' ).toggle();

				if( ! container.find( '.so-widget-form' ).is(':visible') ) {
					control.updateValue();
				}
			} );
		},

		updateValue: function(){
			var formValues = this.getFormValues();
			var widgetValues;

			try {
				widgetValues = formValues['siteorigin_settings_widget'][1];
			}
			catch ( e ) {
				widgetValues = {};
			}

			widgetValues.panels_info = {
				'class' : this.container.find( '.so-widget-form' ).data('widget-class'),
			};

			this.setting.set( widgetValues );
		},

		getFormValues: function(){
			var $f = this.container.find( '.so-widget-form' );
			var data = {}, parts;

			// Find all the named fields in the form
			$f.find( '[name]' ).each( function () {
				var $$ = $( this );

				try {
					var name = /([A-Za-z_]+)\[(.*)\]/.exec( $$.attr( 'name' ) );
					if ( _.isEmpty( name ) ) {
						return true;
					}

					// Create an array with the parts of the name
					if ( _.isUndefined( name[2] ) ) {
						parts = $$.attr( 'name' );
					} else {
						parts = name[2].split( '][' );
						parts.unshift( name[1] );
					}

					parts = parts.map( function ( e ) {
						if ( ! isNaN( parseFloat( e ) ) && isFinite( e ) ) {
							return parseInt( e );
						} else {
							return e;
						}
					} );

					var sub = data;
					var fieldValue = null;

					var fieldType = (
						_.isString( $$.attr( 'type' ) ) ? $$.attr( 'type' ).toLowerCase() : false
					);

					// First we need to get the value from the field
					if ( fieldType === 'checkbox' ) {
						if ( $$.is( ':checked' ) ) {
							fieldValue = $$.val() !== '' ? $$.val() : true;
						} else {
							fieldValue = null;
						}
					}
					else if ( fieldType === 'radio' ) {
						if ( $$.is( ':checked' ) ) {
							fieldValue = $$.val();
						} else {
							//skip over unchecked radios
							return;
						}
					}
					else if ( $$.prop( 'tagName' ) === 'TEXTAREA' && $$.hasClass( 'wp-editor-area' ) ) {
						// This is a TinyMCE editor, so we'll use the tinyMCE object to get the content
						var editor = null;
						if ( ! _.isUndefined( tinyMCE ) ) {
							editor = tinyMCE.get( $$.attr( 'id' ) );
						}

						if ( editor !== null && _.isFunction( editor.getContent ) && ! editor.isHidden() ) {
							fieldValue = editor.getContent();
						} else {
							fieldValue = $$.val();
						}
					}
					else if ( $$.prop( 'tagName' ) === 'SELECT' ) {
						var selected = $$.find( 'option:selected' );

						if ( selected.length === 1 ) {
							fieldValue = $$.find( 'option:selected' ).val();
						}
						else if ( selected.length > 1 ) {
							// This is a mutli-select field
							fieldValue = _.map( $$.find( 'option:selected' ), function ( n, i ) {
								return $( n ).val();
							} );
						}

					} else {
						// This is a fallback that will work for most fields
						fieldValue = $$.val();
					}

					// Now convert this into an array
					if ( fieldValue !== null ) {
						for ( var i = 0; i < parts.length; i ++ ) {
							if ( i === parts.length - 1 ) {
								if ( parts[i] === '' ) {
									// This needs to be an array
									sub.push( fieldValue );
								} else {
									sub[parts[i]] = fieldValue;
								}
							} else {
								if ( _.isUndefined( sub[parts[i]] ) ) {
									if ( parts[i + 1] === '' ) {
										sub[parts[i]] = [];
									} else {
										sub[parts[i]] = {};
									}
								}
								sub = sub[parts[i]];
							}
						}
					}
				}
				catch ( error ) {
					// Ignore this error, just log the message for debugging
					console.log( 'Field [' + $$.attr('name') + '] could not be processed and was skipped - ' + error.message );
				}

			} ); // End of each through input fields

			return data;
		}
	} );

} )( wp.customize, jQuery, _ );
