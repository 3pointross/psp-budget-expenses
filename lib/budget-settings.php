<?php
add_filter( 'psp_settings_sections_addons', 'psp_budget_settings_section' );
add_filter( 'psp_settings_addons', 'psp_budget_settings' );

add_action( 'psp_settings_tab_bottom_psp_settings_addons_psp_expenses_settings', 'psp_expenses_settings_addon_scripts' );
add_action( 'psp_settings_tab_top_psp_settings_addons_psp_expenses_settings', 'psp_expenses_addon_head' );

add_filter( 'psp_settings_section_psp_expenses_settings_form', '__return_false' );
add_action( 'psp_settings_section_psp_expenses_settings', 'psp_expenses_submenu_page' );


function psp_budget_settings_section( $sections ) {

    // $sections[ 'psp_budget_settings' ] = __( 'Budget', 'psp-projects' );
    $sections[ 'psp_expenses_settings' ] = __( 'Expenses', 'psp-projects' );

    return $sections;

}

function psp_expenses_submenu_page() { ?>

    <div class="wrap no_move">
        <?php
        global $psp_data;

        if( isset( $psp_data[ 'admin_message' ] ) ): ?>

            <br>

            <div id="message" class="updated">
                <p><?php echo $psp_data[ 'admin_message' ]; ?></p>
            </div>

        <?php endif; ?>

        <form id="post" method="post" name="post">
            <div class="metabox-holder" id="poststuff">

                <?php
                $args = array(
                    'taxonomy'      =>  'psp_expenses',
                    'hide_empty'    =>  false,
                    'orderby'       =>  'term_id'
                );

                $expenses       = get_terms($args);
                $psp_options    = get_option('psp_settings'); ?>

                <div id="post-body">
                    <div id="post-body-content">

                        <h3><?php esc_html_e( 'Expense Categories', 'psp_projects' ); ?></h3>

                        <table class="widefat wp-list-table psp-expense-option-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Expense Category', 'psp_projects' ); ?></th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($expenses as $expense): ?>
                                    <tr class="psp-expense-option-row">
                                        <td scope="row">
                                            <input type="text" name="psp-expense-option-name[<?php echo esc_attr($expense->term_id); ?>]" value="<?php echo esc_attr($expense->name); ?>">
                                        </td>
                                        <td style="text-align: center;" class="psp-status-remove-option-cell">
                                            <input type="checkbox" name="psp-expense-delete[<?php echo esc_attr($expense->term_id); ?>]" value="Yes" style="display:none">
                                            <input type="button" name="psp-delete" class="button button-secondary psp-expense-delete-option-row" value="<?php echo esc_attr( 'Remove' ); ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><a class="button button-primary psp-expense-add-row" href="#"><?php esc_html_e( 'Add Expense Category', 'psp_projects' ); ?></a></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>

                <div class="psp-expense-hide">
                    <table>
                        <tbody class="psp-clone">
                            <tr class="psp-expense-option-row">
                                <td scope="row">
                                    <input type="text" name="psp-expense-new-option-name[]" value="">
                                </td>
                                <td style="text-align: center;" class="psp-status-remove-option-cell">
                                    <input type="button" name="psp-delete" class="button button-secondary psp-expense-delete-option-row" value="<?php echo esc_attr( 'Remove' ); ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="HTTP_REFERER" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
                <input type="hidden" name="acf_nonce" value="<?php echo wp_create_nonce( 'psp_expenses' ); ?>" />

                <p><input type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'psp_status'); ?>" /> <a class="button" href="&pspb-recalc-expenses=true"><?php esc_html_e('Recalculate Expenses', 'psp_projects'); ?></a></p>


                </div>
            </div>

        </div>
    </form>

</div>

<?php
}

function psp_budget_settings( $settings ) {

    /*
    $psp_budget_settings['psp_budget_settings'] = array(
        'psp_budget_title'  =>  array(
            'id'    =>  'psp_budget_title',
            'name'  =>  '<h2>' . __('Budget Settings', 'psp_projects') . '</h2>',
            'type'  =>  'html'
        ),
        'psp_budget_total'  =>  array(
            'id'    =>  'psp_budget_total',
            'name'  =>  __( 'Total yearly budget', 'psp_projects' ),
            'type'  =>  'text',
            'desc'  =>  '<i>No commas, eg 100000</i>'
        ),
        'psp_budget_year'  =>  array(
            'id'    =>  'psp_budget_year',
            'name'  =>  __( 'Year to budget', 'psp_projects' ),
            'type'  =>  'text',
            'desc'   =>  '<i>e.g. 2017</i>'
        ),
    ); */

    $psp_budget_settings['psp_budget_settings'] = array(
            'psp_budget_license_key' => array(
            'id'    => 'psp_budget_license_key',
            'name'  => __( 'License Key', 'psp-front-edit' ),
            'desc'  => __( 'Enter your license key, save and then activate.', 'psp-front-edit' ),
            'type'  => 'text',
        ),
        'psp_budget_activate_license' => array(
            'id'    =>  'psp_budget_activate_license',
            'name'  =>  __( 'Activate License', 'psp-front-edit' ),
            'type'  =>  'fe_license_key'
        ),
    );

    return array_merge( $settings, $psp_budget_settings );

}

