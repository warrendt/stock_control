<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Stock controller
 * 
 * Contains methods to handle adding, editing and deletion of stock from the inventory.
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Stock extends MX_Controller
{

	private $_data;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array ('html', 'url'));
		$this->load->library('auth/ion_auth');
		if ( ! $this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
	}

	/**
	 * The default method if none is called 
	 */
	public function index()
	{
		$this->_data['title'] = 'Stock control';
		$this->_data['content'] = $this->load->view('view_index', '', TRUE);
		$this->load->view('templates/index', $this->_data);
	}

	/**
	 * Method to edit a stock item 
	 * Create a form, validate it and process the data
	 */
	public function check_stock($maker_id = 0)
	{
		//Load the models needed
		$this->load->model('model_makers');
		$this->load->model('model_stock');

		$this->load->library('form_validation');

		$maker_id = $this->input->post('makers') ? $this->input->post('makers') : $maker_id;
		$id	   = $this->input->post('id') ? $this->input->post('id') : array ();

		if ($this->form_validation->run('edit_stock') == FALSE)
		{
			//Populate the form
			//
			//Get maker names
			$makers = $this->model_makers->get_makers();
			//Add a row at the start of the array
			$this->_data['makers'][0] = 'Choose a supplier';
			foreach ($makers as $maker)
			{
				$this->_data['makers'][$maker['id']] = $maker['name'];
			}

			//Set maker_id value for the form
			$this->_data['maker_id'] = $maker_id;

			//Get the stock for the specified maker
			$this->_data['stock'] = $this->model_stock->get_stock(array ('maker_id'=>$maker_id));

			//Load the form
			$this->_data['title'] = 'View stock';
			$this->_data['content'] = $this->load->view('view_check_stock', $this->_data, TRUE); //Get layout for stock view
			$this->load->view('templates/index', $this->_data); //Load the main view
		}
		else
		{
			$row_ids = $this->input->post('row_id');
			$data	= array ();
			//Get list of fields from the db
			$fields = $this->model_stock->get_fields();
			//First field will be the id, so unset it.
			unset($fields[0]);
			//Build the update query
			foreach ($row_ids as $row_id)
			{
				$row_data = array ();
				$found_fields = 0;
				if (array_key_exists($row_id, $id))//Are we editing a stock entry, or creating a new entry?
				{

					$row_data['id'] = $id[$row_id];
					foreach ($fields as $field)
					{
						if ($this->input->post($field))
						{
							$post = $this->input->post($field);
							if (array_key_exists($row_id, $post))
							{
								$found_fields ++;
								$row_data[$field] = $post[$row_id];
							}
						}
					}
					if (count($row_data) > 1)
					{
						$update_data[] = $row_data;
					}
				}
				else //We're creating a new stock entry
				{
					foreach ($fields as $field)
					{
						$post_field = $this->input->post($field);
						if ($post_field[$row_id])
						{
							$insert_data[$field]	 = $post_field[$row_id];
						}
					}
					$insert_data['maker_id'] = $maker_id;
					$this->model_stock->add_stock($insert_data);
				}
			}
			if (isset($update_data))
			{
				$this->model_stock->edit_stock($update_data);
			}
			redirect('stock/check_stock/' . $this->input->post('makers'));
		}
	}

	/**
	 * Method to record returns to a supplier 
	 * Create a form, validate it and process the data
	 */
	public function return_stock($maker_id = 0)
	{
		//List all the stock for a supplier and show box to indicate how many are being returned.
		//Initially list the supplier, use my json method to get the stock and display it.
		//Javascrpt function to add submit button and text boxes to end of each row.
		//Submitting do data validatino on quantity only, reduce stock quantity accordingly.
		//Use the edit_stock_quantity method in model_stock
		//Load the models needed
		$this->_data['title'] = 'Record returns';

		$this->load->model('model_makers');
		$this->load->model('model_stock');

		$this->load->library('form_validation');
		$maker_id = $this->input->post('makers') ? $this->input->post('makers') : $maker_id;
		if ($this->form_validation->run('return_stock') === FALSE)
		{
			//Display the form
			//Get list of makers for dropdown box
			$makers = $this->model_makers->get_makers();
			//Generate options for box
			//Box will be ('maker_id', $makers, $maker_id)
			foreach ($makers as $maker)
			{
				$this->_data['makers'][$maker['id']] = $maker['name'];
			}
			//Set the first value. There is no maker 0 so I can use this
			$this->_data['makers'][0] = 'Choose a supplier';
			$this->_data['maker_id'] = $maker_id;
			//Get the stock for the specified maker, if specified
			//$this->_data['stock'] = $this->model_stock->get_stock_from_maker($maker_id);
			$this->_data['form'] = array (
				'date'=>array (
					'name'	   =>'date',
					'type'	   =>'text',
					'id'		 =>'datepicker',
					'value'	  =>$this->form_validation->set_value('date')
				),
				'description'=>array (
					'name'			 =>'description[]',
					'type'			 =>'text',
					'readonly'		 =>'readonly'
				),
				'returned_quantity'=>array (
					'name'	=>'returned_quantity[]',
					'type'	=>'text'
				),
				'quantity'=>array (
					'name'=>'quantity[]'
				)
			);
			//Get the view content
			$this->_data['content'] = $this->load->view('stock/view_return_stock', $this->_data, TRUE);
		}
		else
		{
			//Process the data
			$this->load->model('model_returns');
			//Add the record to the returns table
			//Will need post makerid, date.
			$return_data = array (
				'maker_id'		  =>$maker_id,
				'date'			  =>$this->input->post('date')
			);
			//Add the return and get the id of the record
			$return_id		   = $this->model_returns->add_return($return_data);
			//Add the items to the returned stock table
			//Uses post data id
			//Need to create an array where returned quantity > 0
			$returned_quantities = $this->input->post('returned_quantity');
			$stock_quantities	= $this->input->post('quantity');
			$ids				 = $this->input->post('id');
			//Build the data for both adjusting the stock levels and adding to the joining table
			$stock_data		  = array ();
			foreach ($returned_quantities as $key=>$returned_quantity)
			{
				if ($stock_quantities[$key] >= $returned_quantity && $returned_quantity > 0)
				//It's a valid set of quantities so proceed
				{
					$new_quantity = $stock_quantities[$key] - $returned_quantity;
					$stock_data[] = array (
						'quantity'		  =>$new_quantity,
						'id'				=>$ids[$key]
					);
					$returned_stock_data = array (
						'return_id'	 =>$return_id,
						'stock_id'	  =>$ids[$key],
						'stock_quantity'=>$returned_quantity
					);
					$this->model_returns->add_returned_stock($returned_stock_data);
				}
			}
			//Add the returned_stock
			//Adjust the stock quantities.
			$this->model_stock->edit_stock($stock_data);
			//Show the return with an option to print it off.
			//Using the maker_id
			//Get details of the maker
			//Use the return_id
			//Get all the stock_id and quantity returned on that return_id
			$this->_data['return'] = array (
				'date' =>$this->input->post('date'),
				'maker'=>$this->model_makers->get_makers($maker_id)
			);
			$this->_data['returned_stock'] = $this->model_returns->get_returned_stock($return_id);
			//Display the sheet
			$this->_data['content'] = $this->load->view('view_return', $this->_data, TRUE);
		}

		$this->load->view('templates/index', $this->_data);
	}

	/**
	 * Method to add stock to the database.
	 * Create a form, validate it and process the data
	 */
	public function add_delivery()
	{
		$this->_data['title'] = 'Deliveries';
		$this->load->library('form_validation');
		if ($this->form_validation->run('stock') == FALSE)
		{
			//Display the form
			//Get the list of makers for the dropdown
			$makers = Modules::run('stock/makers/get_makers');
			foreach ($makers as $maker)
			{
				$this->_data['makers'][$maker['id']] = $maker['name'];
			}
			$this->_data['maker_id'] = $this->input->post('maker_id');
			$this->_data['makers'][0] = 'Choose a supplier';
			$this->_data['form'] = array (
				'date'=>array (
					'name'	   =>'date',
					'id'		 =>'datepicker',
					'value'	  =>$this->form_validation->set_value('date')
				),
				'description'=>array (
					'name'	=>'description[]',
					'id'	  =>'description',
					'type'	=>'text'
				),
				'category'=>array (
					'name'	=>'category[]',
					'id'	  =>'category',
					'type'	=>'text'
				),
				'quantity'=>array (
					'name'	 =>'quantity[]',
					'id'	   =>'quantity',
					'type'	 =>'text',
					'size'	 =>1
				),
				'wholesale'=>array (
					'name'  =>'wholesale[]',
					'id'	=>'wholesale',
					'type'  =>'text',
					'size'  =>4
				),
				'retail'=>array (
					'name'=>'retail[]',
					'id'  =>'retail',
					'type'=>'text',
					'size'=>4
				)
			);
			$this->_data['content'] = $this->load->view('view_record_delivery', $this->_data, TRUE);
			$this->load->view('templates/index', $this->_data);
		}
		else
		{
			$this->load->model('model_deliveries');
			////Check if a delivery already exists for that day. Only one delivery per day please!
			$delivery = $this->model_deliveries->get_delivery(array ('date'	=>$this->input->post('date'), 'maker_id'=>$this->input->post('maker_id')));
			if (count($delivery) > 0)
			{
				$this->_data['content'] = 'Delivery already recorded for ' . $this->input->post('date') . ' for maker ' . $this->input->post('maker_id');
				$this->load->view('templates/index', $this->_data);
			}
			else
			{
				//Process the data
				//Add the delivery note and get the id of the note added
				$delivery_id = $this->model_deliveries->add_delivery();
				//Add the stock
				//Create a foreach loop, go through and add or edit stock for each post item
				$count	   = count($this->input->post('description'));
				for ($i		   = 0; $i < $count; $i ++ )
				{
					//Extract the raw post data
					$description = $this->input->post('description');
					$category	= $this->input->post('category');
					$quantity	= $this->input->post('quantity');
					$wholesale   = $this->input->post('wholesale');
					$retail	  = $this->input->post('retail');

					//Extract the array elements of the post data
					$data['wholesale']   = $wholesale[$i];
					$data['retail']	  = $retail[$i];
					$data['maker_id']	= $this->input->post('maker_id');
					$data['description'] = $description[$i];
					$data['category']	= $category[$i];
					$data['quantity']	= $quantity[$i];

					if (empty($data['wholesale']))
					{
						$data['wholesale'] = $data['retail'] * 0.6;
					}

					//Extract the quantity for using later
					$quantity = $data['quantity'];

					//See if a stock record alreay exists for this item
					$stock_item = $this->model_stock->get_stock_from_maker($data['maker_id'], $data['description']);
					//If it does, then update it
					if ($stock_item)
					{
						//Should only return one record matching maker_id and description
						if (count($stock_item) > 1)
						{
							echo 'Help, something has gone wrong';
							return;
						}
						$new_quantity = $stock_item[0]['quantity'] + $quantity;
						$stock_id	 = $stock_item[0]['id'];
						$this->model_stock->edit_stock_quantity($stock_id, array ('quantity'=>$new_quantity));
					}
					else
					{
						//Add stock and get the id of the last insert
						$stock_id = $this->model_stock->add_stock($data);
					}

					//Add data to the joining table
					$this->model_stock->add_delivered_stock(compact('stock_id', 'delivery_id', 'quantity'));
				}
			}
			//Display the maker's stock
			redirect('stock/check_stock/' . $this->input->post('maker_id'));
		}
	}

	

	public function json_get_stock_from_maker()
	{
		$this->load->model('model_stock');
		$result = json_encode($this->model_stock->get_stock(array ('maker_id'=>$this->input->post('maker'))));
		echo $result;
	}

	public function json_get_current_stock_from_maker()
	{
		$this->load->model('model_stock');
		$result = json_encode($this->model_stock->get_stock(array ('maker_id'  =>$this->input->post('maker'), 'quantity >'=>0)));
		echo $result;
	}

	public function json_get_stock_details()
	{
		$this->load->model('model_stock');
		$result = json_encode($this->model_stock->get_stock(array ('id'=>$this->input->post('stock_id'))));
		echo $result;
	}

}

?>
