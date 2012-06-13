<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Handles manufacturers
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Makers extends MX_Controller
{
	private $_data;

	public function __construct ()
	{
		$this->load->helper(array ('html', 'url'));
		$this->load->library('auth/ion_auth');
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		parent::__construct();
		$this->load->model('model_makers');
	}
	/**
	 * The default method if none is called 
	 */
	public function index()
	{
		//Simply display a page of options to progress
		$this->load->view('templates/index');
	}
	public function get_makers()
	{
		return $this->model_makers->get_makers();
	}
	public function add_makers()
	{
		$this->load->library('form_validation');
		$this->_data['title'] = "Add maker";
		if($this->form_validation->run('add_makers') == FALSE)
		{
			$this->_data['form'] = array(
				'name' => array(
					'name' => 'name',
					'id' => 'name',
					'value' => $this->form_validation->set_value('name')
				)
			);
			$this->_data['content'] = $this->load->view('view_add_maker', $this->_data, TRUE);
			$this->load->view('templates/index', $this->_data);
		}
		else
		{
			$this->load->model('model_makers');
			$result = $this->model_makers->add_maker();
			redirect('stock/makers/view_makers');
		}
	}
	public function view_makers()
	{
		$this->load->library('form_validation');
		$this->_data['title'] = "View makers";
		if($this->form_validation->run('add_maker') == FALSE)
		{
			$this->_data['form'] = array(
				'name' => array(
					'name' => 'name[]',
					'id' => 'name',
					'value' => $this->form_validation->set_value('name')
				)
			);
			$this->_data['makers'] = $this->model_makers->get_makers();
			$this->_data['content'] = $this->load->view('view_view_makers', $this->_data, TRUE);
		}
		else
		{
			$rows = $this->input->post('row');
			$maker_ids = $this->input->post('maker_id');
			$names = $this->input->post('name');
			$this->_data['content'] = '';
			//Iterate through the rows
			foreach($rows as $row)
			{
				//Check if a maker_id exists. If it does, I'veg ot a row to work with.
				if (array_key_exists($row, $maker_ids))
				{
					if ($maker_ids[$row] === '0')
					{
						$data = array(
							'name' => $names[$row]
						);
						$this->model_makers->add_maker($data);
					}
					else
					{
						$maker_id = $maker_ids[$row];
						$data = array(
							'name' => $names[$row]
						);
						$this->model_makers->edit_maker($maker_id, $data);
					}
					$this->_data['makers'] = $this->model_makers->get_makers();
					$this->_data['content'] = $this->load->view('view_view_makers', $this->_data, TRUE);
				}
			}
		}
		$this->load->view('templates/index', $this->_data);
	}

}

?>
