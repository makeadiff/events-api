<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Library Class: Imap */

class Mail {
	// Open IMAP connection
	function send_email($to,$subject,$content,$arr=array())
	{ 
		$ci =& get_instance();
		$ci->load->library('email');
		$config = Array(
			'protocol' => 'smtp',
			//'smtp_host' => 'smtp.gmail.com',
			'smtp_host'	=> 'ssl://smtp.googlemail.com',
			'smtp_port' => 465, // Was 587
			'smtp_user' => 'madapp@makeadiff.in',
			'smtp_pass' => 'madappgonemad',
			'mailtype'  => 'html', 
			'charset'   => 'utf-8',
			'crlf' => "\r\n",
			'newline' => "\r\n"
		);
		$ci->load->library('email'); 
		$ci->email->initialize($config);
		$ci->email->set_newline("\r\n");

		$ci->email->set_mailtype("html");
		$ci->email->from('madapp@makeadiff.in', 'MADApp');
		$ci->email->to($to);

		$ci->email->subject($subject);
		$ci->email->message($content);
		
		$result = $ci->email->send(false);

		if(!$result) {
			$ci->email->print_debugger();
		}

		return $result;

	  /*  $ci =& get_instance();
		$ci->load->library('email');
//        $ci->load->helper('url');
		$config['protocol'] = "smtp";
		$config['smtp_host'] = "smtp.sendgrid.net";
		$config['smtp_port'] = "465";
		$config['smtp_user'] = "reshmarajan"; //username
		$config['smtp_pass'] = "password123";//password
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";

		$ci->email->initialize($config);

		$ci->email->from('test@gmail.com', 'MAD App');
		$list = array($to);
		$ci->email->to($list);
		//$this->email->reply_to('my-email@gmail.com', 'Explendid Videos');
		//$url = site_url('user_registration/email_verification?mail='.$to.'&id='.$random);
		$ci->email->subject($subject);
		$ci->email->message($content);
		$is_sent_mail   = $ci->email->send();
	   
		if($is_sent_mail):
			return TRUE;
		else:
			return FALSE;
		endif;*/
	}	
}