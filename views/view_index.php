<?php
$list = array(
	anchor('stock/add_delivery', 'Record a delivery'),
	anchor('stock/return_stock', 'Return stock'),
	anchor('stock/check_stock/', 'View stock'),
	anchor('stock/sales/sell_stock/', 'Sell stock')
);
echo ul ( $list );