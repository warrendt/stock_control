<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
//Form to pay suppliers for sales
$this->load->helper('form');
$form_options	= 'id="pay_sales"';
//Set additional info for dropdown boxes.
$dropdown_makers = 'id="maker_id" onChange="look_up_sales();"';

echo form_open('stock/sales/pay_sales', $form_options);
echo validation_errors();
?>
<div id="makers">
	<?=form_dropdown('maker_id', $maker_options, $maker_id, $dropdown_makers);?>
</div>
<p>Date: <?= form_input($sales['paid_date']) ?></p>
<div id="payall"><p>Pay all sales <input type="checkbox" onclick="payall(this)"></p></div>
<table cellpadding="10px" id="sales_table">
	<thead>
	<th>Date</th>
	<th>Description</th>
	<th>Quantity</th>
	<th>Wholesale</th>
	<th>Paid?</th>
</thead>
<?php
?>
	<tr class="sales_row">
		<td class="description"><?= form_input($sales['description'])?></td>
		<td class="quantity"><?=form_input($sales['quantity'])?></td>
		<td class="wholesale"><?=form_input($sales['wholesale'])?></td>
		<td class="paid_supplier"><?= form_checkbox('paid_supplier', 1, '', 'class="paid" onclick="getCheckedTotal()"') ?></td>
		<?= form_hidden('id')?>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td>Total</td>
		<td><input type="text" name="amount" id="checked_total" value ="0"></td>
		<td></td>
	</tr>
</table>
<p id="total"></p>
<script type="text/javascript">
	$(document).ready(maker_selected());
	function maker_selected(){
		var maker = $('#maker_id').val();
		if (!(maker == 0))
			look_up_sales();
	}
	$(document).ready(submit_button());
	$(document).ready(getCheckedTotal());
	$(document).ready(getTotal());
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
			button.setAttribute('value', 'Pay sales');
			button.innerHTML = 'Submit';
			document.getElementById('pay_sales').appendChild(button);
		}
	}
	function look_up_sales()
	{
		var loading = document.createElement('div');
		loading.id = 'loading';
		document.body.appendChild(loading);
		var maker = document.getElementById('maker_id').value;
		$.post('index.php/stock/sales/json_get_unpaid_from_maker', {maker_id: maker}, function(data) {
			$('tr.sales_row').remove();
			var table = document.getElementById('sales_table');
			var total = 0;
			for(var i = 0; i < data.length; i++)
			{
				var row = table.insertRow(1);
				row.setAttribute('class', 'sales_row');
				
				var cell = row.insertCell(-1);
				cell.innerHTML = '<input type="text" name="date[]" class="date" readonly="readonly" value="' + data[i]['date'] + '">';
				
				cell = row.insertCell(-1);
				cell.innerHTML = '<input type="text" name="description[]" class="description" readonly="readonly" value="' + data[i]['description'] + '">';
				
				cell = row.insertCell(-1);
				cell.innerHTML = data[i]['quantity'];
				cell.innerHTML = '<input type="text" name="quantity[]" class="quantity" readonly="readonly" value="' + data[i]['quantity'] + '">';
				
				
				cell = row.insertCell(-1);
				cell.innerHTML = data[i]['wholesale'];
				cell.innerHTML = '<input type="text" name="wholesale[]" class="wholesale" readonly="readonly" value="' + data[i]['wholesale'] + '">';
				
				
				cell = row.insertCell(-1);
				element = document.createElement('input');
				element.type = 'checkbox';
				element.name = 'paid_supplier[]';
				element.onchange = getCheckedTotal;
				element.setAttribute('class', 'paid_supplier');
				element.setAttribute('value', 1);
				cell.appendChild(element);
				
				element = document.createElement('input');
				element.type = 'hidden';
				element.name = 'id[]';
				element.setAttribute('class', 'sales_id');
				element.value = data[i]['id'];
				cell.appendChild(element);
				
			}
			getTotal();
		}, "json");
		document.body.removeChild(loading);
	}
	function payall(e)
	{
		var paidBoxes = document.getElementsByClassName('paid_supplier');
		for(var i = 0; i < paidBoxes.length; i++)
			{
				paidBoxes[i].checked = (e.checked == 1) ? 1 : 0;
			}
			getCheckedTotal();
	}
	function getTotal()
	{
		var total = 0;
		var rows = document.getElementsByClassName('sales_row');
		for (var i = 0; i < rows.length; i++)
		{
			var quantity = rows[i].getElementsByClassName('quantity');
			var wholesale = rows[i].getElementsByClassName('wholesale');
			total += quantity[0].value * wholesale[0].value;
		}
		document.getElementById('total').innerHTML = 'We owe £' + total;
	}
	function getCheckedTotal()
	{
		var total = 0;
		var rows = document.getElementsByClassName('sales_row');
		for (var i = 0; i < rows.length; i++)
		{
			var paid = rows[i].getElementsByClassName('paid_supplier');
			if (paid[0].checked == 1)
				{
					var quantity = rows[i].getElementsByClassName('quantity');
					var wholesale = rows[i].getElementsByClassName('wholesale');
					total += quantity[0].value * wholesale[0].value;
				}
			
		}
		document.getElementById('checked_total').value = total;
	}
</script>