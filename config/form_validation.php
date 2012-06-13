<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
$config = array(
	'stock' => array(
		array(
			'field' => 'date',
			'label' => 'Date',
			'rules' => 'required|alpha_dash'
		),
		array(
			'field' => 'description[]',
			'label' => 'Description',
			'rules' => 'required|max_length[128]|xss_clean'
		),
		array(
			'field' => 'maker_id',
			'label' => 'Maker',
			'rules' => 'required|integer'
		),
		array(
			'field' => 'quantity[]',
			'label' => 'Quantity delivered',
			'rules' => 'required|integer'
		),
		array(
			'field' => 'wholesale[]',
			'label' => 'Wholesale price',
			'rules' => ''
		),
		array(
			'field' => 'retail[]',
			'label' => 'Retail price',
			'rules' => 'required|numeric'
		)
	),
	'edit_stock' => array(
			array(
				'field' => 'description[]',
				'label' => 'Description',
				'rules' => 'max_length[128]|xss_clean'
			),
			array(
				'field' => 'makers',
				'label' => 'Supplier',
				'rules' => 'required|is_natural_no_zero'
			),
			array(
				'field' => 'category[]',
				'label' => 'Category',
				'rules' => 'alpha'
			),
			array(
				'field' => 'quantity[]',
				'label' => 'Quantity delivered',
				'rules' => 'integer'
			),
			array(
				'field' => 'wholesale[]',
				'label' => 'Wholesale price',
				'rules' => 'numeric'
			),
			array(
				'field' => 'retail[]',
				'label' => 'Retail price',
				'rules' => 'numeric'
			)
		),
		'add_maker' => array(
			array(
				'field' => 'name[]',
				'label' => 'Name',
				'rules' => 'required|max_length[128]|xss_clean'
			)
		),
		'return_stock' => array(
			array(
				'field' => 'date',
				'label' => 'date',
				'rules' => 'required|alpha_dash'
			),
			array (
				'field' => 'quantity[]',
				'label' => 'quantity in stock',
				'rules' => 'required|numeric'
			),
			array (
				'field' => 'returned_quantity[]',
				'label' => 'returned quantity',
				'rules' => 'required|numeric|greater_than[-1]'
			),
			array(
				'field' => 'description[]',
				'label' => 'description',
				'rules' => 'required|max_length[128]|xss_clean'
			),
			array(
				'field' => 'id[]',
				'label' => 'stock id',
				'rules' => 'required|numeric'
			)
				
		),
	'sell_stock' => array(
		array(
			'field' => 'maker_id[]',
			'label' => 'Supplier',
			'rules' => 'required|is_natural_no_zero'
		),
		array(
			'field' => 'quantity[]',
			'label' => 'quantity',
			'rules' => 'required|is_natural_no_zero'
		),
		array(
			'field' => 'price[]',
			'label' => 'price',
			'rules' => 'required|numeric'
		),
		array(
			'field' => 'date',
			'label' => 'date',
			'rules' => 'required|alphanum'
		),
		array(
			'field' => 'stock_id[]',
			'label' => 'stock item',
			'rules' => 'required|is_natural_no_zero'
		)
	),
	'pay_sales' => array(
		array(
			'field' => 'paid_supplier[]',
			'label' => 'paid supplier',
			'rules' => 'required|is_bool'
		),
		array(
			'field' => 'paid_date',
			'label' => 'payment date',
			'rules' => 'required'
		)
	)
);