function psp_expenses_settings_addon_scripts() {

    wp_enqueue_style( 'psp-status-admin', PSPB_URL . 'assets/css/psp-budget.css' );
    wp_enqueue_script( 'psp-status-admin', PSPB_URL . 'assets/js/psp-expenses-admin.js' );

}

function psp_expenses_addon_head() {

    if ( isset( $_POST['acf_nonce'] ) && wp_verify_nonce( $_POST['acf_nonce'], 'psp_expenses') ) {

        // The functions built into ACF for this didn't work quite right
        psp_expenses_save_data();

        global $psp_data;

        $psp_data['admin_message'] = __( 'Expense Categories Updated', 'psp_projects' );

    }

}

function psp_expenses_save_data() {

    // Load Fields from POST
    if ( !isset($_POST['psp-expense-option-name'])  && !isset($_POST[ 'psp-expense-new-option-name' ]) ) {
        return false;
    }

    $names      = ( isset( $_POST[ 'psp-expense-option-name' ] ) ? $_POST[ 'psp-expense-option-name' ] : null );
    $budgets    = ( isset( $_POST[ 'psp-expense-option-budget'] ) ? $_POST[ 'psp-expense-option-budget' ] : null );
    $new_names  = ( isset( $_POST[ 'psp-expense-new-option-name' ] ) ? $_POST[ 'psp-expense-new-option-name' ] : null );
    $delete     = ( isset( $_POST[ 'psp-expense-delete' ] ) ? $_POST[ 'psp-expense-delete' ] : null );
    $license_key = ( isset( $_POST['psp_budget_license_key'] ) ? $_POST['psp_budget_license_key'] : null );

    if( is_array($names) ) {

        foreach( $names as $key => $val ) {

            if( $key == 0 ) wp_insert_term( $val, 'psp_expenses' );
            else wp_update_term( $key, 'psp_expenses', array( 'name' => $val ) );

        }

    }

    if( is_array($new_names) ) {
        foreach( $new_names as $key => $val ) wp_insert_term( $val, 'psp_expenses', $args = array( 'slug' => sanitize_title_with_dashes( $new_slugs[ $key ] ) ) );
    }

    if( is_array($delete) ) {
        foreach( $delete as $key => $val ) wp_delete_term( $key, 'psp_expenses' );
    }

    if( is_array($budgets) ) {
        foreach( $budgets as $key => $val ) update_term_meta( $key, '_expense-option-budget', $val );
    }

    if( $license_key ) {
        update_option( 'psp_budget_license_key', $license_key );
    } else {
        delete_option( 'psp_budget_license_key' );
    }

    global $psp_options;

}

add_action( 'init', 'psp_expenses_permissions' );
function psp_expenses_permissions() {

    $roles = array(
        'administrator',
        'editor',
        'psp_project_owner',
        'psp_project_creator',
        'psp_project_manager'
    );

    $caps = array(
        'edit_psp_project_expenses',
        'delete_psp_project_expenses',
        'publish_psp_project_expenses'
    );

    foreach( $roles as $role_slug ) {

        $role = get_role( $role_slug );

        if( $role )
            foreach( $caps as $cap ) $role->add_cap( $cap );

    }

}


add_action( 'admin_init', 'psp_budget_activate_license', 0 );
function psp_budget_activate_license() {

    if( isset( $_POST[ 'psp_budget_license_activate' ] ) ) {
        // run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
        $license = get_option( 'psp_budget_license_key' );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_BUDGET_ITEM_NAME ), // the name of our product in EDD
		    'url'   => home_url()
        );

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_BUDGET_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"

		update_option( 'psp_budget_license_key_status', $license_data->license );

    }

}

function psp_budget_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST[ 'psp_budget_license_deactivate' ] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_budget_license_key' );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_BUDGET_ITEM_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_BUDGET_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'psp_budget_license_key_status' );

	}
}
add_action('admin_init', 'psp_budget_deactivate_license');

function psp_budget_check_activation_response() {

    $license = get_option( 'psp_budget_license_key' );

    // data to send in our API request
    $api_params = array(
        'edd_action'=> 'activate_license',
        'license' 	=> $license,
        'item_name' => urlencode( PSP_FE_ITEM_NAME ), // the name of our product in EDD
        'url'   => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, PSP_BUDGET_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    return $response;

}
