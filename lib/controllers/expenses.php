<?php
add_action( 'wp_ajax_nopriv_psp_add_expense_fe', 'psp_add_expense_fe' );
add_action( 'wp_ajax_psp_add_expense_fe', 'psp_add_expense_fe' );
function psp_add_expense_fe() {

    if( !isset( $_POST[ 'post_id' ] ) ) wp_send_json_error( array( 'success' => false ) );

    $metas = apply_filters( 'psp_add_expense_fe_meta', array(
        array(
            'key'   =>  'psp-expense-project',
            'val'   =>  $_POST[ 'post_id' ],
            'type'  =>  'int',
        ),
        array(
            'key'   =>  'psp-expense-cost',
            'val'   =>  preg_replace("/[^0-9.]/", "", $_POST[ 'cost' ] ),
            'type'  =>  'int'
        ),
        array(
            'key'   =>  'psp-expense-date',
            'val'   =>  $_POST[ 'date' ],
            'type'  =>  'string',
        ),
        array(
            'key'   =>  'psp-expense-description',
            'val'   =>  $_POST[ 'desc' ],
            'type'  =>  'string',
        ),
        array(
            'key'   =>  'psp-expense-categories',
            'val'   =>  $_POST[ 'cats' ],
            'type'  =>  'taxonomy',
        )
    ) );

    $post_date = ( isset( $_POST['date'] ) ? $_POST['date'] : date('m/d/Y') );

    $datetime = DateTime::createFromFormat('m/d/Y', $post_date);
    $date = $datetime->format('Y-m-d H:s:i');

    $args = array(
        'post_title'    =>  get_the_title( $_POST[ 'project_id' ] ) . ' - ' . __( 'expense: ', 'psp_projects' ) . $_POST[ 'cost' ] . __( ' on ', 'psp_projects' ) . ' ' . $date,
        'post_type'     =>  'psp_expenses',
        'post_status'   =>  'publish',
        'post_date'     =>  $date,
    );

    $post_id = wp_insert_post( $args );

    foreach( $metas as $meta ) {

        $val = $meta['val'];

        if( $meta['type'] == 'string' ) $val = sanitize_text_field( $meta['val'] );

        if( $meta['type'] == 'int' ) $val = floatval( $meta['val'] );

        if( $meta['type'] == 'taxonomy' ) {
			wp_set_post_terms( $post_id, $val, 'psp_expenses', false );
        } else {
            update_post_meta( $post_id, '_' . $meta['key'], $val );
        }

    }

    ob_start();

    $symbol = get_option( 'psp-expense-currency', '$' );

    $data = array(
        'postid'       =>  $post_id,
        'date'         =>  get_the_date('m/d/Y', $post_id),
        'desc'         =>  get_post_meta( $post_id, '_psp-expense-description', true ),
        'categories'   =>  get_the_terms( $post_id, 'psp_expenses' ),
        'cost'         =>  get_post_meta( $post_id, '_psp-expense-cost', true )
    ); ?>

    <tr <?php foreach( $data as $key => $val ) echo 'data-' . $key . '="' . esc_attr( $val ) . '" '; ?>>
        <td class="psp-expense-date-td"><?php echo esc_html($data['date']); ?></td>
        <td class="psp-expense-description-td"><?php echo esc_html($data['desc']); ?></td>
        <td class="psp-expense-categories-td">
            <?php
            $string = '';
            $cats   = get_the_terms( $post_id, 'psp_expenses' );

            foreach( $cats as $cat ) $string .= $cat->name . ', ';

            $string = substr( $string, 0, -2 );

            echo esc_html($string); ?>
        </td>
        <td class="psp-expense-cost-td"><?php echo esc_html( $symbol . number_format($data['cost'], 2) ); ?></td>
        <td class="psp-expense-actions-td">
            <?php if( current_user_can( 'edit_psp_project_expenses' ) ): ?>
                <a href="#" class="psp-expense-edit"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit', 'psp_projects' ); ?></a>
            <?php endif; ?>
            <?php if( current_user_can( 'delete_psp_project_expenses' ) ): ?>
                <a href="#" class="psp-expense-delete" data-nonce="<?php echo esc_attr( wp_create_nonce( 'psp_expense_delete_' . $post_id ) ); ?>"><i class="fa fa-trash"></i> <?php esc_html_e( 'Delete', 'psp_projects' ); ?></a>
            <?php endif; ?>
        </td>
    </tr>

    <?php
    $new_markup = ob_get_clean();

    wp_send_json_success( array( 'success' => true, 'markup' => $new_markup, 'cats' => $_POST[ 'cats' ], 'post_id' => $post_id, 'timing' => $timing[ 'percentage_complete' ] ) );

    pspb_add_expense( $_POST[ 'post_id' ], $data['cost'] );

    exit();

}

