<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Description of sales
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Sales extends MX_Controller
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
		//$this->output->enable_profiler(TRUE);
	}
	
	/**
	 * Method to record sales 
	 * Create a form, validate it and process the data
	 */
	public function sell_stock($maker_id = array ())
	{
		$this->load->library('form_validation');
		$this->load->model('model_makers');
		$this->load->model('model_stock');
		//Tell the form to select item 0, if no other item has been selected
		$this->_data['maker_ids'] = $this->input->post('maker_id') ? $this->input->post('maker_id') : array (0);
		$this->_data['stock_ids'] = $this->input->post('stock_id');
		if ($this->form_validation->run('sell_stock') == FALSE)
		{
			//Create the list of makers
			$maker_list = $this->model_makers->get_makers();
			$this->_data['maker_options'][0] = 'Choose a supplier';
			foreach ($maker_list as $maker)
			{
				$this->_data['maker_options'][$maker['id']] = $maker['name'];
			}
			//Create the stock_id options.
			$i = 0;
			$this->_data['stock_options'][0] = array (0=>'Select stock');
			
			//Go through all the post data maker_ids
			foreach ($this->_data['maker_ids'] as $maker_id_check)
			{
				//Get an array of stock for htat maker
				$stock_from_maker = $this->model_stock->get_stock(array ('maker_id'  =>$maker_id_check, 'quantity >'=>0));
				//Create data array for each row of the final table
				//Using $i to count through
				foreach ($stock_from_maker as $stock_item)
				{
					$this->_data['stock_options'][$i][$stock_item['id']] = $stock_item['description'];
				}
				$i ++;
			}
			//Define the form input names
			$this->_data['form'] = array (
				'date'=>array (
					'name'	=>'date',
					'id'	  =>'datepicker'
				),
				'maker_id'=>'maker_id[]',
				'stock_id'=>'stock_id[]',
				'quantity'=>array (
					'name' =>'quantity[]',
					'size' =>4
				),
				'price'=>array (
					'name'=>'price[]',
					'size'=>4
				)
			);
		}
		else
		{
			//Initialise variables so they can be accessed as arrays
			$maker_ids = $this->input->post('maker_id');
			$stock_ids = $this->input->post('stock_id');
			$sale_quantities = $this->input->post('quantity');
			$sale_prices = $this->input->post('price');
			
			//Load the model
			$this->load->model('model_sales');
			$this->load->model('model_stock');
			
			//Process the data
			//Add entry to sales table; date, stock_id, quantity, price
			foreach($maker_ids as $key => $maker_id)
			{
				$data = array(
					'stock_id' => $stock_ids[$key],
					'quantity' => $sale_quantities[$key],
					'price' => $sale_prices[$key],
					'date' => $this->input->post('date')
				);
				//Retrieve stock quantity for each stock_id
				$result = $this->model_stock->get_stock(array('id' => $stock_ids[$key]), 'quantity');
				//Create two arrays; stock_id =>  quantity
				$stock_quantity[$stock_ids[$key]] = $result[0]['quantity'];
				$quantities_sold = array_combine($stock_ids, $sale_quantities);
				// Update the data
				$sale_id = $this->model_sales->add_sales($data);
			}
			
			//Calculate new stock quantities;
			//Calculate the new quantity
			foreach($stock_quantity as $key => $stock)
			{
				$new_stock[] = array('id' => $key,'quantity' =>$stock - $quantities_sold[$key]);
			}
			//Batch update the table
			$this->model_stock->edit_stock($new_stock);
			//Redirect to view_sales for date
			redirect('stock/sales');
		}
		$this->_data['title'] = 'Record sales';
		$this->_data['content'] = $this->load->view('view_sell_stock', $this->_data, TRUE);
		$this->load->view('templates/index', $this->_data);
	}
	
	public function index()
	{
		$this->load->model('model_sales');
		$this->_data['title'] = 'View sales ';
		$this->_data['sales'] = $this->model_sales->get_sales();
		$this->_data['content'] = $this->load->view('view_sales', $this->_data, TRUE);
		$this->load->view('templates/index', $this->_data);
	}
	
	public function view_sales($maker_id = 0)
	{
		$this->load->model('model_sales');
		$this->_data['title'] = 'View sales ';
		if ($maker_id === 0)
		{
			$this->_data['sales'] = $this->model_sales->get_sales();
		}
		else
		{
			$this->_data['sales'] = $this->model_sales->get_sales(array('makers.id' => $maker_id));
		}
		$this->_data['content'] = $this->load->view('view_sales', $this->_data, TRUE);
		$this->load->view('templates/index', $this->_data);
	}
	
	public function pay_sales($maker_id = 0)
	{
		
		$this->load->model('model_sales');
		$this->load->library('form_validation');
		$this->_data['title'] = 'Pay supplier ';
		$this->_data['sales'] = array();
		$this->_data['maker_id'] = $this->input->post('maker_id') ? $this->input->post('maker_id') : $maker_id;
		
		$sales_ids = $this->input->post('id');
		$paid_supplier = $this->input->post('paid_supplier');
		if ($this->form_validation->run('pay_sales') == FALSE)
		{
			//Create the list of makers
			$this->load->model('model_makers');
			$maker_list = $this->model_makers->get_makers();
			$this->_data['maker_options'][0] = 'Choose a supplier';
			foreach ($maker_list as $maker)
			{
				$this->_data['maker_options'][$maker['id']] = $maker['name'];
			}
			//Populate the form
			//We need to grab data from the database if
			//form validation has failed, so I can re-populate the form.
			//Otherwise let the on-page javascript handle it.
			
				$this->_data['sales'] = array(
					'paid_date' => array(
						'name' => 'paid_date',
						'value' =>set_value('paid_date'),
						'id' => 'datepicker'
					),
					'description' => array(
						'name' => 'description[]',
						'value' =>set_value('description[]'),
						'readonly' => 'readonly'
					),
					'quantity' => array(
						'name' => 'quantity[]',
						'value' => set_value('quantity[]'),
						'readonly' => 'readonly'
					),
					'wholesale' => array(
						'name' => 'wholesale[]',
						'value' => set_value('wholesale[]'),
						'readonly' => 'readonly'
					),
					'id' => array(
						'name' => 'id[]',
						'value' =>set_value('id[]')
					)
				);
			$this->_data['sales_ids'] = $sales_ids;
			$this->_data['content'] = $this->load->view('view_pay_sales', $this->_data, TRUE);
		}
		else
		{
			//Process the data
			//
			
			
			
			//Add an entry in the payments table and get the id
			$payment_details = array(
				'maker_id' => $this->_data['maker_id'],
				'date' => $this->input->post('paid_date'),
				'amount' => $this->input->post('amount')
			);
			$payment_id = $this->model_sales->add_payments($payment_details);
			//Mark the sales as paid for
			foreach($sales_ids as $key => $sales_id)
			{
				$data[] = array(
					'id' => $sales_id,
					'paid_supplier' => isset($paid_supplier[$key]) ? $paid_supplier[$key] : 0
				);
				$this->model_sales->add_payment_sales(array('sales_id' =>$sales_id, 'payment_id' => $payment_id));
			}
			$result = $this->model_sales->edit_sales($data);
			//Set up the view data
			$this->_data['payment'] = $payment_details;
			$this->_data['payment']['id'] = $payment_id;
			//Show th epayment,with option to print it off
			$this->_data['content'] = $this->load->view('view_payment', $this->_data, TRUE);
		}
		
		$this->load->view('templates/index', $this->_data);
		
	}
	
	public function json_get_unpaid_from_maker()
	{
		$this->load->model('model_sales');
		$result = json_encode($this->model_sales->get_sales(array ('maker_id'=>$this->input->post('maker_id'), 'paid_supplier' => 0)));
		echo $result;
	}

}

?>
