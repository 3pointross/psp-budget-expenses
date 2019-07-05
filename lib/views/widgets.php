<?php
// add_action( 'wp_dashboard_setup', 'pspb_dashboard_budget_overview_widget' );
function pspb_dashboard_budget_overview_widget() {

    $title = __( 'Budget Overview', 'psp_projects' ) . ( psp_get_option('psp_budget_year') ? ' for ' . psp_get_option('psp_budget_year') : '' );

    wp_add_dashboard_widget(
            'pspb_budget_overview',
            $title,
            'pspb_budget_overview_dashboard_rendering'
        );
}

function pspb_budget_overview_dashboard_rendering() {

    include( pspb_get_template('budget-overview') );

}

// add_action( 'psp_dashboard_widgets', 'pspb_frontend_budget_overview_widget' );
function pspb_frontend_budget_overview_widget() {
    include( pspb_get_template('frontend-budget-overview' ) );
}

add_action( 'psp_before_quick_overview', 'pspb_project_expense_report' );
function pspb_project_expense_report() {

    if( current_user_can('read_psp_expenses') ) {
        include( pspb_get_template('project-budget') );
    }

}

add_action( 'psp_archive_project_listing_before_timing', 'pspb_budget_progress_bar' );
add_action( 'psp_between_short_progress_and_overview_timing', 'pspb_budget_progress_bar' );
function pspb_budget_progress_bar() {

    if( current_user_can('read_psp_expenses') ) {
        include( pspb_get_template('project-budget-bar') );
    }

}
