<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');
//Form to enter a day's sales

$this->load->helper('form');
$form_options	= 'id="sell_stock"';
//Set additional info for dropdown boxes.
$dropdown_makers = 'class="makers" onChange="look_up_stock();"';
$dropdown_stock  = 'class="stock" onChange="get_stock_details();"';
?>
<?= form_open('stock/sales/sell_stock', $form_options) ?>
<?= validation_errors() ?>

<p>Date: <?= form_input($form['date']) ?></p>

<table cellspacing="10px" id="sales_table">
	<thead>
	<th>Maker</th>
	<th>Description</th>
	<th>Quantity</th>
	<th>Price</th>
</thead>
<?php
$i			   = 0;
while ($i < count($maker_ids)):
	?>
	<tr class="form_row">
		<td><?= form_dropdown($form['maker_id'], $maker_options, $maker_ids[$i], 'class="maker_id" id="maker-id-' . $i . '" onChange="look_up_stock(this, ' . $i . ');"') ?></td>
		<td><?= form_dropdown($form['stock_id'], $stock_options[$i], $stock_ids[$i], 'id="stock-id-' . $i . '" onChange="look_up_stock_details(this, ' . $i . ');"') ?></td>
		<td><?= form_input($form['quantity'], $this->form_validation->set_value('quantity[]'), 'id="quantity-id-' . $i . '"') ?></td>
		<td><?= form_input($form['price'], $this->form_validation->set_value('price[]'), 'id="price-' . $i . '"') ?></td>
	</tr>
	<?php
	$i ++;
endwhile;
?>
</table>
<button type="button" onClick="addAnotherRow()">Add another</button>
<script type="text/javascript">
	$(document).ready(submit_button());
	//$(document).ready(maker_selected());
	function maker_selected(){
		var maker = $('.maker_id');
		for(i=0; i< maker.length; i++)
		{
			if (!(maker[i].val == 0))
				look_up_stock(maker[i], i);
		}
	}
	$('#datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	function submit_button()
	{
		var buttons = document.getElementById('submit_button');
		if(buttons == null)
		{
			var button = document.createElement('input');
			button.setAttribute('id', 'submit_button');
			button.setAttribute('type', 'submit');
			button.setAttribute('name', 'submit');
			button.setAttribute('value', 'Record sales');
			button.innerHTML = 'Submit';
			document.getElementById('sell_stock').appendChild(button);
		}
	}
	function look_up_stock(e, j)
	{
		
		var loading = document.createElement('div');
		loading.id = 'loading';
		document.body.appendChild(loading);
		var item = j;
		var maker = e.value;
		$.post('index.php/stock/json_get_current_stock_from_maker', {maker: maker}, function(data) {
			var dropdown_stock = document.getElementById('stock-id-' + j);
			dropdown_stock.options.length = 0;
			var option = document.createElement('option');
			option.text = 'Choose stock';
			option.value = 0;
			dropdown_stock.add(option);
			
			for(i=0; i<data.length; i++)
			{
				option = document.createElement('option');
				option.text = data[i]['description'];
				option.value = data[i]['id'];
				dropdown_stock.add(option);
			}
		}, "json");
		document.body.removeChild(loading);
	}
	function look_up_stock_details(e, j)
	{
		var loading = document.createElement('div');
		loading.id = 'loading';
		document.body.appendChild(loading);
		var stock_id = e.value;
		$.post('index.php/stock/json_get_stock_details', {stock_id: stock_id}, function(data) {
			var price = document.getElementById('price-' + j);
			price.value = data[0]['retail'];
		}, "json");
		document.body.removeChild(loading);
	}
	function addAnotherRow()
	{
		var maker_options = document.getElementById('maker-id-0').innerHTML;
		var table = document.getElementById('sales_table');
		var numRows = table.rows.length-1;
		var row = table.insertRow(-1);
		row.setAttribute('class', 'form_row');
			
		var cell = row.insertCell(0);
		var element = document.createElement('input');
		element.type = 'text';
		element.name = 'price[]';
		element.id = 'price-' + numRows;
		element.size = 4;
		cell.appendChild(element);
			
		cell = row.insertCell(0);
		element = document.createElement('input');
		element.type = 'text';
		element.name = 'quantity[]';
		element.id = 'quantity-' + numRows;
		element.size = 4;
		cell.appendChild(element);
			
		cell = row.insertCell(0);
		element = document.createElement('select');
		element.name = 'stock_id[]';
		element.id = 'stock-id-' + numRows;
		element.onchange = function(){look_up_stock_details(this, numRows)}
		cell.appendChild(element);
			
		cell = row.insertCell(0);
		element = document.createElement('select');
		element.name = 'maker_id[]';
		element.id = 'maker-id-' + numRows;
		element.onchange = function(){look_up_stock(this, numRows)}
		element.innerHTML = maker_options;
		element.selectedIndex = 0;
		cell.appendChild(element);
			
			
	}
</script>