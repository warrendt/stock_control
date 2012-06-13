<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
$this->load->helper('form');
//Create a table of data
$form_attributes = 'id="stock_form"';
echo form_open ('stock/check_stock', $form_attributes);
//Set id and javascript event for dropdown box
$js = 'id="makers" onChange="look_up();"';
?>
<div>
	<?=  form_dropdown ( 'makers', $makers, $maker_id, $js)?>
</div>
<?=validation_errors()?>
<table id ="stock_table" cellpadding="10px">
	<thead>
	<th>Description</th>
	<th>Quantity</th>
	<th>Wholesale</th>
	<th>Retail</th>
	<th></th>
	</thead>
	<?php
	$i = 0;//Counter for row identification
	
	//Add the table rows
	foreach ($stock as $value_array): ?>
	<tr class="stock" class="stock_item" id="stock_item_<?=$i?>">
		<td class="description" onclick="edit(this, <?=$i?>)"><?=$value_array['description']?></td>
		<td class="quantity" onclick="edit(this, <?=$i?>)"><?=$value_array['quantity']?></td>
		<td class="wholesale" onclick="edit(this, <?=$i?>)"><?=$value_array['wholesale']?></td>
		<td class="retail" onclick="edit(this, <?=$i?>)"><?=$value_array['retail']?></td>
		<td>
			<input type="hidden" name="id[<?=$i?>]" class="stock_id" value="<?=$value_array['id']?>" />
			<input type="hidden" name="row_id[<?=$i?>]" class="row_id" value="<?=$i?>" />
		</td>
	</tr>
	<?
	$i++;
	endforeach;?>
</table>
<button type="button" onclick="add_row()">Add more stock</button>
<script type="text/javascript">
	function look_up()
		{
			var loading = document.createElement('div');
			loading.id = 'loading';
			document.body.appendChild(loading);
			var maker = $('#makers').val();
			$.post('index.php/stock/json_get_stock_from_maker', {maker: maker}, function(data) {
			$('tr.stock').remove();
			for(i=0; i<data.length; i++)
				{
					$('table').append('<tr class="stock" id="stock_item_' + i + '">');
					$('tr#stock_item_' + i)
						.append('<td class="description" onclick="edit(this, ' + i + ')">' + data[i]['description'])
						.append('<td class="quantity" onclick="edit(this, ' + i + ')">' + data[i]['quantity'])
						.append('<td class="wholesale" onclick="edit(this, ' + i + ')">' + data[i]['wholesale'])
						.append('<td class="retail" onclick="edit(this, ' + i + ')">' + data[i]['retail'])
						.append('<td><input class="stock_id" name="id[' + i + ']" type="hidden" value="' + data[i]['id'] + '"></td>')
						.append('<td><input class="row_id" name="row_id[' + i + ']" type="hidden" value="' + i + '"></td>')
				}
			}, "json");
			document.body.removeChild(loading);
		}
	function edit(e, row_id)
		{
			//Remove the onclick event so it only fires once
			e.removeAttribute('onclick');
			e.innerHTML = '<input name="' + e.className + '[' + row_id + ']" type="text" value="' + e.innerHTML + '">';
			
			
			//See if there are already any buttons on the form
			var buttons = document.getElementById('submit_button');
			if(buttons == null)
			{
				submit_button();
				
			}
		}
		function submit_button()
		{
			var buttons = document.getElementById('submit_button');
			if(buttons == null)
			{
			var button = document.createElement('input');
				button.setAttribute('id', 'submit_button');
				button.setAttribute('type', 'submit');
				button.setAttribute('name', 'submit');
				button.setAttribute('value', 'Submit changes');
				button.innerHTML = 'Submit';
				document.getElementById('stock_form').appendChild(button);
			}
		}
		function add_row()
		{
			var table = document.getElementById('stock_table');
			var table_rows = table.getElementsByClassName('stock_id');
			var rows = table_rows.length;
			//Insert a new row
			var row = table.insertRow(-1);
			row.setAttribute('class', 'stock_id');
			row.setAttribute('id', 'stock_item_' + rows);
			//Description cell
			var cell = row.insertCell(0);
			cell.setAttribute('class', 'description');
			cell.innerHTML = '<input type="text" name="description[' + rows + ']">'
			cell = row.insertCell(1);
			cell.setAttribute('class', 'quantity');
			cell.innerHTML = '<input type="text" name="quantity[' + rows + ']">';
			cell = row.insertCell(2);
			cell.setAttribute('class', 'wholesale');
			cell.innerHTML = '<input type="text" name="wholesale[' + rows + ']">';
			cell = row.insertCell(3);
			cell.setAttribute('class', 'retail');
			cell.innerHTML = '<input type="text" name="retail[' + rows + ']">';
			cell = row.insertCell(4);
			cell.setAttribute('class', 'row_id');
			cell.innerHTML = '<input type="hidden" name="row_id[' + rows + ']" value="' + rows + '">';
			submit_button();
			
		}
</script>