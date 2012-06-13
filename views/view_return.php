<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
?>
<div>
	<p><?=$return['maker'][0]['name']?></p>
	<p><?=$return['date']?></p>
</div>
<table cellpadding="10px">
	<thead>
		<th style="width: 20%">Description</th>
		<th style="width: 5%">Quantity</th>
	</thead>
	<?php
	foreach ($returned_stock as $return_item) : ?>
	
	<tr>
		<td><?=$return_item['description']?></td>
		<td><?=$return_item['stock_quantity']?></td>
	</tr>
	
	<?php	endforeach;?>
</table>
<form>
<input class="no-print" type="button" value="Print this return" onClick="window.print()">
</form>