<?php

/**
 * Class to handle deliveries. The only way stock can be added is via deliveries.
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Model_deliveries extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Method to add a delivery
	 * @param array $data Array containing the data to be added. Uses the form 'field_name' => value
	 * @return int The id of the last record added
	 */
	public function add_delivery($data)
	{
		$this->db->insert('deliveries',$data );
		return mysql_insert_id ();
	}
	
	/**
	 * Get delivery
	 * Method to retrieve data about a particular delivery
	 * @param array $data Array has keys date and maker
	 * @return array 
	 */
	public function get_delivery($data)
	{
		$result = $this->db->get_where('deliveries', $data);
		return $result->result_array();
	}
	/**
	 * Method to add records to the joining table for deliveries -> stock delivered.
	 * @param array $data Array containing the data to be added. Uses the form 'field_name' => value
	 */
	public function add_delivered_stock($data)
	{
		$this->db->insert('stock_delivered', $data);
	}

}

?>
