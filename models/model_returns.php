<?php

/**
 * Description of model_returns
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Model_returns extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();		
	}
	
	/**
	 * Method to add a return
	 * @param array $data
	 * @return int 
	 */
	public function add_return($data)
	{
		$this->db->insert('returns', $data);
		return mysql_insert_id();
	}
	
	/**
	 * Method to get the stock for a specific return. Needs to look up the stock description
	 * @param int $return_id
	 * @return array
	 */
	public function get_returned_stock($return_id)
	{
		$this->db->join('stock', 'stock.id = returned_stock.stock_id');
		$this->db->select('stock.description, returned_stock.stock_quantity');
		$result = $this->db->get_where('returned_stock', array('return_id' => $return_id));
		return $result->result_array();
	}
	public function add_returned_stock($data)
	{
		$this->db->insert('returned_stock', $data);
		return mysql_insert_id();
	}

}

?>
