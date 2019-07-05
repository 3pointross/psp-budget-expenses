<?php
function pspb_get_category_budget_status( $year = NULL, $term_slug = NULL ) {

    $year = ( $year != NULL ? $year : psp_get_option( 'psp_budget_year' ) );

    if( !$year || $term_slug == NULL ) return FALSE;

    $args = array(
        'post_type'         =>  'psp_expenses',
        'posts_per_page'    =>  -1,
        'year'              =>  $year,
        'tax_query' =>  array(
            array(
                'taxonomy'  =>  'psp_expenses',
                'field'     =>  'slug',
                'terms'     =>  $term_slug,
            )
        )
    );

    $spent      = 0;
    $budget     = array();
    $projects   = new WP_Query($args);

    if( $projects->have_posts() ): while( $projects->have_posts() ): $projects->the_post();

        $spent += get_post_meta( get_the_ID(), '_psp-expense-cost', true );

    endwhile; endif;

    $term = get_term_by( 'slug', $term_slug, 'psp_expenses' );

    $budget['spent'] = $spent;
    $budget['allocated'] = get_term_meta( $term->term_id, '_expense-option-budget', true );
    $budget['remaining'] = $budget['allocated'] - $budget['spent'];

    return $budget;

}

function pspb_get_budget_status( $year = NULL ) {

    $year = ( $year != NULL ? $year : psp_get_option( 'psp_budget_year' ) );

    if( !$year ) return FALSE;

    $budget = array(
        'total'     => psp_get_option( 'psp_budget_total' ),
        'year'      => $year,
        'allocated' =>  '',
        'spent'     =>  '',
        'remaining' =>  '',
    );

    $args = array(
        'post_type'         =>  'psp_projects',
        'posts_per_page'    =>  -1,
        'post_status'       =>  'publish',
        'meta_query'        =>  array(
            array(
                'key'       =>  'pspb_budget_year',
                'value'     =>  $year,
            ),
        )
    );

    $allocated  = 0;
    $spent      = 0;

    $projects   = new WP_Query($args);

    if( $projects->have_posts() ): while( $projects->have_posts() ): $projects->the_post();

        $budgeted = str_replace( ',', '', get_field('pspb_project_budget') );

        if( is_numeric($budgeted) ) $allocated += $budgeted;

        $expenses = get_post_meta( get_the_ID(), '_psp_total_spend', true );
        if( $expenses ) $spent += $expenses;

    endwhile; endif;

    $budget['allocated']    = $allocated;
    $budget['spent']        = $spent;
    $budget['remaining']    = $budget['total'] - $allocated;

    if( empty($budget['total']) || $budget['total'] == 0 || $budget['allocated'] == 0 ) {
        $budget['budget-percentage'] = 0;
    } elseif( $budget['allocated'] > $budget['total'] ) {
        $budget['budget-percentage'] = 100;
    } else {
        $budget['budget-percentage'] = floor( $budget['allocated'] / $budget['total'] * 100 );
    }

    if( empty($budget['total']) || $budget['total'] == 0 || $budget['allocated'] == 0 || $budget['spent'] == 0) {
        $budget['expense-percentage'] = 0;
    } elseif( $budget['spent'] > $budget['allocated'] ) {
        $budget['expense-percentage'] = 100;
    } else {
        $budget['expense-percentage'] = floor($budget['spent'] / $budget['allocated'] * 100);
    }

    $budget['expense-remaining'] = $budget['allocated'] - $budget['spent'];

    return $budget;

}

add_action( 'wp_ajax_psp_update_budget_fe', 'psp_update_budget_fe' );
function psp_update_budget_fe() {

    if( !current_user_can('edit_psp_budgets') ) {
        wp_send_json_error( array( 'success' => false, 'message' => __( 'You do not have permission to edit budgets', 'psp_projects' ) ) );
    }

    $post_id = $_POST['post_id'];
    $budget  = intval($_POST['budget']);

    update_field( 'pspb_project_budget', $budget, $post_id );

    $updated_figures = pspb_get_project_budget( $post_id );

    wp_send_json_success( array('success' => true,
        'markup' => array(
            'budget'    =>  '$' . esc_html( number_format(intval($updated_figures['total']) ) ),
            'remaining' =>  '$' . esc_html( number_format(intval($updated_figures['remaining']) ) ),
        ),
        'percent'   =>  $updated_figures['percent']
    ) );

}
