<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
//Page for displaying a payment made to a supplier
?>
<div class="right">
	<address>
		Crown Gallery<br>
		5 Lonsdale St<br>
		Carlisle<br>
		CA1 1BJ<br>
	</address>
</div>
<div class="left">
	<?=$maker?>
	<?=$payment['id']?>
	<?=$payment['date']?>
</div>
<?php
foreach ($payment_details as $payment_detail): ?>
<tr>
	<td><?=$payment_detail['date']?></td>
	<td><?=$payment_detail['description']?></td>
	<td><?=$payment_detail['quantity']?></td>
	<td><?=$payment_detail['wholesale']?></td>
	<td><?=$payment_detail['quantity'] * $payment_detail['wholesale']?></td>
</tr>
<?endforeach;?>