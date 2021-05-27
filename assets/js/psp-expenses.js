jQuery(document).ready(function($) {

    if( typeof ajaxurl === 'undefined' ) {
        ajaxurl = jQuery('#psp-ajax-url').val();
    }

    $('#psp-projects').on( 'click', '.psp-js-set-budget', function(e) {

        e.preventDefault();
        $(this).hide();
        $(this).siblings('.psp-budget-table_value').hide();
        $(this).siblings('.psp-set-budget-form').show();

    });

    $('.psp-set-budget-form').submit(function(e) {

        e.preventDefault();

        var parent = $(this).parents('.psp-budget-table-total');

        var data = {
            budget  : $(this).find('#psp-budget').val(),
            post_id : $(this).find('#psp-budget-post-id').val(),
        };

        $.ajax({
            url : ajaxurl + '?action=psp_update_budget_fe',
            type: 'post',
            data: data,
            success: function( response ) {

                console.log(parent);

                $(parent).find('.psp-budget-table_value').html( response.data.markup.budget );
                $(parent).find('.psp-budget-table-remaining').html( response.data.markup.remaining );

                $('.psp-budget-bar .psp-budget-progress span').removeClass().addClass( 'psp-' + response.data.percent ).find('b').html( '%' + response.data.percent );

                $(parent).find('.psp-budget-table_value').show();
                $(parent).find('.psp-js-set-budget').show();
                $(parent).find('.psp-set-budget-form').hide();

            }
        });

    });

    $('.psp-add-expense').click(function(e){

        e.preventDefault();

        $(this).hide();
        $('.psp-add-expense-form').slideDown('slow');
        $('.psp-add-expense-form .required').prop( 'required', true );


    });

    $('.modal-close').click(function(e) {

        e.preventDefault();

        $('.psp-add-expense').show();
        $('.psp-add-expense-form').slideUp('slow');
        $('.psp-add-expense-form .required').prop( 'required', false );


    });

    $('.psp-expense-table').on( 'click', '.psp-expense-cancel', function(e) {
        e.preventDefault();
        psp_expense_table_reset();
    });

    function psp_expense_table_reset() {
        $('.psp-expense-active-edit').remove();
        $('.psp-expense-table tr.psp-editing').removeClass('psp-editing');
    }

    $('.psp-expense-table').on( 'click', '.psp-expense-update', function(e) {

        e.preventDefault();

        var parent      = $(this).parents('tr');

        var formData = {
            expense_id  :   $(parent).data('postid'),
            project_id  :   $('#psp-project-expense-id').val(),
            date        :   $(parent).find('input.psp-expense-date').val(),
            cost        :   $(parent).find('input.psp-expense-cost').val(),
            desc        :   $(parent).find('input.psp-expense-description').val(),
            cats        :   []
        }

        $(parent).find('.psp-expense-category-edit:checked').each(function() {
            formData['cats'].push( $(this).val() );
        });

        $.ajax({
            url : ajaxurl + '?action=psp_update_expense_fe',
            type: 'post',
            data: formData,
            success: function( response ) {

                var edit_row = $('.psp-expense-table tr.psp-editing' );
                $(edit_row).find( '.psp-expense-date-td' ).html( formData.date );
                $(edit_row).find( '.psp-expense-cost-td' ).html( psp_currency_symbol + formData.cost );
                $(edit_row).find( '.psp-expense-description-td' ).html( formData.desc );
                $(edit_row).find( '.psp-expense-categories' ).html( response.categories );
                $(edit_row).data( 'cost', formData.cost );

                $(parent).remove();
                $(edit_row).removeClass( 'psp-editing' );

                psp_recaculate_expense();

            }
        });

    });

    $(document).on('click', '.psp-expense-edit', function(e) {

         e.preventDefault();

        if( $('.psp-expense-table tr.psp-editing').length ) {
            psp_expense_table_reset();
        }

        // Grab the parent and populate

        var parent      = $(this).parents('tr');

        var data = {
            postid  : $(parent).data('postid'),
            date    : $(parent).data('date'),
            desc    : $(parent).data('desc'),
            cost    : $(parent).data('cost'),
        }

        var clone = $(this).parents('tbody').find('.psp-expense-clone-row').clone();
        $( clone ).removeClass( 'psp-hide' ).removeClass('psp-expense-clone-row').addClass('psp-expense-active-edit');

        $(clone).data('postid', data.postid );
        $(clone).find('input.psp-expense-date').val( data.date );
        $(clone).find('input.psp-expense-description').val( data.desc );
        $(clone).find('input.psp-expense-cost').val( data.cost );

        $(parent).find('.expense-cat').each(function() {
            $(clone).find('input[value="' + $(this).data('slug') + '"]').attr('checked',true);
        });

        $(parent).after( clone );

        $('.psp-expense-active-edit .psp-expense-date').datepicker({
            'dateFormat' : 'mm/dd/yy'
        });

        $(parent).addClass( 'psp-editing' );

    });

    $(document).on( 'click', '.psp-expense-delete', function(e) {
         e.preventDefault();

         if( typeof psp_budget_trans === 'undefined' ) {
            confirmation_msg = psp_delete_exp_confirmation_message;
         } else {
            confirmation_msg = psp_budget_trans.psp_delete_confirmation_message;
         }

         var response = confirm( confirmation_msg );

         if( response == false ) {
            return false;
         }

         var parent      = $(this).parents('tr');
         var expense_id  = $(this).parents('tr').data('postid');
         var nonce       = $(this).data('nonce');
         var project_id  = $('#psp-project-expense-id').val();

         $.ajax({
            url : ajaxurl + '?action=psp_delete_expense_fe',
            type: 'post',
            data: {
                 project_id      : project_id,
                 expense_id      : expense_id,
                 nonce           : nonce
            },
            success: function( response ) {

                 $(parent).fadeOut( 'slow' ).remove();
                 psp_recaculate_expense();

            }
         });
    });


    $('.psp-expense-submit').click(function(e) {

        e.preventDefault();

        var formData = {
            'post_id':  $('#psp-project-expense-id').val(),
            'date'  :   $('#psp-expense-date').val(),
            'desc'  :   $('#psp-expense-description').val(),
            'cats'  :   [],
            'cost'  :   $('#psp-expense-cost').val(),
        };

        $('.psp-expense-category:checked').each(function() {
            formData['cats'].push( $(this).val() );
        });

        $.ajax({
            type    :   'POST',
            url     :   ajaxurl + '?action=psp_add_expense_fe',
            data    :   formData,
        success: function( response ) {

            $('.psp-add-expense-form .no-expense-row').hide();

                $('.psp-add-expense-form .success-message').fadeIn( 'slow' );
                $('.psp-add-expense-form').find('.psp-modal-actions').slideUp( 'slow' );
                $('.psp-add-expense-form').find('table').slideUp( 'slow' );

                var new_row = response.data.markup;

                $('.psp-expense-listing tbody').append( new_row );

                psp_recaculate_expense();

                setTimeout(function() {

                    $('.psp-add-expense-form').fadeOut( 'slow', function() {
                        psp_reset_expense_modal();
                    });

                }, 1500 );

                $('.no-expense-row').hide();

            },
            error: function( data ) {

                $('.psp-add-expense-form .error-message').fadeIn( 'slow' );

            }

        });

    });

    function psp_reset_expense_modal() {

        $('.psp-add-expense-form .success-message').hide();
        $('.psp-add-expense-form .error-message').hide();
        $('.psp-add-expense-form').find('.psp-modal-actions').show();
        $('.psp-add-expense-form').find('input[type="text"]').val('');
        $('.psp-add-expense-form').find('input[type="checkbox"]').attr('checked',false);
        $('.psp-add-expense-form table').show();
        $('.psp-add-expense').show();

    }

    function psp_recaculate_expense() {

        console.log('recalculating in function son');

        var cost    = 0;
        var budget  = parseInt($('.pspb-project-budget').data('budget'));

        if( typeof budget == "undefined" ) {
            budget = 0;
        }

        $('.psp-expense-table tr').each(function() {
            if( $(this).data('cost') ) {
                cost += parseInt( $(this).data('cost') );
            }
        });

        $('.psp-date-table-total-cost').html( psp_currency_symbol + cost.toLocaleString() );

        var remaining = budget - cost;

        $('.pspb-project-remaining').html( psp_currency_symbol + remaining.toLocaleString());

        if( $('.psp-budget-table').length ) {
            $('.psp-budget-table-spent').html( psp_currency_symbol + cost.toLocaleString() );
            $('.psp-budget-table-remaining').html( psp_currency_symbol + remaining.toLocaleString());
        }

        if( $('.psp-budget-bar').length ) {
            var new_percentage = Math.floor( cost / budget * 100 );
            $('.psp-budget-bar').find('span').removeClass().addClass( 'psp-' + new_percentage ).data( 'title', new_percentage + '%' ).find('b').html( new_percentage + '%' );
        }

    }

    if (jQuery('.psp-expense-datepicker').length) {
        jQuery(".psp-expense-datepicker").datepicker({
            // 'dateFormat': 'mm/dd/yy'
        });
    }

    if( $('.sumoselect').length ) {
        $('.sumoselect').SumoSelect({
            placeholder: 'All Categories',
            csvDispCount: 3,
        });
    }

    /**
     * Frontend Specific
     */

     $('.expand-expenses').click(function(e) {
         e.preventDefault();
         $('#psp-expense-list').slideDown('fast');
         $(this).fadeOut();
     });

});
