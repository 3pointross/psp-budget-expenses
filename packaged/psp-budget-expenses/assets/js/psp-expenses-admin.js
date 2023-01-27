jQuery( document ).ready(function($) {

    $( '.psp-expense-add-row' ).click(function(e) {

        e.preventDefault();

        var new_row = $( '.psp-clone' ).html();

        $( '.psp-expense-option-table tbody' ).append( new_row );

    });

    $( '.psp-expense-option-table' ).on( 'click', '.psp-expense-delete-option-row', function(e) {

        e.preventDefault();

        confirmation = confirm( 'Do you really want to remove this row?' );

        if( confirmation == true ) {

            $(this).siblings( 'input' ).attr( 'checked', 'true' );

            var element = this;

            $(this).parents( 'tr' ).fadeOut( 'slow', function() {

                if( !$( element ).siblings( 'input' ).length ) {
                    $( element ).parents( 'tr' ).remove();
                }

            });

        }

        if( confirmation == false ) {

            return false;

        }

    });

    if (jQuery('.psp-expense-datepicker').length) {
        jQuery(".psp-expense-datepicker").datepicker();
    }


});
