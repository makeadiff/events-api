<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Api_validation{
	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->valid=array();
	}
	function api_data_validation($value,$condition) 
	{
		$flag = 1;
		foreach($condition as $key)
		{
			$key;
			$str=$value;
			$status1=$this->$key($str);
			$status = json_decode($status1);
			foreach($status as $row)
			{
				$arr = array();
				if($row->status == '')
				{
					$this->valid[] = 0;
					return $row->message;
				}			
				else
				{
					$this->valid[] = 1;
					return $row->message;
				}
			}
		}
	}
	public function run()
	{
		//print_r($this->valid);
		$i='';
		foreach($this->valid as $row)
		{
			if($row == 0)
			{
				return 0;
			}
		}
	}
	public function required($str)
	{
		$temp= is_array($str) ? (bool) count($str) : (trim($str) !== '');
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Value Required" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	/**
	 * Valid Email
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		if (function_exists('idn_to_ascii') && $atpos = strpos($str, '@'))
		{
			$str = substr($str, 0, ++$atpos).idn_to_ascii(substr($str, $atpos));
		}

		 $temp = (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
		 $array1 = array();
			if($temp == '')
			{
				$array = array('status' => $temp, 'message' => "Email Not Valid" );
				$array1[] = $array;
				return json_encode($array1);
			}
			else
			{
				$array = array('status' => $temp, 'message' => "Valid" );
				$array1[] = $array;
				return json_encode($array1);
			}
	}
	// --------------------------------------------------------------------
	/**
	 * Alpha
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		$temp = ctype_alpha($str);
		$array1 = array();
			if($temp == '')
			{
				$array = array('status' => $temp, 'message' => "Not Valid" );
				$array1[] = $array;
				return json_encode($array1);
			}
			else
			{
				$array = array('status' => $temp, 'message' => "Valid" );
				$array1[] = $array;
				return json_encode($array1);
			}
	}
	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		$temp= ctype_alnum((string) $str);
		$array1 = array();
			if($temp == '')
			{
				$array = array('status' => $temp, 'message' => "Not Valid" );
				$array1[] = $array;
				return json_encode($array1);
			}
			else
			{
				$array = array('status' => $temp, 'message' => "Valid" );
				$array1[] = $array;
				return json_encode($array1);
			}
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric w/ spaces
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric_spaces($str)
	{
		$temp= (bool) preg_match('/^[A-Z0-9 ]+$/i', $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Invalid Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Alpha-numeric with underscores and dashes
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		$temp= (bool) preg_match('/^[a-z0-9_-]+$/i', $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Numeric
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		$temp=(bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}

	}
	// --------------------------------------------------------------------

	/**
	 * Integer
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		$temp= (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		$temp=(bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		$temp= ctype_digit((string) $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		$temp= ($str != 0 && ctype_digit((string) $str));
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @param	string
	 * @return	bool
	 */
	public function valid_base64($str)
	{
		$temp= (base64_encode(base64_decode($str)) === $str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Strip Image Tags
	 *
	 * @param	string
	 * @return	string
	 */
	public function strip_image_tags($str)
	{
		$temp= $this->CI->security->strip_image_tags($str);
		$array1 = array();
		if($temp == '')
		{
			$array = array('status' => $temp, 'message' => "Not Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
		else
		{
			$array = array('status' => $temp, 'message' => "Valid" );
			$array1[] = $array;
			return json_encode($array1);
		}
	}
}