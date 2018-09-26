
/* globals jQuery, wp */

( function( api, $ ) {

    var loadedFonts = {};

    /**
     * The font control object
     */
    api.controlConstructor['siteorigin-font'] = api.Control.extend( {
        ready: function(){
            var control = this;

            var $f = control.container.find('select.font'),
                $v = control.container.find('select.font-variant'),
                $s = control.container.find('select.font-subset' ),
	            hasSetup = false;

            $f.change( function(){
                var $fs = $(this).find('option:selected');
                $v.empty().val('');
                $s.empty().val('');

                if( $fs.data('variants') !== undefined ) {
                    // Lets populate the variants and subsets
                    $v.append( $("<option></option>")).val('');
                    $.each( $fs.data('variants').split(','), function(i, v){
                        $v.append( $("<option></option>").html(v) );
                    } );
                    $v.val('regular');

                    if( $v.find('option').length > 2 ) {
                        $v.parent().show();
                    }
                    else {
                        $v.parent().hide();
                    }
                }
                else {
                    $v.parent().hide();
                }

                if( $fs.data('subsets') !== undefined ) {
                    // Lets populate the variants and subsets
                    $s.append( $("<option></option>"));
                    $.each( $fs.data('subsets').split(','), function(i, v){
                        $s.append( $("<option></option>").html(v) );
                    } );
                    $s.val('latin');

                    if( $s.find('option').length > 2 ) {
                        $s.parent().show();
                    }
                    else {
                        $s.parent().hide();
                    }
                }
                else {
                    $s.parent().hide();
                }
            } );

            var changeValue = function(){
	            if( ! hasSetup ) {
		            return;
	            }
	            var val = {};
                val.font = $f.val();
                val.webfont = $f.find('option:selected').data('webfont');
                val.category = $f.find('option:selected').data('category');
                val.variant = $v.val();
                val.subset = $s.val();

                control.setting.set( JSON.stringify(val) );
            };

            control.container.find('select').change(changeValue);

            // Now, lets set everything up to start
            if( control.setting() !== '' ) {
                var vals = JSON.parse( control.setting() );
                $f.val( vals.font).change();
                $v.val( vals.variant );
                $s.val( vals.subset );
            }
            else {
                $f.change();
            }

            // This is some setup stuff that needs to happen after the section is expanded
            var chosen = null;
	        api.section( control.section() ).container
                .on( 'expanded', function() {
                    // Setup this field for the first time
                    if( chosen === null ){
                        var timeout = null;
                        $f
                            .on('chosen:ready', function( e, params ){
                                var dropdown = params.chosen.dropdown;
                                var results = dropdown.find('.chosen-results');

                                dropdown.find('.chosen-results').on('scroll', function(){
                                    clearTimeout(timeout);
                                    timeout = setTimeout(function(){
                                        // These are the fonts we'll load
                                        var loadFonts = [], font, match;

                                        results.find('li').each( function(){
                                            var $$ = $(this),
                                                offset = $$.position().top;

                                            // Check that this element is in the viewport and not a websafe font
                                            if( $$.attr('style') !== undefined &&
                                                $$.attr('style').indexOf('__websafe') === -1 &&
                                                offset > - 10 &&
                                                offset < results.outerHeight() + 30
                                            ) {
                                                match = $$.attr('style').match(/font-family: ([^,]+),.*;/);
                                                font = match[1].replace(/'/g, '').trim();
                                                if( typeof loadedFonts[font] === 'undefined' ) {
                                                    loadFonts.push(font);
                                                    loadedFonts[font] = true;
                                                }
                                            }
                                        } );

                                        // Load the fonts
                                        if( loadFonts.length > 0 ) {
                                            var loadUrl = '//fonts.googleapis.com/css';
                                            loadUrl += '?family=' + loadFonts.join('|').replace(' ', '+');
                                            loadUrl += '&text=0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

                                            $("<link/>", {
                                                rel: "stylesheet",
                                                type: "text/css",
                                                href: loadUrl
                                            }).appendTo("head");
                                        }

                                    }, 500);
                                });

                                // Trigger a fake scroll after a short timeout
                                setTimeout( function(){
                                    results.trigger('scroll');
                                }, 500 );

                                // After the user searches, trigger a scroll
                                params.chosen.search_field.on('keyup', function(){
                                    setTimeout( function(){
                                        results.trigger('scroll');
                                    }, 500 );
                                });
                            } )
                            .on('chosen:showing_dropdown', function(e, params){
                                params.chosen.dropdown.find('.chosen-results').trigger('scroll');
                            })
                            .chosen({
                                allow_single_deselect: true,
                                search_contains: true
                            });
                        chosen = true;
                    }
			        hasSetup = true;
                });
        }
    } );

} )( wp.customize, jQuery );
