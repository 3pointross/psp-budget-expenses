<?php
add_action( 'admin_init', 'psp_add_expense_report' );
function psp_add_expense_report() {
    add_submenu_page( 'edit.php?post_type=psp_projects', __('Expenses', 'psp_projects'), __('Expenses', 'psp_projects'), 'manage_options', 'edit.php?post_type=psp_expenses' );
}

add_filter( 'manage_edit-psp-expenses_columns',  'psp_expenses_add_new_columns' );
add_filter( 'manage_edit-psp-expenses_sortable_columns', 'psp_expenses_register_sortable_columns' );
/*
add_filter( 'request', 'hits_column_orderby' );
add_action( 'manage_psp-expenses_custom_column' , 'psp_expenses_custom_columns' );
*/

add_filter( 'manage_psp_expenses_posts_columns', 'psp_expenses_project_header' );
function psp_expenses_project_header( $defaults ) {

    return array(
        'date'      => __('Date', 'psp_projects'),
        'project'   => __('Project','psp_projects'),
        'desc'      => __('Description','psp_projects'),
        'cats'      => __('Categories','psp_projects'),
        'cost'      => __('Cost','psp_projects'),
    );

}

add_action( 'manage_psp_expenses_posts_custom_column', 'psp_expenses_header_content', 10, 2);
function psp_expenses_header_content( $column_name, $post_id ) {

    $project_id = get_post_meta( $post_id, '_psp-expense-project', true );
    $symbol = get_option( 'psp-expense-currency', '$' );


    if( $column_name == 'project' ) {
        echo '<a href="' . esc_url(get_the_permalink($project_id)) . '">' . esc_html( get_the_title($project_id) ) . '</a>';
    }

    if( $column_name == 'desc' ) {
        echo esc_html( get_post_meta( $post_id, '_psp-expense-description', true ) );
    }

    if( $column_name == 'cats' ) {

        $cats   = get_the_terms( $post_id, 'psp_expenses' );

        if( $cats ) {
            $string = '';
            foreach( $cats as $cat ) $string .= '<span class="expense-cat" data-slug="' . $cat->slug . '">' . $cat->name . '</span>, ';
            echo substr( $string, 0, -2 );
        }

    }

    if( $column_name == 'cost' ) {
        echo $symbol . esc_html( number_format(get_post_meta( $post_id, '_psp-expense-cost', true )) );
    }

}

add_action( 'pre_get_posts', 'pspb_custom_pagination_manage_posts' );
function pspb_custom_pagination_manage_posts( $query ) {

  if ( !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != 'psp_expenses' ) {
    return;
  }

  if( isset($_GET['pspb_posts_per_page']) && !empty($_GET['pspb_posts_per_page']) ) {

      update_option( 'edit_psp_expenses_per_page', intval($_GET['pspb_posts_per_page']) );

      $query->set( 'posts_per_page', intval($_GET['pspb_posts_per_page']) );
  }

}

add_action( 'restrict_manage_posts', 'pspb_custom_filters_restrict_manage_posts' );
function pspb_custom_filters_restrict_manage_posts(){

    $type = 'post';

    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ( 'psp_expenses' != $type ) return;

    $cats = get_terms( 'psp_expenses', array( 'hide_empty' => false ) );

    if( !$cats ) return;

    $values = array();

    foreach( $cats as $cat ) {
        $values = array_merge( $values, array( $cat->name => $cat->slug ) );
    } ?>

    <select name="pspb_exp_cat[]" class="sumoselect" multiple="multiple">
        <?php
        $current_v = isset($_GET['pspb_exp_cat'])? $_GET['pspb_exp_cat'] : '';
        foreach ($values as $label => $value) {
            printf
                (
                    '<option value="%s"%s>%s</option>',
                    $value,
                    in_array($value, $current_v) ? ' selected="selected"':'',
                    $label
                );
        } ?>
    </select>

    <input type="text" name="pspb_start_date" value="<?php echo esc_attr(( isset($_GET['pspb_start_date']) ? $_GET['pspb_start_date'] : '' )); ?>" placeholder="From Date" class="psp-expense-datepicker">
    <input type="text" name="pspb_end_date" value="<?php echo esc_attr(( isset($_GET['pspb_end_date']) ? $_GET['pspb_end_date'] : '' )); ?>" placeholder="To Date" class="psp-expense-datepicker">

    <select name="pspb_posts_per_page" class="pspb_posts_per_page">
        <option value="20"><?php esc_html_e('Items Per Page', 'psp_projects'); ?></option>
        <?php if( isset($_GET['pspb_posts_per_page']) && !empty($_GET['pspb_posts_per_page']) ): ?>
            <option value="<?php echo esc_attr($_GET['pspb_posts_per_page']); ?>" selected="selected"><?php echo esc_html($_GET['pspb_posts_per_page']); ?></option>
            <option value="---" disabled>---</option>
        <?php endif;

        $values = array(
            '10'    =>  '10',
            '25'    =>  '25',
            '50'    =>  '50',
            '100'   =>  '100',
            '250'   =>  '250',
            '500'   =>  '500',
            '999'    => '999'
        );

        foreach( $values as $value => $label ): ?>
            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>

    <?php
}

