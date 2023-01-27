<?php
// Register Custom Post Type
function psp_build_expense_cpt() {

	$labels = array(
		'name'                  => _x( 'Expenses', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Expenses', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Expenses', 'text_domain' ),
		'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Expenses', 'text_domain' ),
		'description'           => __( 'Expenses', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( ),
		'taxonomies'            => array( 'psp_expenses' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'menu_position'         => 5,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'psp_expenses', $args );

}
add_action( 'init', 'psp_build_expense_cpt', 0 );

add_action( 'add_meta_boxes', 'psp_expenses_edit_post_metabox' );
function psp_expenses_edit_post_metabox() {
	add_meta_box( 'psp_expenses_meta', __( 'Project Expenses', 'psp_projects' ), 'psp_expenses_edit_post_metabox_callback', 'psp_projects', 'normal', 'default' );
}

function psp_expenses_edit_post_metabox_callback() {

	echo '<input type="hidden" name="psp_delays_post_meta_nonce" id="psp_delays_post_meta_nonce" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

	$args = array(
		'post_type'			=>	'psp_expenses',
		'posts_per_page'	=>	-1,
		'meta_key'          =>  '_psp-expense-project',
		'meta_value'        =>  get_the_ID(),
		'orderby'			=>	'date',
		'order'				=>	'ASC',
		'post_status'		=> 	array( 'publish', 'future' )
	);

    $expense_categories = get_terms('psp_expenses',array( 'hide_empty' => false ));
	$project_id 		= get_the_ID();

    $cost    		= 0;
	$symbol = get_option( 'psp-expense-currency', '$' );
	$expenses       = new WP_Query( $args ); ?>

	<table class="widefat wp-list-table psp-expense-table psp-expense-listing">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'psp_projects' ); ?></th>
				<th><?php esc_html_e( 'Description', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Categories', 'psp_projects' ); ?></th>
				<th><?php esc_html_e( 'Cost', 'psp_projects' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if( $expenses->have_posts() ): while( $expenses->have_posts() ): $expenses->the_post();

				global $post;

				$data = array(
					'postid'       =>  $post->ID,
					'date'         =>  get_the_date('m/d/Y'),
					'desc'         =>  get_post_meta( $post->ID, '_psp-expense-description', true ),
					'cost'         =>  get_post_meta( $post->ID, '_psp-expense-cost', true )
				);

                $cost += floatval($data['cost']); ?>

				<tr <?php foreach( $data as $key => $val ) echo 'data-' . $key . '="' . esc_attr( $val ) . '" '; ?>>
					<td class="psp-expense-date-td"><?php echo esc_html($data['date']); ?></td>
					<td class="psp-expense-description-td"><?php echo esc_html($data['desc']); ?></td>
                    <td class="psp-expense-categories-td">
                        <?php
                        $string = '';
                        $cats   = get_the_terms( $post->ID, 'psp_expenses' );

						if( $cats ) {

							foreach( $cats as $cat ) $string .= '<span class="expense-cat" data-slug="' . $cat->slug . '">' . $cat->name . '</span>, ';

                        	$string = substr( $string, 0, -2 );

                        	echo $string;

						} ?>
                    </td>
					<td class="psp-expense-cost-td"><?php echo esc_html( $symbol . number_format(floatval($data['cost']), 2) ); ?></td>
					<td class="psp-expense-actions-td">
						<?php if( current_user_can( 'edit_psp_project_expenses' ) ): ?>
							<a href="#" class="psp-expense-edit"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit', 'psp_projects' ); ?></a>
						<?php endif; ?>
						<?php if( current_user_can( 'delete_psp_project_expenses' ) ): ?>
							<a href="#" class="psp-expense-delete" data-nonce="<?php echo esc_attr( wp_create_nonce( 'psp_expense_delete_' . $post->ID ) ); ?>"><i class="fa fa-trash"></i> <?php esc_html_e( 'Delete', 'psp_projects' ); ?></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php
			endwhile; wp_reset_query(); wp_reset_postdata();

			pspb_set_total_expenses( $project_id, $cost );

			/**
			 * If the user can edit delays have a hidden row for JS
			 */
			if( current_user_can( 'edit_psp_project_expenses' ) ): ?>
				<tr class="psp-hide psp-expense-clone-row" data-postid="null">
					<td>
						<label for="psp-expense-date"><?php esc_html_e( 'Date', 'psp_projects' ); ?></label>
						<input type="text" class="psp-expense-date" name="psp-expense-date" class="psp-datepicker" value="">
					</td>
                    <td>
                        <label for="psp-expense-description"><?php esc_html_e( 'Description', 'psp_projects' ); ?></label>
                        <input type="text" class="psp-expense-description" name="psp-expense-description">
                    </td>
                    <td id="psp-expense-categories">
                        <label for="psp-expense-categories"><?php esc_html_e( 'Categories', 'psp_projects' ); ?></label>
                        <?php
                        if($expense_categories):
                            foreach( $expense_categories as $cat ):?>
                                <label for="<?php echo esc_attr($cat->term_id); ?>" class="psp-edit-checkbox"><input class="psp-expense-category-edit" name="psp-expense-categories" type="checkbox" value="<?php echo esc_attr($cat->slug); ?>"> <?php echo esc_html($cat->name); ?></label>
                            <?php
                            endforeach;
						else:
							echo __( 'No expense categories set.' ) . '<a href="' . admin_url() . '/options-general.php?page=panorama-license&tab=psp_settings_addons&section=psp_expenses_settings" target="new">' . __( 'You can set them here.', 'psp_projects' ) . '</a>';
                        endif; ?>
                    </td>
					<td>
						<label for="psp-expense-cost"><?php esc_html_e( 'Cost', 'psp_projects' ); ?></label>
						<input type="number" name="psp-expense-cost" class="psp-expense-cost" value="" step="any">
					</td>
					<td class="psp-expense-edit-actions-td"><a class="button button-primary psp-expense-update" href="#"><?php esc_html_e( 'Update', 'psp_projects' ); ?></a> <a href="#" class="psp-expense-cancel"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></td>
				</tr>
			<?php
			endif;

			else: ?>
			<tr class="no-expense-row">
				<td colspan="5"><?php esc_html_e( 'No expenses recorded at this time.', 'psp_projects' ); ?></td>
			</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Budget', 'psp_projects' ); ?></th>
				<td colspan="2"><?php echo esc_html($symbol); ?><span class="pspb-project-budget" data-budget="<?php the_field('pspb_project_budget',$project_id); ?>"><?php echo number_format(floatval(get_field( 'pspb_project_budget', $project_id )), 2); ?></span></td>
			</tr>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Expenses', 'psp_projects' ); ?></th>
				<td colspan="2" class="psp-date-table-total-cost"><?php echo esc_html($symbol); ?><?php echo esc_html( number_format(floatval($cost)) ); ?></td>
			</tr>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Remaining', 'psp_projects' ); ?></th>
				<td colspan="2" class="psp-date-table-remaining"><?php echo esc_html($symbol); ?><span class="pspb-project-remaining"><?php echo number_format(floatval(get_field('pspb_project_budget', $project_id)) - $cost, 2); ?></span></td>
			</tr>
		</tfoot>
	</table>

	<?php wp_reset_query(); wp_reset_postdata(); ?>

	<div class="psp-expense-hide psp-add-expense-form">

		<h3><?php esc_html_e( 'Add Expense', 'psp_projects' ); ?></h3>

		<div class="psp-modal-form" id="psp-expense-modal-form">

			<div class="success-message psp-hide psp-success">
				<?php echo wpautop( __( 'Expense added to project.', 'psp_projects' ) ); ?>
			</div>
			<div class="error-message psp-hide psp-error">
				<?php echo wpautop( __( 'Error adding expense to project. Please check internet connectivity and try again.', 'psp_projects' ) ); ?>
			</div>

			<input type="hidden" id="psp-project-expense-id" value="<?php echo esc_attr( $project_id ); ?>">

			<table class="widefat wp-list-table psp-expense-table">
				<?php

				$meta_fields = apply_filters( 'psp_expenses_fe_modal_fields', array(
					array(
						'id'        =>  'psp-expense-date',
						'label'     =>  __( 'Date', 'psp_projects' ),
						'type'      =>  'text',
						'classes'   =>  'psp-date psp-datepicker required',
					),
                    array(
                        'id'        =>  'psp-expense-description',
                        'label'     =>  __( 'Description', 'psp_projects' ),
                        'type'      =>  'text',
                    ),
                    array(
                        'id'        =>  'psp-expense-categories',
                        'label'     =>  __( 'Categories', 'psp_projects' ),
                        'type'      =>  'taxonomy',
                        'value'     =>  $expense_categories
                    ),
					array(
						'id'        =>  'psp-expense-cost',
						'label'     =>  __( 'Cost', 'psp_projects' ),
						'type'      =>  'number',
						'classes'	=>	'required'
					),
				) );

				$standard_fields = apply_filters( 'psp_delays_fe_modal_standard_fields', array(
					'text',
					'date',
					'email',
					'password',
					'number',
					'hidden',
				) ); ?>
				<thead>
					<tr>
						<?php foreach( $meta_fields as $field ): ?>
							<th><label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['label']); ?></label></th>
						<?php endforeach; ?>
					</tr>
				<tbody>
					<tr>
						<?php foreach( $meta_fields as $field ): ?>
							<td>

								<?php if( $field['type'] == 'textarea' ): ?>
									<textarea name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" <?php if( isset($field['classes']) ) { echo 'class="' . $field['classes'] . '"'; } ?> <?php if( isset( $field['required'] ) ) { echo ' required'; } ?>></textarea>
								<?php endif; ?>

                                <?php
                                if( $field['type'] == 'taxonomy' ):
                                    if(!empty($field['value'])):
                                        foreach( $field['value'] as $cat ): ?>
                                            <label for="tax-<?php echo esc_attr($cat->term_id); ?>"><input type="checkbox" id="tax-<?php echo esc_attr($cat->term_id); ?>" class="psp-expense-category" name="psp-expense-categories" value="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></label>
                                        <?php
                                        endforeach;
									else:
										echo __( 'No expense categories set.' ) . '<a href="' . admin_url() . '/options-general.php?page=panorama-license&tab=psp_settings_addons&section=psp_expenses_settings" target="new">' . __( 'You can set them here.', 'psp_projects' ) . '</a>';
									endif;
                                endif; ?>

								<?php if( in_array( $field['type'],  $standard_fields ) ): ?>
									<input type="<?php echo esc_attr($field['type']); ?>" id="<?php echo esc_attr($field['id']); ?>" name="<?php echo esc_attr($field['id']); ?>" value="" <?php if( isset( $field['classes'] ) ) { echo 'class="' . $field['classes'] . '"'; } if( isset( $field['required'] ) ) { echo ' required'; } ?>>
								<?php endif; ?>

								<?php do_action( 'psp_delays_modal_meta_fields', $field, get_the_ID() ); ?>

							</td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>

			<script>
				jQuery( document ).ready(function() {
					jQuery( '.psp-datepicker' ).datepicker({
						'dateFormat' : 'mm/dd/yy'
					});
				});
			</script>

		</div>

		<div class="psp-modal-actions">
			<p class="pano-modal-add-btn"><input type="submit" class="psp-expense-submit button button-primary" value="<?php echo esc_attr_e( 'Save', 'psp_projects' ); ?>"> <a href="#" class="modal-close modal_close hidemodal"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a></p>
		</div>

	</div>

	<?php
	if( current_user_can('edit_psp_expenses') ): ?>
		<p><a class="button button-primary psp-add-expense" href="#"><?php esc_html_e( 'Add Expense', 'psp_projects' ); ?></a></p>
	<?php
	endif;

}
