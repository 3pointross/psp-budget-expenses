<?php
$symbol = get_option( 'psp-expense-currency', '$' );

if( current_user_can('read_psp_budgets') ):

    $budget = pspb_get_project_budget( get_the_ID() ); ?>

    <div id="psp-budgeting" class="psp-overview-box cf">

        <h4><?php esc_html_e( 'Budgeting', 'psp_projects' ); ?></h4>

        <table class="psp-table psp-budget-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Project Budget', 'psp_projects' ); ?></th>
                    <th><?php esc_html_e( 'Expenses', 'psp_projects' ); ?></th>
                    <th><?php esc_html_e( 'Remaining', 'psp_projects' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="psp-budget-table-total">
                        <span class="psp-budget-table_value">
                            <?php echo esc_html( $symbol . number_format(intval($budget['total']) )); ?>
                        </span>
                        <?php if( current_user_can('edit_psp_budgets') ): ?>
                            <a class="psp-js-set-budget" href="#"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Budget', 'psp_projects' ); ?></a>
                            <form method="" action="post" class="psp-set-budget-form psp-hide">
                                <input type="hidden" name="psp_budget_post_id" id="psp-budget-post-id" value="<?php esc_attr_e( get_the_ID() ); ?>">
                                <input name="psp-budget" id="psp-budget" type="number" value="<?php echo esc_html( intval($budget['total']) ); ?>">
                                <input type="submit" class="pano-btn pano-btn-primary" value="<?php esc_attr_e( 'Save', 'psp_projects' ); ?>">
                            </form> <!--/.psp-set-budget-form-->
                        <?php endif; ?>
                    </td>
                    <td class="psp-budget-table-spent"><?php echo esc_html( $symbol ); ?><?php echo esc_html( number_format(intval($budget['spent']) )); ?></td>
                    <td class="psp-budget-table-remaining"><?php echo esc_html( $symbol ); ?><?php echo esc_html( number_format(intval($budget['remaining']) )); ?>
                </tr>
            </tbody>
        </table>

        <?php if( current_user_can('read_psp_expenses') ): ?>

            <p><a class="pano-btn psp-btn expand-expenses" href="#"><?php esc_html_e( 'View Expenses', 'psp_projects' ); ?></a></p>

            <div class="psp-hide" id="psp-expense-list">
    		          <?php include( pspb_get_template('project-expenses') ); ?>
    	    </div>

        <?php endif; ?>

    </div>
    <?php
endif; ?>