add_action( 'pre_get_posts', 'pspb_budget_posts_filter' );
function pspb_budget_posts_filter( $query ){

    global $pagenow;

    $type = 'post';

    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if( 'psp_expenses' != $type || !is_admin() || $pagenow != 'edit.php' ) return $query;

    /**
     * Is a custom category set?
     */
    if( isset( $_GET['pspb_exp_cat'] ) && $_GET['pspb_exp_cat'] != '' ) {

        $query->query_vars['tax_query'] = array(
                array(
                    'taxonomy'  =>  'psp_expenses',
                    'field'     =>  'slug',
                    'terms'     =>  $_GET['pspb_exp_cat']
                )
        );

    }


    if( isset($_GET['pspb_posts_per_page']) && !empty($_GET['pspb_posts_per_page']) ) {

        // $query->query_vars['posts_per_page'] = intval($_GET['pspb_posts_per_page']);

        $cuser = wp_get_current_user();

        update_user_option( $cuser->ID, 'edit_psp_expenses_per_page', intval($_GET['pspb_posts_per_page']) );

    }

    if( ( isset( $_GET['pspb_start_date'] ) && !empty($_GET['pspb_start_date']) ) || ( isset( $_GET['pspb_end_date'] ) && !empty($_GET['pspb_end_date']) ) ) {

        $date_query = array(
            array(
                'after',
                'before',
                'inclusive' =>  true
            )
        );

        if( isset( $_GET['pspb_start_date'] ) && !empty($_GET['pspb_start_date']) ) {
            $date_query[0]['after'] = $_GET['pspb_start_date'];
        } else {
            unset($date_query[0]['after']);
        }

        if( isset( $_GET['pspb_end_date'] ) && !empty($_GET['pspb_end_date']) ) {

            $date_query[0]['before'] = date( 'F d, Y', strtotime('+1 day', strtotime($_GET['pspb_end_date']) ) );

        } else {
            unset($date_query[0]['before']);
        }

        $query->query_vars['date_query'] = $date_query;

    }

    return $query;

}

add_action( 'in_admin_footer', 'pspb_total_expenses' );
function pspb_total_expenses() {

    global $pagenow;

    $type = 'post';

    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if( 'psp_expenses' != $type || !is_admin() || $pagenow != 'edit.php' ) return;

    global $wp_query;
    $total = 0;

    if( $wp_query->have_posts() ): while( $wp_query->have_posts() ): $wp_query->the_post();
            $total += intval( get_post_meta( get_the_ID(), '_psp-expense-cost', true ) );
    endwhile; endif; ?>

    <table>
        <tr class="pspb-expense-footer">
            <th colspan="4"><strong><?php esc_html_e( 'Total Expenses', 'psp_projects' ); ?></strong></th>
            <td><strong>$<?php echo esc_html( number_format($total) ); ?></strong></td>
        </tr>
    </table>

    <script>
        jQuery(document).ready(function($) {
            var tablefooter = $('.pspb-expense-footer').remove();
            $('.wp-list-table tfoot tr').remove();
            $('.wp-list-table tfoot').append(tablefooter);
        });
    </script>

    <?php

}