add_action( 'wp_ajax_nopriv_psp_delete_expense_fe', 'psp_delete_expense_fe' );
add_action( 'wp_ajax_psp_delete_expense_fe', 'psp_delete_expense_fe' );
function psp_delete_expense_fe() {

    if( !isset( $_POST[ 'expense_id' ] ) || !current_user_can( 'delete_psp_project_expenses' ) )
        wp_send_json_error( array( 'success' => false ) );

    if( get_post_type( $_POST[ 'expense_id'] ) != 'psp_expenses' )
        wp_send_json_error( array( 'success' => false ) );


    // Update the total
    pspb_remove_expense( $_POST['project_id'], get_post_meta( $_POST['expense_id'], '_psp-expense-cost', true ) );

    wp_delete_post( $_POST[ 'expense_id' ], true );

    wp_send_json_success();

    exit();

}

add_action( 'wp_ajax_nopriv_psp_update_expense_fe', 'psp_update_expense_fe' );
add_action( 'wp_ajax_psp_update_expense_fe', 'psp_update_expense_fe' );
function psp_update_expense_fe() {

    if( !isset( $_POST[ 'expense_id' ] ) || !current_user_can( 'edit_psp_project_expenses' ) )
            wp_send_json_error( array( 'success' => false ) );

        if( get_post_type( $_POST[ 'expense_id'] ) != 'psp_expenses' )
            wp_send_json_error( array( 'success' => false ) );

        $post_date = ( isset( $_POST['date'] ) ? $_POST['date'] : date('m/d/Y') );

        $datetime       = DateTime::createFromFormat('m/d/Y', $post_date);
        $date           = $datetime->format('Y-m-d H:s:i');
        $previous_cost  = get_post_meta( $_POST['expense_id'], '_psp-expense-cost', true );
        $post_id        = $_POST['expense_id'];

        $metas = apply_filters( 'psp_update_expense_fe_meta', array(
            array(
                'key'   =>  'psp-expense-cost',
                'val'   =>  $_POST[ 'cost' ],
                'type'  =>  'int'
            ),
            array(
                'key'   =>  'psp-expense-date',
                'val'   =>  $date,
                'type'  =>  'date',
            ),
            array(
                'key'   =>  'psp-expense-description',
                'val'   =>  $_POST[ 'desc' ],
                'type'  =>  'string',
            ),
            array(
                'key'   =>  'psp-expense-categories',
                'val'   =>  $_POST[ 'cats' ],
                'type'  =>  'taxonomy',
            )
        ) );

        foreach( $metas as $meta ) {

            $val = $meta['val'];

            if( $meta['type'] == 'string' ) $val = sanitize_text_field( $meta['val'] );

            if( $meta['type'] == 'int' ) $val = floatval( $meta['val'] );

            if( $meta['type'] == 'taxonomy' ) {

                wp_set_post_terms( $post_id, $val, 'psp_expenses', false );

            } elseif( $meta['type'] == 'date' ) {

                wp_update_post( array(
                    'ID'        => $post_id,
                    'post_date' =>  $meta['val']
                ) );

            } else {

                update_post_meta( $post_id, '_' . $meta['key'], $val );

            }

        }

        pspb_remove_expense( $_POST['project_id'], $previous_cost );
        pspb_add_expense( $_POST['project_id'], $_POST['cost'] );

        $cats = get_the_terms( $post_id, 'psp_expenses' );

        foreach( $cats as $cat ) $string .= '<span class="expense-cat" data-slug="' . $cat->slug . '">' . $cat->name . '</span>, ';

        $string = substr( $string, 0, -2 );

        wp_send_json_success( array( 'success' => true, 'categories' => $string ) );

        exit();

}

function pspb_add_expense( $post_id, $cost ) {

    $spent = floatval( get_post_meta( $post_id, '_psp_total_spent', true ) ) + $cost;

    update_post_meta( $post_id, '_psp_total_spend', $spent );

    return true;

}

