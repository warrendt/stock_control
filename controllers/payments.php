<?php

/**
 * Description of payments
 *
 * @author Joe Miller
 * @copyright Joe Miller 2012
 * 
 */
class Payments extends MX_Controller
{

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
	
	public function viewpayment($id = 0)
	{
		
	}

}

?>
