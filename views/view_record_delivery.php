<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
//Takes data in the form of $form and displays it
$this->load->helper('form');
$js = 'id="makers" onChange="look_up();"';
$id = 'class="description_dropdown" onChange="add_description(this)"';
$form_attributes = array('onSubmit' => 'form_validate()');
$options = array();
?>
<p>Choose the supplier</p>
<?=form_open('stock/add_delivery', $form_attributes)?>
<div id="errors"><?=validation_errors()?></div>
<?=form_dropdown('maker_id', $makers, $maker_id, $js)?>
<p>Date: <?=form_input($form['date'])?></p>
<table cellpadding="10px" style="table-layout:fixed;" width="90%">
	<thead>
		<th style="width: 20%">Description</th>
		<th style="width: 5%">Quantity</th>
		<th style="width: 5%">Wholesale (£)</th>
		<th style="width: 5%">Retail (£)</th>
		<th style="width: 5%">Suggested</th>
	</thead>
	<?php $i = 0;
	while ($i < count($this->input->post('description'))) : ?>
	<tr class="form_row">
		<td class="combo"><?=form_input($form['description'], $this->form_validation->set_value('description[]'))?><?=form_dropdown('description_dropdown', $options, '', $id)?></td>
		<td><?=form_input($form['quantity'], $this->form_validation->set_value('quantity[]'))?><span class="current_quantity" style="padding: 0 5px"></span></td>
		<td><?=form_input($form['wholesale'], $this->form_validation->set_value('wholesale[]'))?></td>
		<td><?=form_input($form['retail'], $this->form_validation->set_value('retail[]'))?></td>
		<td class="suggested"></td>
	</tr>
	<?php 
	$i ++;
	endwhile; ?>
</table>

<button type="button" id="add">More</button>
<input type="submit" value="Add this delivery">
<script type="text/javascript">
	$(document).ready(maker_selected());
	function maker_selected(){
		var maker = $('#makers').val();
		if (!(maker == 0))
			look_up();
	}
	$('#datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	$('#add').click(function(){
		$('.form_row:last').clone(true).insertAfter('.form_row:last');
	});
	function look_up()
	{
		var loading = document.createElement('div');
		loading.id = 'loading';
		document.body.appendChild(loading);
		var maker = $('#makers').val();
		$.post('index.php/stock/json_get_stock_from_maker', {maker: maker}, function(data) {
			data_array = data;
			$('.description_dropdown option').remove();
			var Stock = new Object;
			Stock.description = 'Stock items';
			Stock.category = '';
			Stock.wholesale = 0;
			Stock.retail = 0;
			Stock.quantity = 0;
			data.unshift(Stock);
			for(i=0; i<data.length; i++)
			{
				$('.description_dropdown').append('<option value="' + data[i].id + '">' + data[i].description + '</option>');
			}
		}, "json");
		document.body.removeChild(loading);
	}
	$('input#wholesale').keyup(function(){
		var price = Math.round(100*$(this).val()/0.6)/100;
		$(this).parent('td').siblings('.suggested').html('£' + price);
	});
	function add_description(e){
		
		var parent = e.parentNode.parentNode;
		var w = e.selectedIndex;
		var description = data_array[w]['description'];
		var quantity = data_array[w]['quantity'];
		var wholesale = data_array[w]['wholesale'];
		var retail = data_array[w]['retail'];
		var category = data_array[w]['category'];
		parent.querySelector('#description').value = description;
		parent.querySelector('#wholesale').value = wholesale;
		parent.querySelector('#retail').value = retail;
		parent.querySelector('.current_quantity').innerHTML = quantity;
		parent.querySelector('#category').value = category;
	}
</script>