<?php

$budget = psp_get_option('psp_budget_total');
$symbol = get_option( 'psp-expense-currency', '$' );

if( !isset($budget) ): ?>

    <h2><?php esc_html_e('No budget or year set, please set these options here.', 'psp_projects'); ?></h2>

<?php else:

    wp_enqueue_style( 'psp-expenses-admin' );

    $budget = pspb_get_budget_status();
    $args = array(
        'taxonomy'      =>  'psp_expenses',
        'hide_empty'    =>  false,
        'orderby'       =>  'term_id'
    );
    $expenses       = get_terms($args);
    $exp_budgets    = array();
    ?>

    <h3><?php esc_html_e( 'Budget Allocation', 'psp_projects' ); ?></h3>

    <div class="psp-budget-bar">
        <p class="psp-progress">
            <?php if( $budget['budget-percentage'] > 0 ): ?>
                <span class="psp-<?php echo esc_attr( $budget['budget-percentage'] ); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo esc_attr( $budget['budget-percentage'] . __( '% of budget spent', 'psp_projects' ) ); ?>">
                    <b>%<?php echo esc_html( $budget['budget-percentage'] ); ?></b>
                </span>
            <?php endif; ?>
        </p>
    </div>

    <table class="psp-table pspb-budget-widget wp-list-table widefat">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Total Budget', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Allocated', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Remaining', 'psp_projects' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>$<?php echo esc_html( number_format( intval($budget['total'] )) ); ?></td>
                <td>$<?php echo esc_html( number_format( intval($budget['allocated'] )) ); ?></td>
                <td>$<?php echo esc_html( number_format( intval($budget['remaining'] )) ); ?></td>
            </tr>
        </tbody>
    </table>

    <h3><?php esc_html_e( 'Expense Allocation', 'psp_projects' ); ?></h3>

    <div class="psp-budget-bar">
        <p class="psp-progress">
            <?php if( $budget['expense-percentage'] > 0 ): ?>
            <span class="psp-<?php echo esc_attr( $budget['expense-percentage'] ); ?>">
                <b>%<?php echo esc_html( $budget['expense-percentage'] ); ?></b>
            </span>
            <?php endif; ?>
        </p>
    </div>

    <table class="psp-table pspb-budget-widget wp-list-table widefat">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Budget Allocated', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Expenses', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Remaining', 'psp_projects' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo esc_html( $symbol ); ?><?php echo esc_html( number_format( intval($budget['allocated'] )) ); ?></td>
                <td><?php echo esc_html( $symbol ); ?><?php echo esc_html( number_format( intval($budget['spent'] )) ); ?></td>
                <td><?php echo esc_html( $symbol ); ?><?php echo esc_html( number_format( intval($budget['expense-remaining'] )) ); ?></td>
            </tr>
        </tbody>
    </table>

    <h3><?php esc_html_e( 'Budget Categories', 'psp_projects' ); ?></h3>

    <table class="psp-table pspb-budget-widget wp-list-table widefat">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Category', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Budgeted', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Expenses', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Remaining', 'psp_projects' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach( $expenses as $expense ):
            $exp_budget = pspb_get_category_budget_status( $year, $expense->slug );
            $exp_budget[$expense->term_id] = $exp_budget;
            ?>
            <tr>
                <td><?php echo esc_html( $expense->name ); ?></td>
                <td>$<?php echo esc_html( number_format(intval(get_term_meta( $expense->term_id, '_expense-option-budget', true ))) ); ?></td>
                <td>$<?php echo esc_html( number_format(intval($exp_budget['spent']))); ?></td>
                <td>$<?php echo esc_html( number_format(intval($exp_budget['remaining']))); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif;
