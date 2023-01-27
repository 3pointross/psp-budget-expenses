<?php
add_action('init', 'pspb_init_fields', 999999 );

function pspb_init_fields() {
	if(function_exists("register_field_group"))
	{
		register_field_group(array (
			'id' => 'acf_project-budget',
			'title' => 'Project Budget',
			'fields' => array (
				array (
					'key' => 'field_5876bb9f3ef51',
					'label' => 'Project Budget',
					'name' => 'pspb_project_budget',
					'type' => 'number',
					'instructions' => 'Total project budget, no commas or dollar sign.',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
				), /*
				array (
					'key' => 'field_5876bbbb3ef52',
					'label' => 'Budget Year',
					'name' => 'pspb_budget_year',
					'type' => 'number',
					'instructions' => 'What budget year does this project belong to? e.g. 2017, 2018',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
				), */
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'psp_projects',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		));
	}
}
