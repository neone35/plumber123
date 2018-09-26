( function( api, $ ) {

	api.controlConstructor['siteorigin-image-select'] = api.Control.extend({
        ready: function () {
            var control = this;
            var container = control.container;

            container.find('.image-options li').click( function(){
                container.find('select').val( $(this).data('key')).change();
                container.find('li').removeClass('active');
                $(this).addClass('active');
            } );
        }
    });

} )( wp.customize, jQuery );
