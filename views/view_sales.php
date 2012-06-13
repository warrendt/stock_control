<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
$this->load->helper('form');
$i = 0;
?>
<button type="button" onClick="parent.location='index.php/stock/sales/sell_stock'">Add more sales</button>
<table cellpadding="10px" id="sales_table">
	<?php for ($i = 0; $i < count($sales); $i++):?>
	
	<tr>
		<td><?=$sales[$i]['date']?></td>
		<td><?=$sales[$i]['maker']?></td>
		<td><?=$sales[$i]['description']?></td>
		<td><?=$sales[$i]['quantity']?></td>
		<td>£<?=$sales[$i]['price']?></td>
	</tr>
	<?php endfor; ?>
</table>