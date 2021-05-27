<?php
/**
 * Plugin Name: Project Panorama - Budget & Expense Tracking
 * Plugin URI: http://www.projectpanorama.com
 * Description: Set a project budget and track expenses against it
 * Version: 1.5.2
 * Author: SnapOrbital
 * Author URI: http://www.projectpanorama.com/
 * License: GPL2
 * Text Domain: psp_projects
 */

do_action( 'pspb_before_init' );

$defintions = array(
    'PSPB_VER'  =>  '1.5.2',
    'PSPB_PATH' =>  plugin_dir_path( __FILE__ ),
    'PSPB_URL'  =>  plugin_dir_url( __FILE__ ),
    'PSP_BUDGET_STORE_URL'  =>  'https://www.projectpanorama.com/',
    'PSP_BUDGET_ITEM_NAME'  =>  ''
);

foreach( $defintions as $definition => $value ) {
    if( !defined($definition) ) define( $definition, $value );
}


function psp_budget_needs_panorama() { ?>
    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e( 'Project Panorama Project Budget & Expenses requires Project Panorama 1.6 or higher to run', 'psp_projects' ); ?></p>
    </div>
    <?php
}

add_action( 'psp_after_panorama_loaded', 'pspb_init' );

function pspb_init() {

    if( !function_exists('psp_get_option') ) {
        add_action( 'admin_notices', 'psp_budget_needs_panorama' );
        return;
    } else {
        include_once( 'lib/init.php' );
    }

}

add_action( 'plugins_loaded', 'psp_budget_localize_init' );
function psp_budget_localize_init() {
    load_plugin_textdomain( 'psp-projects', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

require 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/3pointross/psp-budget-expenses',
	__FILE__,
	'psp-budgeting'
);


do_action( 'pspb_after_init' );
