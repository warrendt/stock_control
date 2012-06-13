<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
$this->load->helper('form');
$form_attributes = 'id="makers_form"';
echo form_open ('stock/makers/view_makers', $form_attributes);
echo validation_errors();
$i = 0;//Counter for row identification
//View all makers as a table
$actions = array(
	'void' => 'Select action',
	'Stock' => array(
		'stock/stock/check_stock' => 'View stock',
		'stock/stock/return_stock' => 'Return stock',
	),
	'Sales' => array(
		'stock/sales/view_sales' => 'View sales',
		'stock/sales/pay_sales' => 'Pay supplier'
	)
);
echo form_dropdown('actions', $actions, 'void', 'id="action"');
?>
<button type="button" onClick="executeAction()">Go</button>
<table cellpadding="10px" id="makers_table">
<?php foreach($makers as $maker) : ?>
	<tr class="row" id="row_<?=$i?>">
		<td><input type ="radio" name="maker_id" value="<?=$maker['id']?>"></td>
		<td class="name" onclick="edit(this, <?=$i?>, <?=$maker['id']?>)"><?=$maker['name']?></td>
		<td><input type="hidden" name="row[<?=$i?>]" value="<?=$i?>"></td>
	</tr>
<?php 
$i ++;
endforeach; ?>
</table>
<button type="button" onClick="add_row()">Add another</button>
<script type="text/javascript">
	function executeAction()
	{
		var element = document.getElementById('action');
		var action = element.selectedIndex;
		var actionName = element.options[action].value;
		var makerId = document.forms[0].elements['maker_id'];
		for (i = 0; i < makerId.length; i++)
			{
				if (makerId[i].checked)
					{
						maker = makerId[i].value;
					}
			}
		var actionURL = 'index.php/' + actionName + '/' + maker;
		parent.location = actionURL;
	}
	function edit(e, row_id, maker_id)
		{
			//Remove the onclick event so it only fires once
			e.removeAttribute('onclick');
			e.innerHTML = '<input name="' + e.className + '[' + row_id + ']" type="text" value="' + e.innerHTML + '"><input type="hidden" name = "maker_id[' + row_id + ']" value="' + maker_id + '">';
		
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
				document.getElementById('makers_form').appendChild(button);
			}
		}
		function add_row()
		{
			var table = document.getElementById('makers_table');
			var table_rows = table.getElementsByClassName('row');
			var rows = table_rows.length;
			//Insert a new row
			var row = table.insertRow(-1);
			row.setAttribute('class', 'row');
			row.setAttribute('id', 'row_' + rows);
			//Description cell
			var cell = row.insertCell(0);
			cell.setAttribute('class', 'name');
			cell.innerHTML = '<input type="text" name="name[' + rows + ']">';
			cell = row.insertCell(-1);
			cell.innerHTML = '<input type="hidden" name="row[' + rows + ']" value=' + rows + '>';
			cell = row.insertCell(-1);
			cell.innerHTML = '<input type="hidden" name="maker_id[' + rows + ']" value=0>';
			submit_button();
			
		}
</script>