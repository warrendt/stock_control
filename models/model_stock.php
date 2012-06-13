<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Model for handling stock
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Model_stock extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Method to update stock records. Must have an associated id for each record
	 * @param array $data
	 */
	public function edit_stock($data)
	{
		$this->db->update_batch('stock', $data, 'id');
		return;
	}

	/**
	 * Method to delete a single stock record
	 * @param int $id 
	 */
	public function delete_stock($id)
	{
		$this->db->delete('stock', $id);
	}

	/**
	 * Method to create a stock item
	 * @param array $data
	 * @return int The last inserted id
	 */
	public function add_stock($data)
	{
		$this->db->insert('stock', $data);
		return mysql_insert_id();
	}

	/**
	 * Method to get all the stock by a maker
	 * @param int $maker_id 
	 * @return array
	 */
	public function get_stock($data, $select = '')
	{
		if(!($select === ''))
		{
			$this->db->select($select);
		}
		$result = $this->db->get_where('stock', $data);
		return $result->result_array();
	}
	public function get_fields()
	{
		return $this->db->list_fields('stock');
	}

}

?>
