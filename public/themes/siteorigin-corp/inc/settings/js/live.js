
/* globals soSettings, jQuery */

jQuery( function($){

    var $style = $('style[data-siteorigin-settings="true"]');
    if( $style.length === 0 ) {
        $style = $('<style type="text/css" id="siteorigin-settings-css" data-siteorigin-settings="true"></style>').appendTo('head');tinycolor.js
    }

    function replaceAll(string, find, replace) {
        return string.replace(new RegExp( find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1") , 'g'), replace);
    }

	function hexToRgb(hex) {
		// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		hex = hex.replace(shorthandRegex, function(m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? [
			parseInt(result[1], 16),
			parseInt(result[2], 16),
			parseInt(result[3], 16)
		] : null;
	}

    var updateCss = function(){
        // Create a copy of the CSS
        var css = JSON.parse( JSON.stringify(soSettings.css) );
        var re;
        for( var k in soSettings.settings ) {
            css = replaceAll( css, '${' + k + '}', soSettings.settings[k] );
        }

        // Now we also need to handle the CSS functions.
        // This should mirror what's in PHP - SiteOrigin_Settings::css_functions
        var match, replace, prepend, fargs, color;
        do {
            match =  css.match(/\.([a-z\-]+) *\(([^\)]*)\) *;/);
            if( match === null ) {
                break;
            }
            replace = '';
            prepend = '';

            switch( match[1] ) {
                case 'font':
                    try {
                        fargs = JSON.parse( match[2] );
                    }
                    catch( e ) {
                        break;
                    }

                    if( fargs.font === '' ) {
                        break;
                    }

                    if( fargs.webfont ) {
                        prepend = '@import url(//fonts.googleapis.com/css?';
                        prepend += 'family=' + encodeURIComponent( fargs.font ) + ':' + encodeURIComponent( fargs.variant );
                        if( fargs.subset !== null ) {
                            prepend += '&subset=' + encodeURIComponent( fargs.subset );
                        }
                        prepend += '); ';
                    }

                    replace += 'font-family: "' + fargs.font + '", ' + fargs.category + '; ';

                    var weight;
                    if( fargs.variant && fargs.variant.indexOf('italic' ) !== -1 ) {
                        weight = fargs.variant.replace('italic', '');
                        replace += 'font-style: italic; ';
                    }
                    else {
                        weight = fargs.variant;
                    }

                    if( fargs.variant === '' ) {
                        fargs.variant = 'regular';
                    }
                    replace += 'font-weight: ' + weight + '; ';

                    break;

	            case 'rgba' :
		            try {
			            fargs = match[2].split(',');
		            }
		            catch( e ) {
			            break;
		            }
		            color = tinycolor( fargs[0].trim() );

		            color.setAlpha( parseFloat( fargs[1] )  );
		            replace = color.toRgbString();
		            break;

	            case 'lighten':
		            try {
			            fargs = match[2].split(',');
		            }
		            catch( e ) {
			            break;
		            }
		            color = tinycolor( fargs[0].trim() );
		            fargs[1] = fargs[1].trim();
		            if( fargs[1].indexOf('%') > -1 ) {
			            fargs[1] = parseInt( fargs[1] );
		            } else {
			            fargs[1] = Math.floor( parseFloat( fargs[1] ) * 100 );
		            }
		            color.lighten( fargs[1] );
		            replace = color.toHexString();

		            break;

	            case 'darken':
		            try {
			            fargs = match[2].split(',');
		            }
		            catch( e ) {
			            break;
		            }
		            color = tinycolor( fargs[0].trim() );
		            fargs[1] = fargs[1].trim();
		            if( fargs[1].indexOf('%') > -1 ) {
			            fargs[1] = parseInt( fargs[1] );
		            } else {
			            fargs[1] = Math.floor( parseFloat( fargs[1] ) * 100 );
		            }
		            color.darken( fargs[1] );
		            replace = color.toHexString();

		            break;
            }

            css = css.replace( match[0], replace );
            css = prepend + css;
        } while( match !== null );

        $style.html( css );
    };
    updateCss();

    if( soSettings.settings !== false && soSettings.css !== '' ) {

        $.each( soSettings.settings, function(k, setting){
            wp.customize( 'theme_settings_' + k, function( value ) {
                value.bind( function( newval ) {
                    soSettings.settings[k] = newval;
                    updateCss();
                } );
            } );
        } );
    }
} );
