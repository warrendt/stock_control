<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
$this->load->helper('form');
//Create a table of data
$form_attributes = 'id="return_stock"';
echo form_open ('stock/return_stock', $form_attributes);
//Set id and javascript event for dropdown box
$dropdown = 'id="makers" onChange="look_up();"';
?>
<div>
	<?=form_dropdown ( 'makers', $makers, $maker_id, $dropdown)?>
	<p>Date: <?=form_input($form['date'])?></p>
</div>
<?=validation_errors()?>
<table cellspacing="10px">
	<thead>
	<th>Description</th>
	<th>Returning</th>
	</thead>
<?php 
	$i = 0;
	while ($i < count($this->input->post('description'))) : ?>
	<tr class="stock">
		<td><?php echo form_input ($form['description'], $this->form_validation->set_value('description[]')); ?></td>
		<td><?php echo form_input ($form['returned_quantity'], $this->form_validation->set_value('returned_quantity[]')); ?>
		<?php echo form_hidden ('id[]', $this->form_validation->set_value('id[]')); ?>
		<?php echo form_hidden ('quantity[]', $this->form_validation->set_value('quantity[]')); ?>
		</td>
	</tr>
	<?php 
	$i++;
	endwhile; ?>
</table>
<script type="text/javascript">
	$(document).ready(maker_selected());
	function maker_selected(){
		var maker = $('#makers').val();
		if (!(maker == 0))
			look_up();
	}
	$(document).ready(submit_button());
	$('#datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	function look_up()
		{
			var loading = document.createElement('div');
			loading.id = 'loading';
			document.body.appendChild(loading);
			var maker = $('#makers').val();
			$.post('index.php/stock/json_get_current_stock_from_maker', {maker: maker}, function(data) {
			$('tr.stock').remove();
			for(i=0; i<data.length; i++)
				{
					$('table').append('<tr class="stock" id="stock_item_' + i + '">');
					$('tr#stock_item_' + i)
						.append('<td class="description"><input type="text" name="description[]" readonly="readonly" value="' + data[i]['description'] + '" />')
						.append('<td class="returned_stock"><input type="text" name="returned_quantity[]" value="' + data[i]['quantity'] + '" onBlur="check_quantity(this)" /><input class="stock_id" name="id[]" type="hidden" value="' + data[i]['id'] + '"><input class="quantity" name="quantity[]" type="hidden" value="' + data[i]['quantity'] + '"></td>')
				}
			}, "json");
			submit_button();
			document.body.removeChild(loading);
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
				button.setAttribute('value', 'Return stock');
				button.innerHTML = 'Submit';
				document.getElementById('return_stock').appendChild(button);
			}
		}
</script>