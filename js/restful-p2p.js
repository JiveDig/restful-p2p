;(function( $ ) {
    'use strict';

    $('.restful-p2p-button').show();

    $('.restful-p2p-button').on( 'click', function(connection) {

        var clicked = $(this);

        var data = {
                name: clicked.attr('data-name'),
                from: clicked.attr('data-from-id'),
                to: clicked.attr('data-to-id'),
            };

        var connect   = clicked.attr('data-connect');
        var connected = clicked.attr('data-connected');
        var loading   = clicked.attr('data-loading');

        clicked.toggleClass('loading').html(loading);

        if ( clicked.is( '.connect' ) ) {
            var url  = 'restful-p2p/v1/connect/';
            var text = connected;
        } else if ( clicked.is( '.connected' ) ) {
            var url  = 'restful-p2p/v1/disconnect/';
            var text = connect;
        }

        $.ajax({
            method: "POST",
            url: restful_p2p_vars.root + url,
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restful_p2p_vars.nonce );
            },
            success: function( response ) {
                if ( response.success == true ) {
                    clicked.toggleClass('loading connect connected').html(text);
                } else {
                    console.log(response.message);
                }
            },
            fail: function( response ) {
                // What to do if no response at all?
                console.log( 'Sorry, something went wrong. Please try again.' );
            }
        });

    });

})( jQuery );