<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Description of model_makers
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Model_makers extends CI_Model
{

	public function __construct ()
	{
		parent::__construct ();
		$this->load->database();
	}
	/**
	 * Method to retrieve maker
	 * If none is chosen, return all of them
	 * @param int $id 
	 */
	public function get_makers($id = 0)
	{
		$this->db->order_by('name');
		if ($id === 0)
		{
			$result = $this->db->get('makers');
		}
		else
		{
			$result = $this->db->get_where('makers', array('id' => $id));
		}
		return $result->result_array();
		
	}
	/**
	 * Method to update a maker record 
	 */
	public function edit_maker($maker_id, $data)
	{
		$this->db->where('id', $maker_id);
		return $this->db->update('makers', $data);
	}
	/**
	 * Method to create a maker item based on form data 
	 */
	public function add_maker($data)
	{
		return $this->db->insert('makers', $data);
	}

}

?>
