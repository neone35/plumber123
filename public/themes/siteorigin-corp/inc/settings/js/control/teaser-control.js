
/* globals ajaxurl, wp */

( function( api, $ ) {

	api.controlConstructor['siteorigin-teaser'] = api.Control.extend( {
        ready: function () {
            var control = this;
            var container = control.container;

            container.find('.so-premium-upgrade').click( function( e ){
                // We can show a modal here at some point, for now we just direct users to SiteOrigin
            } );
        }
    } );

} )( wp.customize, jQuery );
