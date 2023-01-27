<?php

add_action( 'psp_head', 'psp_budget_currency_symbol' );
function psp_budget_currency_symbol() { ?>

    <script>
        var psp_currency_symbol = '<?php echo get_option( 'psp-expense-currency', '$' ); ?>';
    </script>
    <?php
}


add_action( 'admin_enqueue_scripts', 'psp_expenses_admin_scripts' );
function psp_expenses_admin_scripts() {

    global $pagenow;

    wp_register_style( 'psp-budget-admin', PSPB_URL . 'assets/css/psp-budget.css' );
    wp_register_style( 'jquery-sumoselect', PSPB_URL . 'assets/css/jquery.sumoselect.css' );
    wp_register_script( 'jquery-sumoselect', PSPB_URL . 'assets/js/jquery.sumoselect.js', array('jquery'), PSPB_VER );

    if(( get_post_type() == 'psp_expenses' || get_post_type() == 'psp_projects' ) || ( $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'psp_expenses' )) {

            wp_enqueue_script('jquery-sumoselect');
            wp_enqueue_style('jquery-sumoselect');

        	wp_enqueue_script(
        			'psp-expenses-admin',
        			PSPB_URL . 'assets/js/psp-expenses.js',
        			array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-sumoselect'),
        			time(),
        			true
        		);

            wp_localize_script(
                'psp-expenses-admin',
                'psp_budget_trans',
                array(
                    'psp_delete_confirmation_message' => __( 'Are you sure you want to delete this expense?', 'psp_projects' )
                )
            );

            wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

            wp_enqueue_style( 'psp-budget-admin' );

    }

}

add_action( 'psp_head', 'pspb_add_front_assets' );
function pspb_add_front_assets() {

    psp_register_style( 'psp-expenses-front', PSPB_URL . 'assets/css/psp-budget.css' );
    psp_register_script( 'psp-expenses-front', PSPB_URL . 'assets/js/psp-expenses.js' );

}

add_action( 'psp_js_variables', 'pspb_translation_messages' );
function pspb_translation_messages() {

    echo 'var psp_delete_exp_confirmation_message = "' . __( 'Are you sure you want to delete this expense?', 'psp_projects' ) . '";';

}
