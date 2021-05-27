<?php
if( get_field('pspb_project_budget')):

    $budget = pspb_get_project_budget( get_the_ID() ); ?>
    <div class="simplified-timebar psp-budget-bar">
        <div class="psp-budget-progress">
            <span class="psp-<?php echo esc_attr( $budget['percent'] ); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr( $budget['percent'] . __( '% of budget spent', 'psp_projects' ) ); ?>">
                <b>
                    <?php echo esc_html( $budget['percent'] ); ?>%
                </b>
            </span>
            <i class="psp-progress-label"><?php esc_html_e( 'Budget', 'psp_projects' ); ?></i>
       </div>
    </div>

<?php endif; ?>
