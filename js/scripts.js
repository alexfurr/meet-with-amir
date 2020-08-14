


var MWA_JS = {

    //---
    site_wrapper_id:    'mwa_listener_wrap',

    //---
    init: function () {
        this.add_listeners();
    },

    //---
    add_listeners: function () {
        jQuery('#' + MWA_JS.site_wrapper_id ).on( 'click', '.has-click-event', function ( event ) {
            MWA_JS.on_ui_event( event, this );
            event.preventDefault();
        });

    },

    //---
    on_ui_event: function ( event, element ) {
        var method = jQuery( element ).attr('data-method');
        if ( typeof MWA_JS[ method ] !== 'undefined' ) {
            MWA_JS[ method ]( event, element );
        }
    },



    // List of actual interactions
    //---
    confirm_booking_date: function ( event, element ) {
        var this_date_str = jQuery( element ).data("this_date_str");
        var this_date = jQuery( element ).data("this_date");
        // Show the popup
    	document.getElementById('imperial-modal').style.display = "block";
        var confirm_html = '<h2>Please confirm this booking</h2>';
        confirm_html+='<div style="padding:20px">You are signing up to meet Dr Sam on:<br/><strong>'+this_date_str+'</strong></div>';
        confirm_html+='<a href="?action=mwa_make_booking&date='+this_date+'" class="imperial-button">Make this booking</a>';

        document.getElementById('imperial-modal-content').innerHTML = confirm_html;
    },



    //---
    delete_booking_check: function ( event, element ) {
        var booking_id = jQuery( element ).data("id");


        document.getElementById('imperial-modal').style.display = "block";
        var confirm_html = '<h2>Are you sure you want to delete this booking?</h2>';
        confirm_html+='<a href="?action=mwa_delete_booking&id='+booking_id+'" class="imperial-button">Yes, delete this booking</a>';

        document.getElementById('imperial-modal-content').innerHTML = confirm_html;


    },

    //---
    close_modal: function ( event, element ) {
        document.getElementById('imperial-modal').style.display = "none";
    },






};

jQuery( document ).ready( function () {
    MWA_JS.init();
});
