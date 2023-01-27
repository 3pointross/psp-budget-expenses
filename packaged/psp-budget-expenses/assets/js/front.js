jQuery(document).ready(function($){

    $('.expand-expenses').click(function(e) {
        e.preventDefault();
        $('#psp-expense-list').slideDown('fast');
        $(this).fadeOut();
    });

    $(document).on( 'click', '.js-psp-add-expense', function(e) {
        e.preventDefault();
        $(this).hide();
        $('psp-new-expense-row').show();
    });

    $(document).on( 'click', '.js-add-exp-row', function(e) {

        e.preventDefault();

        var ajaxurl = jQuery('#psp-ajax-url').val();

        var data = {
            post_id : $('.psp-new-expense-row').find('.psp-post-id'),
            date    : $('.psp-new-expense-row').find('.psp-new-exp-date'),
            desc    : $('.psp-new-expense-row').find('.psp-new-desc'),
            cats    : $('.psp-new-expense-row').find('.psp-new-cats'),
            cost    : $('.psp-new-expense-row').find('.psp-new-cost'),
        };

        jQuery.ajax({
     		url: ajaxurl + "?action=psp_add_expense_fe",
     		type: 'post',
     		data: data,
     		success: function( response, data ) {

                $('.psp-new-expense-row').append( data.markup );
                psp_exp_clear_new_row();

     		},
     		error: function(data) {
     			console.log(data);
     		}
 	    });


    });

    function psp_exp_clear_new_row() {
        $('.psp-new-expense-row').find('input').val('').prop( 'checked', false );
        $('.psp-new-expense-row').hide();
        $('.js-psp-add-expense').show();
    }

});
