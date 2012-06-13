<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Description of model_sales
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Model_sales extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	/**
	 * Method to update sales records. Must have an associated id for each record
	 * @param array $data
	 */
	public function edit_sales($data)
	{
		$this->db->update_batch('sales', $data, 'id');
		return;
	}

	/**
	 * Method to delete a single sales record
	 * @param int $id 
	 */
	public function delete_sales($id)
	{
		$this->db->delete('sales', $id);
	}

	/**
	 * Method to create a sales item
	 * @param array $data
	 * @return int The last inserted id
	 */
	public function add_sales($data)
	{
		$this->db->insert('sales', $data);
		return mysql_insert_id();
	}

	/**
	 * Method to get all the sales by a maker
	 * @param array $data Allows for selecting sales by maker, date, etc 
	 * @return array
	 */
	public function get_sales($data = array())
	{
		$this->db->select('sales.id, makers.name AS maker, stock.description AS description, sales.quantity, wholesale, price, date, paid_supplier');
		$this->db->join('stock', 'stock.id = sales.stock_id');
		$this->db->join('makers', 'makers.id = stock.maker_id');
		$this->db->order_by('date', 'desc');
		$result = $this->db->get_where('sales', $data);
		return $result->result_array();
	}
	
	public function add_payments($data)
	{
		$this->db->insert('payments', $data);
		return mysql_insert_id();
	}
	public function add_payment_sales($data)
	{
		$this->db->insert('payment_sales', $data);
		return;
	}

}

?>
