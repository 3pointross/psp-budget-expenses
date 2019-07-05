<?php
psp_expenses_edit_post_metabox_callback();

/*
$expense_categories = get_terms( 'psp_expenses', array( 'hide_empty' => false ) );
$cost     = 0;
$expenses = pspb_get_project_expenses();

if( $expenses->have_posts() ): ?>

    <table class="widefat wp-list-table psp-expense-table psp-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Date', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Description', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Categories', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Cost', 'psp_projects' ); ?></th>
                <th style="width:90px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if( $expenses->have_posts() ): while( $expenses->have_posts() ): $expenses->the_post();

                include( pspb_get_template('partials/project-expense-row') );

            endwhile;

                pspb_set_total_expenses( $project_id, $data['cost'] );
                wp_reset_query(); wp_reset_postdata();

            else:  ?>
                <tr class="no-expense-row">
                    <td colspan="4"><?php esc_html_e( 'No expenses recorded at this time.', 'psp_projects' ); ?></td>
                </tr>
            <?php endif;
            if( current_user_can('edit_psp_expenses') ): ?>
                <tr class="psp-new-expense-row">
                    <td>
                        <input type="hidden" class="psp-post-id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <input type="text" class="psp-new-exp-date psp-datepicker" name="psp-new-exp-date" value="">
                    </td>
                    <td>
                        <input type="text" class="psp-new-exp-desc" name="psp-new-exp-desc" value="">
                    </td>
                    <td class="psp-new-expense-cats">
                        <?php
                        $expense_categories = get_terms('psp_expenses',array( 'hide_empty' => false ));
                        if( $expense_categories ):
                            foreach( $expense_categories as $exp_cat ): ?>
                                <label for="<?php echo esc_attr( 'exp-cat-' . $exp_cat->slug ); ?>">
                                    <input type="checkbox" name="psp-new-exp-cat[]" value="<?php echo esc_attr($exp_cat->slug); ?>"> <?php echo esc_html( $exp_cat->name ); ?>
                                </label>
                            <?php endforeach;
                        endif; ?>
                    </td>
                    <td>
                        $ <input type="number" class="psp-new-exp-cost" name="psp-new-exp-cost" value="">
                    </td>
                    <td>
                        <a class="js-add-exp-row pano-btn" href="#"><?php esc_html_e( 'Add', 'psp_projects' ); ?></a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3"><?php esc_html_e( 'Total', 'psp_projects' ); ?></th>
                <th class="psp-date-table-total-cost">$<?php echo esc_html( number_format(intval($cost)) ); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <?php if( current_user_can('edit_psp_expenses') ): ?>
        <div class="psp-budget-actions">
            <p>
                <a href="#" class="pano-btn pano-btn-primary js-psp-add-expense"><?php echo esc_html( 'Add Expense', 'psp_projects' ); ?></a>
            </p>
        </div>
    <?php endif; ?>

<?php endif; ?>