function pspb_remove_expense( $post_id, $cost ) {

    $spent = floatval( get_post_meta( $post_id, '_psp_total_spent', true ) ) - $cost;

    update_post_meta( $post_id, '_psp_total_spend', $spent );

    return true;

}

function pspb_set_total_expenses( $post_id, $expenses ) {

    update_post_meta( $post_id, '_psp_total_spend', floatval($expenses) );

    return true;

}

add_action( 'admin_init', 'pspb_set_recaulcate_project_expenses' );
function pspb_set_recaulcate_project_expenses() {

    if( !isset($_GET['pspb-recalc-expenses'] ) ) return;

    $args = array(
        'post_type'         =>  'psp_projects',
        'posts_per_page'    =>  -1,
    );

    $projects = new WP_Query($args);

    if($projects->have_posts()) {
        while($projects->have_posts()) {
            $projects->the_post();

            global $post;

            $e_args = array(
                'post_type'         =>  'psp_expenses',
                'posts_per_page'    =>  -1,
                'meta_key'          =>  '_psp-expense-project',
                'meta_value'        =>  $post->ID,
                'post_status'       =>  array( 'publish', 'future' ),
            );

            $expenses   = get_posts($e_args);
            $total      = 0;

            foreach( $expenses as $expense ) $total += floatval( get_post_meta( $expense->ID, '_psp-expense-cost', true ) );

            update_post_meta( $post->ID, '_psp_total_spend', floatval($total) );

        }

    }

}

function pspb_get_project_budget( $post_id = null ) {

    $post_id = $post_id ? $post_id : get_the_ID();

    $budget = array(
        'total'         =>  floatval( get_field( 'pspb_project_budget', $post_id ) ),
        'spent'         =>  0,
        'percent'       =>  0,
        'remaining'     =>  0,
    );

    $args = array(
        'post_type'         =>  'psp_expenses',
        'posts_per_page'    => -1,
        'meta_key'          =>  '_psp-expense-project',
        'meta_value'        =>  $post_id,
        'post_status'       =>  array( 'publish', 'future' )
    );

    $expenses = new WP_Query($args);

    if( !$expenses->have_posts() ) {

        $budget['spent']    = 0;
        $budget['percent']  = 0;

        return $budget;

    }

    while( $expenses->have_posts() ) { $expenses->the_post();

        global $post;

        $cost = floatval(get_post_meta( $post->ID, '_psp-expense-cost', true ));

        if( $cost ) {
            $budget['spent'] += $cost;
        }
    }

    wp_reset_query(); wp_reset_postdata();

    $budget['remaining'] = $budget['total'] - $budget['spent'];

    if( $budget['total'] > 0 && $budget['total'] >= $budget['spent'] ) {
        $budget['percent'] = floor( $budget['spent'] / $budget['total'] * 100 );
    } elseif( $budget['total'] < $budget['spent'] ) {
        $budget['percent'] = 100;
    }

    return $budget;

}

function pspb_get_project_expenses( $post_id = null ) {

    $post_id = $post_id ? $post_id : get_the_ID();

    $args = array(
        'post_type'			=>	'psp_expenses',
        'posts_per_page'	=>	-1,
        'meta_key'          =>  '_psp-expense-project',
        'meta_value'        =>  get_the_ID(),
        'orderby'			=>	'date',
        'order'				=>	'ASC',
        'post_status'       =>  array( 'publish', 'future' )
    );

    $expenses = new WP_Query($args);

    if( !$expenses->have_posts() ) {
        return false;
    }

    return $expenses;

}

add_action( 'init', 'psp_budget_set_permissions' );
function psp_budget_set_permissions() {

    $psp_budget_perms = get_option( 'psp_set_budget_perms', false );

    if( $psp_budget_perms ) {
        return;
    }

    $roles = array(
        'editor',
        'administrator',
        'psp_project_owner',
        'psp_project_manager',
    );

    foreach( $roles as $role_slug ) {

        $role = get_role( $role_slug );

        if( $role ) {
            $role->add_cap('read_psp_budgets');
            $role->add_cap('edit_psp_budgets');
            $role->add_cap('read_psp_expenses');
            $role->add_cap('edit_psp_expenses');
        }

    }

    update_option( 'psp_set_budget_perms', 1 );

}
