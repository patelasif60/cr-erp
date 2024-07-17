<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Caranium configuration parameters
	|--------------------------------------------------------------------------
	|
	| This file contains all the configuration variables that can be set
	| for this Website.
	|
	*/
	'PRODUCT_PACKAGE' =>
	[
		'Product'	=> 'Product',
		'Package'   => 'Package',
	],
	'packaging_materials' => [
        'ETIN' => 'ETIN',
        'product_description' => 'Product Description',
        'material_type_id' => 'Material Type',
        'quantity_per_bundle' => 'Quantity Per Bundle',
        'bundle_qty_per_truck_load' => 'Bundle Qty Per Truck Load',
        'product_temperature' => 'Product Temperature',
        'supplier_product_number' => 'Supplier Product Number',
        'UPC' => 'UPC',
        'weight' => 'Weight',
        'external_length' => 'External Length',
        'external_width' => 'External Width',
        'external_height' => 'External Height',
        'internal_length' => 'Internal Length',
        'internal_width' => 'Internal Width',
        'internal_height' => 'Internal Height',
        'capacity_cubic' => 'Capacity Cubic',
        'cost' => 'Cost',
        'acquisition_cost' => 'Acquisition Cost',
        'new_cost' => 'New Cost',
        'new_cost_date' => 'New Cost Date',
        'warehouses_assigned' => 'Warehouse Assigned',
        'product_assigned' => 'Product Assigned',
        'clients_assigned' => 'Clients Assigned',
        'bluck_price' => 'Bluck Price',
        'status' => 'Status',
    ],
    'quorantine_locations' => [
        1 => ['N-1-1:1:1'],
        2 => ['PA-B-6-1:1:1'],
        3 => ['NV-H-1-1:1:1'],
        4 => ['OK-B-3-10:1:1']
    ],

    'INV_Priority' => [
        1 => 'Oh Shit',
        2 => 'High',
        3 => 'Medium',
        4 => 'Low'
    ]

];