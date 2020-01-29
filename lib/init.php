<?php
$deps = array(
    'budget-settings',
    'assets',
    'views/logic',
    'views/widgets',
    'models/fields',
    'models/categories',
    'models/expenses',
    'controllers/budgets',
    'controllers/expenses',
    'controllers/reports'
);

foreach( $deps as $dep ) include_once( $dep . '.php' );
