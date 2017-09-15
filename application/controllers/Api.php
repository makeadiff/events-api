<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
 * Prints a array, an object or a scalar variable in an easy to view format.
 * Arguments  : $data - the variable that must be displayed
 * Link : http://www.bin-co.com/php/scripts/dump/
 */
function dump() {
  $args = func_get_args();
  $count = count($args) - 1;
  
  print "<pre>";
  if($count) print "-------------------------------------------------------------------------------------------------------------------\n";
  foreach($args as $data) {
	if(is_array($data) or is_object($data)) { //If the given variable is an array, print using the print_r function.
	  if(!$count) print "-----------------------\n";
	  if(is_array($data)) print_r($data);
	  else var_export($data);
	  if(!$count) print "-----------------------\n";
	  else print "=======================================================\n";
	} else {
	  print "</pre>=========&gt;";
	  print var_dump($data);
	  print "&lt;=========<pre>\n";
	}
  }
  if($count) print "-------------------------------------------------------------------------------------------------------------------";
  print "</pre>\n";
}


class Api extends CI_Controller {
	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Api controller constructor
	 */
	function __construct() {
		parent::__construct();
		$this->load->model(array('Api_model'));
		$this->load->helper(array('url', 'string'));
		$this->load->library('api_validation');
		$this->load->library('device_validation');
		date_default_timezone_set('Asia/Kolkata');
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Index page.
	 */
	public function index() {
		echo "api";
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to process login


	 * modified by
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        19-06-2017   
	 * Description:     Function to add city details
	 */
	public function user_login() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) {
			$data['fcm_regid']='';
			$data['email_id'] = $this->input->post_get('email_id');
			$data['password'] = $this->input->post_get('password');
			$data['imei_number'] = $this->input->post_get('imei_number');
			$data['latest_app_version'] = $this->input->post_get('latest_app_version');
			$data['member_list_db_ver'] = $this->input->post_get('member_list_db_ver');
			$data['city_list_db_ver'] = $this->input->post_get('city_list_db_ver');
			$data['vertical_list_db_ver'] = $this->input->post_get('vertical_list_db_ver');
			$data['center_list_db_ver'] = $this->input->post_get('center_list_db_ver');
			$data['event_type_db_ver'] = $this->input->post_get('event_type_db_ver');
			$data['fcm_regid']=$this->input->post_get('fcm_regid');

			#Validation starts here
			$this->api_validation->api_data_validation($data['email_id'], array('required', 'valid_email'));
			$this->api_validation->api_data_validation($data['password'], array('required'));
			$this->api_validation->api_data_validation($data['imei_number'], array('required'));
			/* $this->api_validation->api_data_validation($data['latest_app_version'], array('required'));
			  $this->api_validation->api_data_validation($data['member_list_db_ver'], array('required'));
			  $this->api_validation->api_data_validation($data['city_list_db_ver'], array('required'));
			  $this->api_validation->api_data_validation($data['vertical_list_db_ver'], array('required'));
			  $this->api_validation->api_data_validation($data['center_list_db_ver'], array('required')); */
			$validation = $this->api_validation->run();

			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED, 'user_id' => "", 'name' => "", 'email' => "", 'phone' => "", 
								'role' => "", 'cities' => "", 'verticals' => "", 'centers' => "", 'members' => "", 
								'hash_key' => "",'event_types' => "");
				echo json_encode($results);
				die;
			} else {
				if ((isset($data['email_id']) && !empty($data['email_id'])) && (isset($data['password']) && !empty($data['password']))) {
					$login_data_det = $this->Api_model->login_data($data);

					// dump($login_data_det); echo $this->db->last_query();die;
					if (isset($login_data_det) && !empty($login_data_det)) {
						#$status = $this->exist_user($data);
						$id = $login_data_det['id'];
						$title = $login_data_det['title'];
						$name = $login_data_det['name'];
						$email = $login_data_det['email'];
						$mad_email = $login_data_det['mad_email'];
						$phone = $login_data_det['phone'];
						$dbpassword = $login_data_det['password'];
						$role_id = $login_data_det['group_type'];
						$city_id = $login_data_det['city_id'];
						if ($data['password'] === $dbpassword) {
							/* $log_arr = array(
							  'user_id' => $id,
							  'login_time' => get_cur_date_time(TRUE, TRUE),
							  'ip_address' => get_client_ip(),
							  'created_date' => get_cur_date_time(TRUE, TRUE),
							  );
							  $this->Api_model->save('gtech_user_log', $log_arr); */

							$user_cnt = $this->Api_model->count_detail('User', array('status' => ACTIVE, 'user_type' => 'voluteer', 'city_id' => $city_id)); # Total Count of Center table
							$city_cnt = $this->Api_model->count_detail('City', array('type' => 'actual')); # Total Count of city table
							$vertical_cnt = $this->Api_model->count_detail('Vertical', array('status' => ACTIVE)); # Total Count of vertical table
							$center_cnt = $this->Api_model->count_detail('Center', array('status' => ACTIVE, 'city_id' => $city_id)); # Total Count of Center table
							$event_type_cnt = $this->Api_model->count_detail('Event_Type', array('status' => ACTIVE)); # Total Count of event type table
							$data['city_id'] = $city_id;
							$dbstatus = $this->check_app_ws_db($data, $user_cnt, $city_cnt, $vertical_cnt, $center_cnt,$event_type_cnt);
							
							$check_imei = $this->Api_model->count_detail('Push_Notification', array('status' => ACTIVE,'user_id' =>$id ,'imei_no'=>$data['imei_number'])); # Checking imei number and user id in push notification table
							if(isset($check_imei) && $check_imei > 0) {
								$hash_key = $this->device_validation->key_generator($data['imei_number']);
								$update_array = array('hash_key' => $hash_key,'fcm_regid'=>$data['fcm_regid']);
								$keyvalue=$this->Api_model->update("Push_Notification", $update_array, array('imei_no'=>$data['imei_number'],'user_id' =>$id));
								
							} else {
								$this->Api_model->update("Push_Notification", array('status' => INACTIVE), array('imei_no'=>$data['imei_number']));
								$hash_key = $this->device_validation->key_generator($data['imei_number']);
								$update_array1 = array('hash_key' => $hash_key,
								'user_id'=>$id,'imei_no'=>$data['imei_number'],
								'status'=>ACTIVE,
								'fcm_regid'=>$data['fcm_regid'],
								'created_on'=>$this->device_validation->get_cur_date_time(),
								);
								$keyvalue=$this->Api_model->save_data('Push_Notification',$update_array1);
							}
							$this->Api_model->update('User',array('app_version'=>$data['latest_app_version']),array('id'=>$id)); // update appversion number
							//$hash_key = $this->device_validation->key_generator($data['imei_number']);
							
							if (isset($keyvalue) && !empty($keyvalue)) {
								$results = array('status' => LOGIN_SUCCESS, 'user_id' => $id, 'name' => $name, 'email' => $email, 'mad_email' => $mad_email, 'phone' => $phone, 
													'role' => $role_id,'city_id'=>$city_id, 'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 
													'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'], 'hash_key' => $hash_key,
													'event_types'=>$dbstatus['event_type_det']);
							} else {
								$results = array('status' => LOGIN_FAILURE, 'user_id' => "", 'name' => "", 'email' => "", 'phone' => "", 'role' => "",
													'city_id' => "", 'cities' => "", 'verticals' => "", 'centers' => "", 'members' => "", 'hash_key' => "",'event_types' =>"");
							}
							echo json_encode($results);
							die;
						} else {
							$results = array('status' => INVALID_PASSWORD, 'user_id' => "", 'name' => "", 'email' => "", 'phone' => "", 'role' => "",
												'city_id' => "", 'cities' => "", 'verticals' => "", 'centers' => "", 'members' => "", 'hash_key' => "",'event_types' =>"");
							echo json_encode($results);
							die;
						}
					} else {
						$results = array('status' => INVALID_EMAIL, 'user_id' => "", 'name' => "", 'email' => "", 'phone' => "", 'role' => "",
											'city_id' => "", 'cities' => "", 'verticals' => "", 'centers' => "", 'members' => "", 'hash_key' => "",'event_types' =>"");
						echo json_encode($results);
						die;
					}
				} else {
					$results = array('status' => NULL_CREDENTIALS, 'user_id' => "", 'name' => "", 'email' => "", 'phone' => "", 'role' => "",
										'city_id' => "", 'cities' => "", 'verticals' => "", 'centers' => "", 'members' => "", 'hash_key' => "",'event_types' =>"");
					echo json_encode($results);
					die;
				}
			}
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...');
			echo json_encode($results);
			die;
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to check app and webservice database.
	 */
	public function check_app_ws_db($data, $user_cnt, $city_cnt, $vertical_cnt, $center_cnt,$event_type_cnt) {
		if(!$data['city_id'] and $data['user_id']) $data['city_id'] = $this->Api_model->get_datum('User', 'city_id', array('id' => $data['user_id']))->city_id;

		if ($data['member_list_db_ver'] == DB_VER_DEF_CNT) {
			$user_det = $this->Api_model->get_data('User', 'id,name,title,email,phone,sex,photo,joined_on,address,center_id,city_id,city_other,user_type,admin_credit', 
			  array('status' => ACTIVE, 'user_type' => 'volunteer', 'city_id' => $data['city_id']), TRUE, 'id asc');
			#echo $this->db->last_query();
		} else if ($data['member_list_db_ver'] != $user_cnt) {
			$user_det = $this->Api_model->get_data('User', 'id,name,title,email,phone,sex,photo,joined_on,address,center_id,city_id,city_other,user_type,admin_credit', 
			  array('status' => ACTIVE, 'user_type' => 'volunteer', 'city_id' => $data['city_id']), TRUE, 'id asc');
			#echo $this->db->last_query();
		} else {
			$user_det = array();
		}

		if ($data['city_list_db_ver'] == DB_VER_DEF_CNT) {
			$city_det = $this->Api_model->get_data('City', 'id,name,president_id,added_on,classes_happening,region_id,type', 
			  array(), TRUE, 'id asc');
			#echo $this->db->last_query();
		} else if ($data['city_list_db_ver'] != $city_cnt) {
			$city_det = $this->Api_model->get_data('City', 'id,name,president_id,added_on,classes_happening,region_id,type', 
			  array(), TRUE, 'id asc');
			#echo $this->db->last_query();
		} else {
			$city_det = array();
		}
		if ($data['vertical_list_db_ver'] == DB_VER_DEF_CNT) {
			$vertical_det = $this->Api_model->get_data('Vertical', 'id,key,name,status', array('status' => ACTIVE), TRUE, 'id asc');
		} else if ($data['vertical_list_db_ver'] != $vertical_cnt) {
			$vertical_det = $this->Api_model->get_data('Vertical', 'id,key,name,status', array('status' => ACTIVE), TRUE, 'id asc');
		} else {
			$vertical_det = array();
		}
		if ($data['center_list_db_ver'] == DB_VER_DEF_CNT) {
			$center_det = $this->Api_model->get_data('Center', 'id,name,city_id,center_head_id,class_starts_on,status', 
			  array('status' => ACTIVE, 'city_id' => $data['city_id']), TRUE, 'id asc');
			#echo $this->db->last_query();
		} else if ($data['center_list_db_ver'] != $vertical_cnt) {
			$center_det = $this->Api_model->get_data('Center', 'id,name,city_id,center_head_id,class_starts_on,status', 
			  array('status' => ACTIVE, 'city_id' => $data['city_id']), TRUE, 'id asc');
			#echo $this->db->last_query();
		} else {
			$center_det = array();
		}

		 if ($data['event_type_db_ver'] == DB_VER_DEF_CNT) {
			$event_type_det = $this->Api_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
		} else if ($data['event_type_db_ver'] != $user_cnt) {
			$event_type_det = $this->Api_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
		} else {
			$event_type_det = array();
		}
		/* $city_det = array();
		  $vertical_det = array();
		  $center_det = array(); */
		$results = array('user_det' => $user_det, 'city_det' => $city_det, 'vertical_det' => $vertical_det, 'center_det' => $center_det,'event_type_det'=>$event_type_det);
		#$results = array('user_det' => 10, 'city_det' => 100, 'vertical_det' => 1000, 'center_det' => 10000);
		return $results;
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to handle event management.

	 * modified by
	 * @author          Reshma rajan
	 * @since           Version 1.0.0
	 * Date:        19-06-2017   
	 * Description:     Function to handle event management.
	 */
	public function event_management() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_type'] = $this->input->post_get('event_type');
			$data['city_id'] = $this->input->post_get('city_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$data['event_category'] = $this->input->post_get('event_category');
			$data['page'] = $this->input->post_get('page');
			$data['member_list_db_ver'] = $this->input->post_get('member_list_db_ver');
			$data['city_list_db_ver'] = $this->input->post_get('city_list_db_ver');
			$data['vertical_list_db_ver'] = $this->input->post_get('vertical_list_db_ver');
			$data['center_list_db_ver'] = $this->input->post_get('center_list_db_ver');
			$data['event_type_db_ver'] = $this->input->post_get('event_type_db_ver');

			#Validation starts here
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_type'], array('required'));
			$this->api_validation->api_data_validation($data['city_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_category'], array('required'));
			$validation = $this->api_validation->run();

			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED);
				echo json_encode($results);
				die;
			} else {
				$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
				$today_midnit= $this->device_validation->get_cur_date_time(TRUE,TRUE);

				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_category']) && !empty($data['event_category']))) {
					#echo $this->db->last_query();die;
					if(isset($data['page']) && $data['page'] !='') {
						$offset = PAGINATION_CONST * $data['page'];
						$limit = array('start' => $offset , 'length' => PAGINATION_CONST);
					} else {
						$limit=array();
					}
					$user_cnt = $this->Api_model->count_detail('User', array('status' => ACTIVE)); # Total Count of Center table
					$city_cnt = $this->Api_model->count_detail('City'); # Total Count of city table
					$vertical_cnt = $this->Api_model->count_detail('Vertical', array('status' => ACTIVE)); # Total Count of vertical table
					$center_cnt = $this->Api_model->count_detail('Center', array('status' => ACTIVE)); # Total Count of Center table
					$event_type_cnt = $this->Api_model->count_detail('Event_Type', array('status' => ACTIVE)); # Total Count of event type table
					$dbstatus = $this->check_app_ws_db($data, $user_cnt, $city_cnt, $vertical_cnt, $center_cnt,$event_type_cnt);

					if ($data['event_category'] == INVITED_EVENT) {
						$joins = array(
							array('table' => 'Event AS ET',
								'condition' => 'ET.id=UE.event_id AND ET.created_by_user_id != '.$data['user_id'],
								'jointype' => ''
							));
						
						$event_det = $this->Api_model->get_data('UserEvent AS UE', 'ET.id AS event_id,ET.name,ET.description,ET.event_type_id,ET.starts_on,ET.city_id,ET.latitude,ET.longitude,ET.city_id,ET.place,UE.user_choice', array('UE.user_id' => $data['user_id'],'UE.created_from' => APP, 'ET.status' => ACTIVE,'ET.starts_on >=' => $today_midnit), TRUE, 'ET.starts_on asc', $joins,$limit);
						// print $this->Api_model->db->last_query();
						// exit;
						if (isset($event_det) && !empty($event_det)) {
							$res_array = '';
							foreach ($event_det as $ed) {
								$event_id = $ed->event_id;
								$event_name = $ed->name;
								$event_details = $ed->description;
								$event_location = $ed->place;
								$event_type =$ed->event_type_id;
								$latitude = $ed->latitude;
								$longitude = $ed->longitude;
								$city_id = $ed->city_id;
								$user_choice = $ed->user_choice;
								$event_time=date("h:i A", strtotime($ed->starts_on));
								$event_date=date("d-F-Y", strtotime($ed->starts_on));

								$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => GO));
								$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id));
								$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => MYBEGO));
								$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => CANTGO));
								
								
								$results_1 = array('event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 
													'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 
													'latitude' => $latitude, 'longitude' => $longitude, 'event_city' => $city_id, 'event_going_count' => $user_go_cnt, 
													'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'event_time'=>$event_time,
													'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
								$res_array[] = $results_1;
							}
							$results = array('status' => SUCCESS,'events_array'=>$res_array, 'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 
											'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'],'event_types'=>$dbstatus['event_type_det']);
							echo json_encode($results);
							die;
						} else {
							//$results = array('status' => FAILURE, 'event_id' => "", 'event_name' => "", 'event_details' => "", 'event_date' => "", 'event_location' => "", 'event_type' => "", 'latitude' => "", 'longitude' => "", 'event_city' => "", 'event_going_count' => "", 'event_invited_count' => "", 'event_maybe_count' => "");
							$results = array('status' => SUCCESS,'events_array'=>array() ,'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 
												'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'],'event_types'=>$dbstatus['event_type_det']);
							echo json_encode($results);
							die;
						}
					} else if ($data['event_category'] == POSTED_EVENT) {
						$event_det=array();
						$upcoming_event=array();
						$past_event=array();
						$upcoming_event = $this->Api_model->get_data('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,event_type_id', 
							array('Event.created_by_user_id' => $data['user_id'],'Event.created_from' => APP, 'Event.status' => ACTIVE,
								'starts_on >='=>$this->device_validation->get_cur_date_time(TRUE,TRUE)), TRUE, 'starts_on asc',array(),$limit);
						$past_event = $this->Api_model->get_data('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,event_type_id', 
							array('Event.created_by_user_id' => $data['user_id'],'Event.created_from' => APP, 'Event.status' => ACTIVE,
								'starts_on <'=>$this->device_validation->get_cur_date_time(TRUE,TRUE)), TRUE, 'starts_on desc',array(),$limit);
					  // print $this->Api_model->db->last_query();
					 // $event_det = $this->Api_model->get_data('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,event_type_id', array('Event.created_by_user_id' => $data['user_id'],'Event.created_from' => APP, 'Event.status' => ACTIVE), TRUE, 'starts_on desc',array(),$limit);
						if(isset($upcoming_event) && !empty($upcoming_event) && isset($past_event) && !empty($past_event)){
						    $event_det = array_merge($upcoming_event, $past_event);
						} else if(isset($past_event) && !empty($past_event)) {
						    $event_det = $this->Api_model->get_data('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,event_type_id', 
						  		array('Event.created_by_user_id' => $data['user_id'],'Event.created_from' => APP, 'Event.status' => ACTIVE,
						  		'starts_on <'=>$this->device_validation->get_cur_date_time(TRUE,TRUE)), TRUE, 'starts_on desc',array(),$limit);
						} else {
						    $event_det = $this->Api_model->get_data('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,event_type_id', 
						   			array('Event.created_by_user_id' => $data['user_id'],'Event.created_from' => APP, 'Event.status' => ACTIVE,
						   			'starts_on >='=>$this->device_validation->get_cur_date_time(TRUE,TRUE)), TRUE, 'starts_on asc',array(),$limit);
						}

						if (isset($event_det) && !empty($event_det)) {
							$res_array = '';
							$event_arr=array();
							foreach ($event_det as $ed) {
							    $event_id = $ed->event_id;
								$event_name = $ed->name;
								$event_details = $ed->description;
								$event_location = $ed->place;
								$event_type =$ed->event_type_id;
								$latitude = $ed->latitude;
								$longitude = $ed->longitude;
								$city_id = $ed->city_id;
								$event_time=date("h:i A", strtotime($ed->starts_on));
								$event_date=date("d-F-Y", strtotime($ed->starts_on));
								$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$ed->event_id), TRUE, 'id asc');
								
								if(isset($event_arr) && !empty($event_arr)){
									$user_choice = $event_arr->user_choice;
								} else {
								    $user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
								}
								
							   $eventdate=date("Y-m-d H:i:s", strtotime($ed->starts_on));
							   $current_date=$this->device_validation->get_cur_date_time(TRUE,TRUE);
							   
								if($eventdate >= $current_date)
								{
									$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => GO));
									$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id));
									$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => MYBEGO));
									$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => CANTGO));
									$results_1 = array('event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 'latitude' => $latitude, 'longitude' => $longitude, 'event_city' => $city_id, 'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'event_time'=>$event_time,'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
							   
								} else { 
									$attended = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'present' => ACTIVE,'late'=>INACTIVE));
									$late = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id,'present' => ACTIVE,'late'=>ACTIVE));
									$missed = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'present' => MISSED,'late'=>DEFAULT_STATUS));
									$user_cantgo_cnt=0;
									$results_1 = array('event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 'latitude' => $latitude, 'longitude' => $longitude, 'event_city' => $city_id, 'event_going_count' => $attended, 'event_invited_count' => $missed, 'event_maybe_count' => $late,'event_time'=>$event_time,'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
								}
								$res_array[] = $results_1;
							}
							$results = array('status' => SUCCESS,'events_array'=>$res_array, 'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'],'event_types'=>$dbstatus['event_type_det']);
							echo json_encode($results);
							die;
						} else {
							//$results = array('status' => FAILURE, 'event_id' => "", 'event_name' => "", 'event_details' => "", 'event_date' => "", 'event_location' => "", 'event_type' => "", 'latitude' => "", 'longitude' => "", 'event_city' => "", 'event_going_count' => "", 'event_invited_count' => "", 'event_maybe_count' => "");
							 $results = array('status' => SUCCESS,'events_array'=>array(),  'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'],'event_types'=>$dbstatus['event_type_det']);
							 echo json_encode($results);
							die;
						}
					} else if ($data['event_category'] == USER_CITY_EVENT) {
						
						//$user_city = $this->Api_model->get_datum('User', 'id,city_id', array('id' => $data['user_id']), TRUE, 'id asc');
						//if (isset($user_city) && !empty($user_city)) {

						   $event_det = $this->Api_model->get_data('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,Event.event_type_id', array('Event.city_id' => $data['city_id'],'Event.created_by_user_id!=' => $data['user_id'],'Event.created_from' => APP,'Event.starts_on >=' => $today_midnit, 'Event.status' => ACTIVE), TRUE, 'Event.starts_on asc',array(),$limit);

							if (isset($event_det) && !empty($event_det)) {
								$res_array = '';
								$event_arr=array();
								foreach ($event_det as $ed) {
									$event_id = $ed->event_id;
									$event_name = $ed->name;
									$event_details = $ed->description;
									$event_location = $ed->place;
									$event_type =$ed->event_type_id;
									$latitude = $ed->latitude;
									$longitude = $ed->longitude;
									$city_id = $ed->city_id;
									$event_time=date("h:i A", strtotime($ed->starts_on));
									$event_date=date("d-F-Y", strtotime($ed->starts_on));
								   
									$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$ed->event_id), TRUE, 'id asc');
								
									if(isset($event_arr) && !empty($event_arr)){
										$user_choice = $event_arr->user_choice;
									}else
									{
									  $user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
									}
									$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => GO));
									$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id));
									$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => MYBEGO));
									$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => CANTGO));
									$results_1 = array('event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 'latitude' => $latitude, 'longitude' => $longitude, 'event_city' => $city_id, 'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'event_time'=>$event_time,'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
									$res_array[] = $results_1;
								}
								 $results = array('status' => SUCCESS,'events_array'=>$res_array, 'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'],'event_types'=>$dbstatus['event_type_det']);
								echo json_encode($results);
								die;
							} else {
								//$results = array('status' => FAILURE, 'event_id' => "", 'event_name' => "", 'event_details' => "", 'event_date' => "", 'event_location' => "", 'event_type' => "", 'latitude' => "", 'longitude' => "", 'event_city' => "", 'event_going_count' => "", 'event_invited_count' => "", 'event_maybe_count' => "");
								$results = array('status' => SUCCESS,'events_array'=>array(),  'cities' => $dbstatus['city_det'], 'verticals' => $dbstatus['vertical_det'], 'centers' => $dbstatus['center_det'], 'members' => $dbstatus['user_det'],'event_types'=>$dbstatus['event_type_det']);
								echo json_encode($results);
								die;
							}
					   /* } else {
							//$results = array('status' => FAILURE, 'event_id' => "", 'event_name' => "", 'event_details' => "", 'event_date' => "", 'event_location' => "", 'event_type' => "", 'latitude' => "", 'longitude' => "", 'event_city' => "", 'event_going_count' => "", 'event_invited_count' => "", 'event_maybe_count' => "");
							 $results = array('status' => FAILURE,'events_array'=>array());
							echo json_encode($results);
							die;
						}*/
					}
				} else {
					$results = array('status' => VALIDATION_FAILED);
					echo json_encode($results);
					die;
				}
			}
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...');
			echo json_encode($results);
			die;
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to check RSVP from UserEventer
	 */
	public function rsvp_user() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) {
			$data['event_id'] = $this->input->post_get('event_id');
			$data['user_id'] = $this->input->post_get('user_id');
			$data['user_choice'] = $this->input->post_get('user_choice');
			$data['reason'] = $this->input->post_get('reason');
			$data['hash_key'] = $this->input->post_get('hash_key');

			#Validation starts here
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['user_choice'], array('required'));
			$validation = $this->api_validation->run();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED);
				echo json_encode($results);
				die;
			} else {
				$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id']))) {
					$where = array('user_id' => $data['user_id'], 'event_id' => $data['event_id']);
				   /* if ($data['user_choice'] == CANTGO) { // Checking whether  user choose can't go option
						$update_array = array('user_choice' => $data['user_choice'], 'reason' => $data['reason']);
					} else {
						$update_array = array('user_choice' => $data['user_choice']);
					}*/
					$user_exist = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_id' => $data['user_id']));
					 if (isset($user_exist) && !empty($user_exist)) {
						  $update_array = array('user_choice' => $data['user_choice'], 'reason' => $data['reason']);
						  $is_update = $this->Api_model->update("UserEvent", $update_array, $where);
					 }else
					 {     
						  $user_arr=array('user_id'=>$data['user_id'],'event_id'=>$data['event_id'],'created_from'=>APP,'created_on'=>$this->device_validation->get_cur_date_time(),'type'=>RSVP,'present'=>INACTIVE,'user_choice' => $data['user_choice'], 'reason' => $data['reason']);
						  $is_update=$this->Api_model->save_data('UserEvent',$user_arr);
					 }
					
					#echo $this->db->last_query();die;
					if (isset($is_update) && !empty($is_update)) {
						
						$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
						$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
						$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
						$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
						$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');
						if(isset($event_arr) && !empty($event_arr)){
							$user_choice = $event_arr->user_choice;
						} else {
							$user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
						}
						
						$results = array('status' => SUCCESS,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,
											'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
						echo json_encode($results);
						die;
					} else {
						$results = array('status' => FAILURE);
						echo json_encode($results);
						die;
					}
				} else {
					$results = array('status' => VALIDATION_FAILED);
					echo json_encode($results);
					die;
				}
			}
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to create or update Event.

	  * modified by
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        19-06-2017   
	 * Description:     Function to add push notification.
	 * modified by
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:            30-06-2017   
	 * Description:     Send mail section
	 */
	public function create_update_event() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_name'] = $this->input->post_get('event_name');
			$data['date'] = $this->input->post_get('event_date');
			$data['time'] = $this->input->post_get('event_time');
			$data['description'] = $this->input->post_get('event_details');
			$data['type'] = $this->input->post_get('event_type');
			$data['latitude'] = $this->input->post_get('latitude');
			$data['longitude'] = $this->input->post_get('longitude');
			$data['location'] = $this->input->post_get('event_location');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['notification_status'] = $this->input->post_get('notification_status');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$data['event_city'] = $this->input->post_get('event_city');
			
			$dat['f_event_name'] = $this->input->post_get('f_event_name');
			$dat['f_event_date'] = $this->input->post_get('f_event_date');
			$dat['f_event_time'] = $this->input->post_get('f_event_time');
			$dat['f_event_details'] = $this->input->post_get('f_event_details');
			$dat['f_event_type'] = $this->input->post_get('f_event_type');
			$dat['f_event_location'] = $this->input->post_get('f_event_location');
			$dat['f_event_city'] = $this->input->post_get('f_event_city');

			#Validation starts here
			
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_name'], array('required'));
			$this->api_validation->api_data_validation($data['date'], array('required'));
			$this->api_validation->api_data_validation($data['time'], array('required'));
			$this->api_validation->api_data_validation($data['description'], array('required'));
			$this->api_validation->api_data_validation($data['type'], array('required'));
			//$this->api_validation->api_data_validation($data['latitude'], array('required'));
			//$this->api_validation->api_data_validation($data['longitude'], array('required'));
			//$this->api_validation->api_data_validation($data['location'], array('required'));
			$email=array();
			$validation = $this->api_validation->run();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED);
				echo json_encode($results);
				die;
			} else {
				$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_name']) && !empty($data['event_name']))) {
					#echo $this->db->last_query();die;
					$event_date = date("Y-m-d", strtotime($data['date'])) . ' ' . date("H:i", strtotime($data['time']));
					if (isset($data['event_id']) && !empty($data['event_id'])) {

						$update_array = array(
							'name' => $data['event_name'],
							'description' => $data['description'],
							'starts_on' => $event_date,
							'place' => $data['location'],
							'event_type_id' => $data['type'],
							'created_by_user_id' => $data['user_id'],
							'city_id' => $data['event_city'],
							'latitude' => $data['latitude'],
							'longitude' => $data['longitude'],
							'created_from' => APP,
							'updated_on' => $this->device_validation->get_cur_date_time()
						);
						$where = array('id' => $data['event_id']);
						$is_update = $this->Api_model->update("Event", $update_array, $where);
						if (isset($is_update) && !empty($is_update)) {

							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
							$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
							$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
							$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
							$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');
							if(isset($event_arr) && !empty($event_arr)){
								$user_choice = $event_arr->user_choice;
							} else {
							    $user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
							}

							$results = array('user_choice'=>$user_choice,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'status' => SUCCESS,'event_id' => $data['event_id'], 'event_name' => $data['event_name'], 'event_details' => $data['description'], 'event_date' => $data['date'], 'event_location' => $data['location'], 'event_type' => $data['type'], 'latitude' => $data['latitude'], 'longitude' => $data['longitude'], 'event_city' => $data['event_city'],'event_time'=>$data['time'],'event_cantgo_count' => $user_cantgo_cnt);
							echo json_encode($results);


							$event_det = $this->Api_model->get_datum('Event', 'id,name,description,city_id,latitude,longitude,place,starts_on,event_type_id', 
										array('id' => $data['event_id']), TRUE, 'id asc');
							$event_time=date("h:i A", strtotime($event_det->starts_on));
							$event_date=date("d-F-Y", strtotime($event_det->starts_on));

							$joins = array(
							  array('table' => 'Push_Notification',
								  'condition' => 'Push_Notification.user_id=UserEvent.user_id AND Push_Notification.status='.ACTIVE,
								  'jointype' => 'LEFT'
							  ),array('table' => 'User',
									'condition' => 'User.id=UserEvent.user_id',
									'jointype' => ''
								)
							);

							// start push notification code 
							$get_users = $this->Api_model->get_data('UserEvent', 'UserEvent.rsvp_auth_key,Push_Notification.fcm_regid,User.email,User.id,User.name', 
									array('UserEvent.event_id' => $data['event_id'],'UserEvent.user_id != '=>$data['user_id']), TRUE, 'UserEvent.id asc',$joins);

							if($data['notification_status'] == ON && isset($get_users) && !empty($get_users)){ // checking notification status on or off
								$registatoin_ids=array();
								$msg_content='';
								if(isset($dat['f_event_name']) && $dat['f_event_name']==1){
									$msg_content.='Name : '.$data['event_name'].'<br/>';
								}
								if(isset($dat['f_event_details']) && $dat['f_event_details']==1){
									$msg_content.='Details : '.$data['description'].'<br/>';
								}
								if(isset($dat['f_event_date']) && $dat['f_event_date']==1){
									$msg_content.='Date : '.$data['date'].'<br/>';
								}
								if(isset($dat['f_event_time']) && $dat['f_event_time']==1){
									$msg_content.='Time : '.$data['time'].'<br/>';
								}
								if(isset($dat['f_event_location']) && $dat['f_event_location']==1){
									$msg_content.='Location : '.$data['location'].'<br/>';
								}
								if(isset($dat['f_event_city']) && $dat['f_event_city']==1 && $data['event_city'] !=''){
									$city_det = $this->Api_model->get_datum('City', 'id,name', array('id'=>$data['event_city']), TRUE, 'id asc');
									$msg_content.='City : '.$city_det->name.'<br/>';
								}
								if(isset($dat['f_event_type']) && $dat['f_event_type']==1){
									$type_det = $this->Api_model->get_datum('event_type', 'id,name', array('id'=>$data['type']), TRUE, 'id asc');
									$msg_content.='Type of Event : '.$type_det->name.'<br/>';
								}

								if(isset($msg_content) && $msg_content !='') {
									$updated_msg='<br/>The following details of the event has been changed.<br/><br/>'.$msg_content;
								} else {
									$updated_msg='';
								}
								foreach ($get_users as $key) {
									if(isset($key->fcm_regid) && $key->fcm_regid !='null' && $key->fcm_regid !=''){
									    $registatoin_ids[]=$key->fcm_regid;
									}
									if(isset($key->email) && $key->email !='null' && $key->email !=''){
									    //$email[]=$key->email;
										if(isset($key->rsvp_auth_key) && !empty($key->rsvp_auth_key)){
										  $auth_key=$key->rsvp_auth_key; 
										} else {
										  $auth_key='';
										}
										$subject = "Event Re-scheduled";
										$go_url=site_url('api/deep_linking_url').'?event_id='.$data['event_id'].'&rsvp='.GO.'&rsvp_auth_key='.$auth_key;
										$may_go_url=site_url('api/deep_linking_url').'?event_id='.$data['event_id'].'&rsvp='.MYBEGO.'&rsvp_auth_key='.$auth_key;
										$not_go_url=site_url('api/deep_linking_url').'?event_id='.$data['event_id'].'&rsvp='.CANTGO.'&rsvp_auth_key='.$auth_key;
										$data['mail_gretting'] = "Dear ".$key->name;
										$data['mad_gretting'] = "";
										$data['mail_content'] = "This is a gentle reminder on behalf of MAD(Make A Difference) that we have updated the scheduled event <b>".$event_det->name. "</b> on ".$event_date." ".$event_time.".<br/>".$updated_msg."Please confirm your presence at the Event. Looking forward to meet you at the Event.<div style=\"text-align:center;padding:15px 0;\">
<a href='".$go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>GOING</a><a href='".$may_go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>MAYBE</a><a href='".$not_go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>CAN’T GO</a>
</div> Looking forward to meet you at the Event.<br/>";

										$tracker_exist = $this->Api_model->get_datum('App_Event_Mail_Tracker', 'id', 
															  array('receive_id' => $key->id,'item_id'=>$data['event_id'],'type'=>TYPE_UPDATE,'status'=>2), TRUE, 'id asc');
										if (isset($tracker_exist) && empty($tracker_exist) && $auth_key !='') 
										{
										 
											$tracker=array(
											'receive_id' =>$key->id,
											'item_id' =>$data['event_id'], 
											'type' =>TYPE_UPDATE,
											'email_id' => $key->email,
											'email_subject' =>$subject,
											//'gtech_greating' =>$data['mail_gretting'],
											'email_greating' =>$data['mail_gretting'],
											'email_content' =>$data['mail_content'],
										   // 'created_by' =>0,
											'status' =>2,
											'created_at' =>$this->device_validation->get_cur_date_time()
											);
											$this->Api_model->save_data('App_Event_Mail_Tracker',$tracker);
										}
									}
								  }
								  $event_det = $this->Api_model->get_datum('Event', 'id,name,description,city_id,latitude,longitude,place,starts_on,event_type_id', 
																array('id' => $data['event_id']), TRUE, 'id asc');
								  $event_time=date("h:i A", strtotime($event_det->starts_on));
								  $event_date=date("d-F-Y", strtotime($event_det->starts_on));
								  $user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
								  $invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
								  $user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
								  $user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
								  $event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');
								  if(isset($event_arr) && !empty($event_arr)){
										  $user_choice = $event_arr->user_choice;
								  }else
								  {
										  $user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
								  }
								  $message = array('status' => SUCCESS,"title" => "Event has been updated",'type'=>TYPE_UPDATE,'event_id' => $data['event_id'], 'event_name' => $event_det->name, 'event_details' => $event_det->description, 'event_date' => $event_date, 'event_location' => $event_det->place, 'event_type' => $event_det->event_type_id, 'latitude' => $event_det->latitude, 'longitude' => $event_det->longitude, 'event_city' => $event_det->city_id,'event_time'=>$event_time,'user_choice'=>$user_choice,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'event_cantgo_count' => $user_cantgo_cnt);
								  $this->push_notification($registatoin_ids,$message);
								}
							} else {
								$results = array('status' => FAILURE);
								echo json_encode($results);
								die;
							}
					 /* } else {
							$results = array('status' => ALREADY_EXIST);
							echo json_encode($results);
							die;
						} */   

					} else {
						$event_det = $this->Api_model->get_datum('Event', 'id,name,description,city_id,latitude,longitude,place,starts_on,event_type_id', array('name' => $data['event_name'],'city_id'=>$data['event_city'],'starts_on'=>$event_date,'place'=>$data['location']), TRUE, 'id asc');
						if (isset($event_det) && empty($event_det)) {
							$arr = array(
								'name' => $data['event_name'],
								'description' => $data['description'],
								'starts_on' => $event_date,
								'place' => $data['location'],
								'event_type_id' => $data['type'],
								'created_by_user_id' => $data['user_id'],
								'city_id' => $data['event_city'],
								'latitude' => $data['latitude'],
								'longitude' => $data['longitude'],
								'notification_status' => $data['notification_status'],
								'created_from' => APP,
								'created_on' => $this->device_validation->get_cur_date_time(),
								'status' => ACTIVE
							);
							$add_event = $this->Api_model->save_data('Event', $arr);

							if (isset($add_event) && !empty($add_event)) {

								$user_arr=array('user_id'=>$data['user_id'],'event_id'=>$add_event,'created_from'=>APP,'created_on'=>$this->device_validation->get_cur_date_time(),'present'=>INACTIVE);
								$insertid[]=$this->Api_model->save_data('UserEvent',$user_arr);
								$results = array('status' => SUCCESS, 'event_id' => $add_event);
								echo json_encode($results);
								die;
							} else {
								$results = array('status' => FAILURE);
								echo json_encode($results);
								die;
							}
						} else {
							$results = array('status' => ALREADY_EXIST);
							echo json_encode($results);
							die;
						}    
					}
				} else {
					$results = array('status' => VALIDATION_FAILED);
					echo json_encode($results);
					die;
				}
			}
		} else {
			#$data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...');
			echo json_encode($results);
			die;
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to cancel Event.
	 * modified by
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        19-06-2017   
	 * Description:     Function to cancel Event.
	 * modified by
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:            30-06-2017   
	 * Description:     Send mail section
	 */
	public function cancel_event() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) { 
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			#Validation starts here
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$validation = $this->api_validation->run();
			//$validation = 1;
			$email=array();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED);
				echo json_encode($results);
			} else {
				$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id']))) {
					$update_array = array('status' => INACTIVEEVNT);
					$where = array('id' => $data['event_id']);
					$is_update = $this->Api_model->update("Event", $update_array, $where);
					if (isset($is_update) && !empty($is_update)) {

					  $results = array('status' => SUCCESS);
					  echo json_encode($results);

						$joins = array(
							  array('table' => 'Push_Notification',
								  'condition' => 'Push_Notification.user_id=UserEvent.user_id AND Push_Notification.status='.ACTIVE,
								  'jointype' => ''
							  ),
							array('table' => 'Event',
								  'condition' => 'Event.id=UserEvent.event_id AND Event.created_by_user_id!=UserEvent.user_id',
								  'jointype' => ''
							  ),
							array('table' => 'User',
								  'condition' => 'User.id=UserEvent.user_id',
								  'jointype' => ''
							  )
						);
						$event_det = $this->Api_model->get_datum('Event', 'id,name,description,city_id,latitude,longitude,place,starts_on', array('id' => $data['event_id']), TRUE, 'id asc');
						$event_type ='';
						$event_time=date("h:i A", strtotime($event_det->starts_on));
						$event_date=date("d-F-Y", strtotime($event_det->starts_on));
						// start  push notification code 
						$get_users = $this->Api_model->get_data('UserEvent', 'Push_Notification.fcm_regid,User.email,User.id,User.name', 
							array('UserEvent.event_id' => $data['event_id'],'UserEvent.user_id != '=>$data['user_id']), TRUE, 'UserEvent.id asc',$joins);

						if(isset($get_users) && !empty($get_users)) {
							$registation_ids=array();
							foreach ($get_users as $key) {
								if(isset($key->fcm_regid) && $key->fcm_regid !='null' && $key->fcm_regid !=''){
									$registation_ids[]=$key->fcm_regid;
								}
								if(isset($key->email) && $key->email !='null' && $key->email !=''){
									$this->load->library('mail');
									$subject = "Event Cancellation";
									$data['mail_gretting'] = "Dear ".$key->name;
									$data['mad_gretting'] = "";
									$data['mail_content'] = "We just had to cancel a meeting that has been on the calendar on ".$event_date." ".$event_time.".<br/>We apologise for any inconvenience caused.";
									$tracker_exist = $this->Api_model->get_datum('App_Event_Mail_Tracker', 'id', 
										array('receive_id' => $key->id,'item_id'=>$data['event_id'],'type'=>TYPE_CANCEL,'status'=>2), TRUE, 'id asc');
									if (isset($tracker_exist) && empty($tracker_exist)) {
										$tracker=array(
											'receive_id' =>$key->id,
											'item_id' =>$data['event_id'], 
											'type' =>TYPE_CANCEL,
											'email_id' => $key->email,
											'email_subject' =>$subject,
											// 'gtech_greating' =>$data['mail_gretting'],
											'email_greating' =>$data['mail_gretting'],
											'email_content' =>$data['mail_content'],
											// 'created_by' =>0,
											'status' =>2,
											'created_at' =>$this->device_validation->get_cur_date_time()
										);
										$this->Api_model->save_data('App_Event_Mail_Tracker',$tracker);
									}
								}
							}
				  
							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
							$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
							$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
							$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
							$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');

							if(isset($event_arr) && !empty($event_arr)){
								$user_choice = $event_arr->user_choice;
							} else {
								$user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
							}

							$message = array('status' => SUCCESS, "title" => "Event has been cancelled",'type'=>TYPE_CANCEL,'event_id' => $data['event_id'], 
												'event_name' => $event_det->name, 'event_details' => $event_det->description, 'event_date' => $event_date, 
												'event_location' => $event_det->place, 'event_type' => $event_type, 'latitude' => $event_det->latitude, 
												'longitude' => $event_det->longitude, 'event_city' => $event_det->city_id,'event_time'=>$event_time,
												'user_choice'=>$user_choice,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 
												'event_maybe_count' => $user_may_go_cnt,'event_cantgo_count' => $user_cantgo_cnt);

							$this->push_notification($registation_ids, $message);
							
							/*Send Mail Start*/
							$this->load->library('mail');
							$subject = "Event Cancellation";
							$data['mail_gretting'] = "Dear Member";
							$data['mad_gretting'] = "";
							$data['mail_content'] = "We just had to cancel a meeting that has been on the calendar on ".$event_date." ".$event_time.".<br/>We apologise for any inconvenience caused.";
							// $content = $this->load->view('mail_template',$data,TRUE);
							//$this->mail->send_email($email,$subject,$content);
							/*Send Mail End*/
						}
					} else {
						$results = array('status' => FAILURE);
						echo json_encode($results);
					}
				} else {
					$results = array('status' => FAILURE);
					echo json_encode($results);
				}
			}
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...');
			echo json_encode($results);
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	Function to invite Event.

	 * modified by
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        20-06-2017   
	 * Description:     Function to inviting events
	 * modified by
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:            30-06-2017   
	 * Description:     Send mail section
	 */
	public function invite_event() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['volunteer_ids1'] = $this->input->post_get('volunteer_ids');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$data['volunteer_ids']=json_decode($data['volunteer_ids1']);
			#$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			#Validation starts here
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$this->api_validation->api_data_validation($data['volunteer_ids'], array('required'));
			$validation = $this->api_validation->run();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED);
				echo json_encode($results);
			} else {
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id'])) && (isset($data['volunteer_ids']) && !empty($data['volunteer_ids']))) {
					$registatoin_ids=array();
					$volunteerid=array();
					foreach ($data['volunteer_ids'] as  $value) {
						
						$check_user_event=$this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'],'user_id'=>$value)); # Check user events 
						if(isset($value) && !empty($value) && isset($check_user_event) && ($check_user_event == 0)){
							$user_det = $this->Api_model->get_datum('User', 'id,name,email', array('id' => $value), TRUE, 'id asc');
							if(isset($user_det) && !empty($user_det)){
								$data['name']=$user_det->name;
								$data['email']=$user_det->email;
							} else {
								$data['name']='';
								$data['email']='';
							}

							$auth_key = random_string('alnum', 14);
							$user_arr=array('user_id'=>$value,'event_id'=>$data['event_id'],'created_from'=>APP,'created_on'=>$this->device_validation->get_cur_date_time(),
											'type'=>INVITED_EVENT,'present'=>INACTIVE,'rsvp_auth_key'=>$auth_key);
							$insertid[]=$this->Api_model->save_data('UserEvent',$user_arr);
							$volunteerid[]=$value;
							$get_users = $this->Api_model->get_data('Push_Notification', 'Push_Notification.fcm_regid', 
											array('Push_Notification.user_id'=>$value,'Push_Notification.status'=>ACTIVE), TRUE, 'Push_Notification.id asc');
							if(isset($get_users) && !empty($get_users)){
								foreach ($get_users as $key ) {
									if(isset($key->fcm_regid) && $key->fcm_regid !='null' && $key->fcm_regid !=''){
										$registatoin_ids[]=$key->fcm_regid;
									}
								}
							}
							$event_det = $this->Api_model->get_datum('Event', 'id,name,description,city_id,latitude,longitude,place,starts_on', array('id' => $data['event_id']), TRUE, 'id asc');
							$event_type ='';
							$event_time=date("h:i A", strtotime($event_det->starts_on));
							$event_date=date("d-F-Y", strtotime($event_det->starts_on));
							/*Send Mail Start*/
							$this->load->library('mail');
							$go_url=site_url('api/deep_linking_url').'?event_id='.$data['event_id'].'&rsvp='.GO.'&rsvp_auth_key='.$auth_key;
							$may_go_url=site_url('api/deep_linking_url').'?event_id='.$data['event_id'].'&rsvp='.MYBEGO.'&rsvp_auth_key='.$auth_key;
							$not_go_url=site_url('api/deep_linking_url').'?event_id='.$data['event_id'].'&rsvp='.CANTGO.'&rsvp_auth_key='.$auth_key;
							$subject = "Please join us";
							$data['mail_gretting'] = "Dear ".$data['name'];
							
						   // $data['mail_content'] = "It is indeed a great pleasure to invite you for ".$event_det->name. " in ".$event_det->place. " on ".$event_date." ".$event_time."Please confirm your presence at the Event. <a href='".$url."' style='text-decoration: underline;color:#058676'><b>RSVP<b></a> Looking forward to meet you at the Event.";
							$data['mail_content'] = "It is indeed a great pleasure to invite you for ".$event_det->name. " in ".$event_det->place. " on ".$event_date." ".$event_time." <br/>Please confirm your presence at the Event. <div style=\"text-align:center;padding:15px 0;\">
		<a href='".$go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>GOING</a><a href='".$may_go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>MAYBE</a><a href='".$not_go_url."' style='display:inline-block;padding:7px 15px;background-color:#ED1849;color:#fff;border-radius:4px;-webkit-border-radius:4px;margin:0 5px;'>CAN’T GO</a>
		</div> Looking forward to meet you at the Event.<br/>";
								#$data['mail_content']='Test';

							$content = $this->load->view('mail_template',$data,TRUE);
							$this->mail->send_email($data['email'],$subject,$content);
							$tracker_exist = $this->Api_model->get_datum('App_Event_Mail_Tracker', 'id', 
												array('receive_id' => $value,'item_id'=>$data['event_id'],'type'=>TYPE_INVITE,'status'=>2), TRUE, 'id asc');
							if (isset($tracker_exist) && empty($tracker_exist)) {
								$tracker=array(
									'receive_id' =>$value,
									'item_id' =>$data['event_id'], 
									'type' =>TYPE_INVITE,
									'email_id' => $data['email'],
									'email_subject' =>$subject,
									// 'gtech_greating' =>$data['mail_gretting'],
									'email_greating' =>$data['mail_gretting'],
									'email_content' =>$data['mail_content'],
									// 'created_by' =>0,
									'status' =>2,
									'created_at' =>$this->device_validation->get_cur_date_time()
								);
								$this->Api_model->save_data('App_Event_Mail_Tracker',$tracker);
							}
							/*Send Mail End*/ 
						}
					}

					$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
					$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
					$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
					$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
					$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');

					if(isset($event_arr) && !empty($event_arr)){
						$user_choice = $event_arr->user_choice;
					} else {
						$user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
					}

					if (isset($insertid) && !empty($insertid)) {
						$event_det = $this->Api_model->get_datum('Event', 'id,name,description,city_id,latitude,longitude,place,starts_on', array('id' => $data['event_id']), TRUE, 'id asc');
						if(isset($event_det) && !empty($event_det)){
							$event_type ='';
							$event_time=date("h:i A", strtotime($event_det->starts_on));
							$event_date=date("d-F-Y", strtotime($event_det->starts_on));
							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
							$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
							$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
							$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
							$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');
							if(isset($event_arr) && !empty($event_arr)){
								$user_choice = $event_arr->user_choice;
							} else {
								$user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
							}
							$results = array('status' => SUCCESS,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'user_choice'=>$user_choice,'volunteer_ids'=>$volunteerid,'event_cantgo_count' => $user_cantgo_cnt);
							echo json_encode($results);

							$message = array('status' => SUCCESS,"title" => "You are invited to the event",'type'=>TYPE_INVITE,'event_id' => $data['event_id'], 
												'event_name' => $event_det->name, 'event_details' => $event_det->description, 'event_date' => $event_date, 
												'event_location' => $event_det->place, 'event_type' => $event_type, 'latitude' => $event_det->latitude, 
												'longitude' => $event_det->longitude, 'event_city' => $event_det->city_id,'event_time'=>$event_time,
												'user_choice'=>$user_choice,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 
												'event_maybe_count' => $user_may_go_cnt,'event_cantgo_count' => $user_cantgo_cnt);
							$this->push_notification($registatoin_ids,$message);
						}    
					} else {
						$results = array('status' => FAILURE,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 
											'event_maybe_count' => $user_may_go_cnt,'user_choice'=>$user_choice,'volunteer_ids'=>$volunteerid,
											'event_cantgo_count' => $user_cantgo_cnt);
						echo json_encode($results);
					}

				} else {
					$results = array('status' => VALIDATION_FAILED);
					echo json_encode($results);
				}
			}
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...');
			echo json_encode($results);
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        19-06-2017   
	 * Description:     Function to mark attendance 
	 */
	
	public function mark_attendence() {
		#$valid = $this->device_validation->check_user_device();
		$valid = 1;
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['participants_ids_raw'] = $this->input->post_get('participants_ids');
			$data['hash_key'] = $this->input->post_get('hash_key');
			
			$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			#Validation starts here
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$this->api_validation->api_data_validation($data['participants_ids_raw'], array('required'));
		   
			$validation = $this->api_validation->run();

			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED);
				echo json_encode($results);
			} else {
			    $data['participants_ids'] = json_decode($data['participants_ids_raw']);

			    if (  (isset($data['user_id']) && !empty($data['user_id'])) 
				  && (isset($data['event_id']) && !empty($data['event_id'])) 
				  && (isset($data['participants_ids']) && !empty($data['participants_ids'])) ) {
					
					$present=DEFAULT_STATUS; //  set present value to o
					$late=DEFAULT_STATUS; // set late value to 0
					
					foreach ($data['participants_ids'] as  $value) {

						if($value->status == ATTENDED) // checking user attended the event or not
						{
						  $present=ACTIVE; // if attended set present value to 1
						  $late=DEFAULT_STATUS; // set late value to 0
						}
						if($value->status == LATE) // user attended but late in the event 
						{
						  $present=ACTIVE; // if attended set present value to 1
						  $late=ACTIVE; // set late value to 1
						}
						if($value->status == MISSED) // user attended but late in the event 
						{
						  $present=MISSED; // if attended set present value to 3
						  $late=DEFAULT_STATUS; // set late value to 0
						}
						
						if(isset($value->id) && !empty($value->id)){
							$user_arr=array('present'=>$present,'late'=>$late);
							$insertid[]=$this->Api_model->update('UserEvent',$user_arr,array('event_id' => $data['event_id'],'user_id'=>$value->id)); 
						}
						
					}
					if (isset($insertid) && !empty($insertid)) {
						$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$data['event_id']), TRUE, 'id asc');
								
						if(isset($event_arr) && !empty($event_arr)){
							$user_choice = $event_arr->user_choice;
						} else {
						  $user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
						}

						$ed = $this->Api_model->get_datum('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,Event.event_type_id', 
							array('Event.id' => $data['event_id']), TRUE, 'id asc');
						$eventdate=date("Y-m-d H:i:s", strtotime($ed->starts_on));
						$current_date=$this->device_validation->get_cur_date_time(TRUE,TRUE);
						if($eventdate >= $current_date) {
							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => GO));
							$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id']));
							$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => MYBEGO));
							$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'user_choice' => CANTGO));
						} else {
							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'present' => ACTIVE,'late'=>INACTIVE));
							$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'],'present' => ACTIVE,'late'=>ACTIVE));
							$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $data['event_id'], 'present' => MISSED,'late'=>DEFAULT_STATUS));
							$user_cantgo_cnt=0;
						}

						$results = array('status' => SUCCESS,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 
											'event_maybe_count' => $user_may_go_cnt,'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
							echo json_encode($results);
						} else {
							$results = array('status' => FAILURE);
							echo json_encode($results);
						}

				} else {
					$results = array('status' => VALIDATION_FAILED);
					echo json_encode($results);
				}
			  }
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...');
			echo json_encode($results);
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:		02-06-2017	 
	 * Description: 	
	 */
	public function mail_details() {
		$header = getAllHeaders();
		$this->load->library('mail');
		$agent = $header['User-Agent'];
		//$ip=$header['X-Real-Ip'];
		$ip = '';
		$subject = 'try to hack';
		$content = "Dear sir,<br/>The following ip and agents are try to hack your visit kerala webservice'.<br/>details : <br/>Agent:" . $agent . "<br/> Ip address:" . $ip;
		//$content = "Dear sir,<br/>The following ip and agents are try to hack your visit kerala webservice'.<br/>details : <br/>Agent:"$agent;
		$to = 'rejeeshknair@gmail.com';
		#$data['status'] = $this->mail->send_verification_mail($to, $subject, $content);
		$data['status'] = 1;
		return $data['status'];
	}

	 /**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        22-06-2017   
	 * Description:     Function to list attendance details on event 
	 */
	function attendance_details() {
		$valid = 1;
		$res_array=array();
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$data['status'] = $this->input->post_get('status');

			$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$this->api_validation->api_data_validation($data['status'], array('required'));
		   
			$validation = $this->api_validation->run();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
				echo json_encode($results);
			} else {
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id']))  && (isset($data['status']) && !empty($data['status']))) {
					$joins = array(
							array('table' => 'User',
								'condition' => 'User.id=UserEvent.user_id',
								'jointype' => 'LEFT'
							));

					$where=array();
					if($data['status'] == ATTENDED) {
						$where=array('User.status' => ACTIVE,'UserEvent.event_id' => $data['event_id'],'present'=>ACTIVE,'late'=>DEFAULT_STATUS);
					}
					if($data['status'] == LATE) {
						$where=array('User.status' => ACTIVE,'UserEvent.event_id' => $data['event_id'],'present'=>ACTIVE,'late'=>ACTIVE);
					}
					if($data['status'] == MISSED) {
						$where=array('User.status' => ACTIVE,'UserEvent.event_id' => $data['event_id'],'present'=>MISSED,'late'=>DEFAULT_STATUS);
					}
					$event_det = $this->Api_model->get_data('UserEvent', 'User.id,User.name,User.email,User.phone', $where, TRUE, 'User.name asc',$joins);
					if (isset($event_det) && !empty($event_det)) {
					   foreach ($event_det as $ed) {
							$id = $ed->id;
							$name = $ed->name;
							$email = $ed->email;
							$phone = $ed->phone;
						   
							$results_1 = array('id' => $id, 'name' => $name,'email' => $email, 'phone' => $phone);
							$res_array[] = $results_1;
						}
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results);
					} else {
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results); 
					}
				} else { 
					$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
					echo json_encode($results);
				}
			}
	    } else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...','user_array'=>$res_array);
			echo json_encode($results);
	    }
	}
	 /**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        22-06-2017   
	 * Description:     Function to list invited user details on event 
	 */
	function invited_user_details() {
		$valid = 1;
		$res_array=array();
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$validation = $this->api_validation->run();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
				echo json_encode($results);
			} else {
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id']))) {
					$joins = array(
							array('table' => 'User',
								'condition' => 'User.id=UserEvent.user_id',
								'jointype' => 'LEFT'
							));

					$event_det = $this->Api_model->get_data('UserEvent', 'User.id,User.name', array('UserEvent.event_id'=>$data['event_id']), TRUE, 'id asc',$joins);
					if (isset($event_det) && !empty($event_det)) {
					   foreach ($event_det as $ed) {
							$id = $ed->id;
							$name = $ed->name;
						   
							$results_1 = array('id' => $id, 'name' => $name);
							$res_array[] = $results_1;
						}
					    $results = array('status' => SUCCESS,'user_array'=>$res_array);
					    echo json_encode($results);
					} else {
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results); 
					}    
				} else {
					$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
					echo json_encode($results);
				}
			}
	  	} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...','user_array'=>$res_array);
			echo json_encode($results);
	  	}
	}
	
	 /**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K Nair
	 * @since           Version 1.0.0
	 * Date:        30-06-2017   
	 * Description:     Function to list city members 
	 */
	function city_members() {
		$valid = 1;
		$res_array=array();
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['city_id'] = $this->input->post_get('city_id');
			$data['center_id'] = $this->input->post_get('center_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$data['event_id'] = $this->input->post_get('event_id');
			#$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['city_id'], array('required'));
			$validation = $this->api_validation->run();
			
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
				echo json_encode($results);
			} else {
				if ((isset($data['user_id']) && !empty($data['user_id']))) {
					$joins = array();
					if(isset($data['center_id'])&& !empty($data['center_id'])){
						// $where = array('center_id'=>$data['center_id'],'city_id'=>$data['city_id'],'id !='=>$data['user_id'], 'status' => 1, 'user_type' => 'volunteer');
						$user_det = $this->Api_model->get_teachers_in_center($data['center_id']);
					} else {
						$where = array('city_id'=>$data['city_id'],'id !='=>$data['user_id'], 'status' => 1, 'user_type' => 'volunteer'); 
					}
					$event_user_det=array();
					$invite_arr=array();
					if(!$user_det) $user_det = $this->Api_model->get_data('User', 'id,title,name,email,phone,city_id,center_id', $where, TRUE, 'name asc',$joins);
					$event_user_det = $this->Api_model->get_data('UserEvent', 'user_id', array('event_id'=>$data['event_id']), TRUE, 'id asc',$joins);
					if(isset($event_user_det ) && !empty($event_user_det )){
						foreach ($event_user_det as  $value) {
							$invite_arr[]=$value->user_id;
						}
					}
					if (isset($user_det) && !empty($user_det)) {
						foreach ($user_det as $ud) {
							$id = $ud->id;
							$title = $ud->title;
							$name = $ud->name;
							$email = $ud->email;
							$phone = $ud->phone;
							$city_id = $ud->city_id;
							$center_id = $ud->center_id; // :TODO: This is not how you get the center_id - correct this.

							if(isset($event_user_det) && !empty($event_user_det) && !empty($invite_arr)){
								if(in_array($id,$invite_arr)){ 
								} else {
									$results_1 = array('id' => $id, 'title' => $title, 'name' => $name, 'email' => $email, 'phone' => $phone, 'city_id' => $city_id,'center_id'=>$center_id);
									$res_array[] = $results_1;
								}
							} else {
								$results_1 = array('id' => $id, 'title' => $title, 'name' => $name, 'email' => $email, 'phone' => $phone, 'city_id' => $city_id,'center_id'=>$center_id);
								$res_array[] = $results_1;
							}
						}
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results);
					} else {
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results); 
					}
				} else {
					$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
					echo json_encode($results);
				}
			}
	    } else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...','user_array'=>$res_array);
			echo json_encode($results);
	    }
	}
	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma RAjan
	 * @since           Version 1.0.0
	 * Date:        30-06-2017   
	 * Description:     Function to list event invited members 
	 */
	function invited_members() {
		$valid = 1;
		$res_array=array();
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$data['type'] = $this->input->post_get('type');
			$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
		   
			$validation = $this->api_validation->run();
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
				echo json_encode($results);
			} else {
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id']))) {
					$joins = array(
								  array('table' => 'UserEvent',
									  'condition' => 'UserEvent.user_id=User.id',
									  'jointype' => 'LEFT'
								  ));
					
					if(isset($data['type']) && $data['type'] == GO){ //  user list for marking rsvp option as GO
						$where=array('UserEvent.event_id'=>$data['event_id'],'UserEvent.user_choice'=>GO); 
					} else if(isset($data['type']) && $data['type'] == MYBEGO) {
						$where=array('UserEvent.event_id'=>$data['event_id'],'UserEvent.user_choice'=>MYBEGO);
					} else if(isset($data['type']) && $data['type'] == CANTGO) {
						$where=array('UserEvent.event_id'=>$data['event_id'],'UserEvent.user_choice'=>CANTGO); 
					} else {
						$where=array('UserEvent.event_id'=>$data['event_id']);
					}
				   
					$user_det = $this->Api_model->get_data('User', 'UserEvent.present,UserEvent.late,UserEvent.user_choice,UserEvent.reason,User.id,User.title,User.name,User.email,User.phone,User.city_id,User.center_id',
								$where, TRUE, 'User.name asc',$joins);
					if (isset($user_det) && !empty($user_det)) {
						foreach ($user_det as $ud) {
							$id = $ud->id;
							$title = $ud->title;
							$name = $ud->name;
							$email = $ud->email;
							$phone = $ud->phone;
							$city_id = $ud->city_id;
							$center_id = $ud->center_id; // :TODO: - This is not how you get the center - correct this.
							if($ud->present ==ACTIVE && $ud->late == DEFAULT_STATUS){
								$attendance_status=ATTENDED; // set as attended
							} else if($ud->present ==ACTIVE && $ud->late == ACTIVE){
								$attendance_status=LATE;  //set as late
							} else if($ud->present ==MISSED && $ud->late == DEFAULT_STATUS){
								$attendance_status=MISSED;  //set as missed
							} else {
								$attendance_status=DEFAULT_STATUS;  // Not marking attendance
							}
							$user_choice=$ud->user_choice;
							$reason=$ud->reason;
							
							$results_1 = array('id' => $id, 'title' => $title, 'name' => $name, 'email' => $email, 'phone' => $phone, 
									'city_id' => $city_id,'center_id'=>$center_id,'attendance_status'=>$attendance_status,'rsvp_choice'=>$user_choice,'reason'=>$reason);
							$res_array[] = $results_1;
						}
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results);
					} else {
						$results = array('status' => SUCCESS,'user_array'=>$res_array);
						echo json_encode($results); 
					}    
				} else {
					$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
					echo json_encode($results);
				}
			}
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...','user_array'=>$res_array);
			echo json_encode($results);
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:        26-06-2017   
	 * Description:     Function to get event details
	 */
	function event_detailed_view() {
		$valid = 1;
		$res_array=array();
		if ($valid == VALID_AGENTS) {
			$data['user_id'] = $this->input->post_get('user_id');
			$data['event_id'] = $this->input->post_get('event_id');
			$data['hash_key'] = $this->input->post_get('hash_key');
			$this->device_validation->check_hash_key_validation($data['user_id'], $data['hash_key']);
			$this->api_validation->api_data_validation($data['user_id'], array('required'));
			$this->api_validation->api_data_validation($data['event_id'], array('required'));
			$validation = $this->api_validation->run();
			//$validation = 1;
			if ($validation == VALIDATION_FAILED) {
				$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
				echo json_encode($results);
			} else {
				if ((isset($data['user_id']) && !empty($data['user_id'])) && (isset($data['event_id']) && !empty($data['event_id']))) {
					$ed = $this->Api_model->get_datum('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,Event.event_type_id', 
						array('Event.id' => $data['event_id']), TRUE, 'id asc');

					if (isset($ed) && !empty($ed)) {
						
						$event_id = $ed->event_id;
						$event_name = $ed->name;
						$event_details = $ed->description;
						$event_location = $ed->place;
						$event_type =$ed->event_type_id;
						$latitude = $ed->latitude;
						$longitude = $ed->longitude;
						$city_id = $ed->city_id;
						$event_time=date("h:i A", strtotime($ed->starts_on));
						$event_date=date("d-F-Y", strtotime($ed->starts_on));
						
						$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $data['user_id'],'event_id'=>$ed->event_id), TRUE, 'id asc');
						
						if(isset($event_arr) && !empty($event_arr)){
							$user_choice = $event_arr->user_choice;
						} else {
							$user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
						}
						$eventdate=date("Y-m-d H:i:s", strtotime($ed->starts_on));
						$current_date=$this->device_validation->get_cur_date_time(TRUE,TRUE);
					   
						if($eventdate >= $current_date)
						{
							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => GO));
							$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id));
							$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => MYBEGO));
							$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => CANTGO));
						} else {
							$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'present' => ACTIVE,'late'=>INACTIVE));
							 $user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id,'present' => ACTIVE,'late'=>ACTIVE));
							 $invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'present' => MISSED,'late'=>DEFAULT_STATUS));
							 $user_cantgo_cnt =0; 
						}
						
						$results = array('status' => SUCCESS,'event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 'latitude' => $latitude, 'longitude' => $longitude, 'event_city' => $city_id,'event_time'=>$event_time,'event_going_count' => $user_go_cnt, 'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'user_choice'=>$user_choice,'event_cantgo_count' => $user_cantgo_cnt);
						echo json_encode($results);
						die;
					} else {
						//$results = array('status' => FAILURE, 'event_id' => "", 'event_name' => "", 'event_details' => "", 'event_date' => "", 'event_location' => "", 'event_type' => "", 'latitude' => "", 'longitude' => "", 'event_city' => "", 'event_going_count' => "", 'event_invited_count' => "", 'event_maybe_count' => "");
						$results = array('status' => FAILURE);
						echo json_encode($results);
						die;
					}    
				} else {
					$results = array('status' => VALIDATION_FAILED,'user_array'=>$res_array);
					echo json_encode($results);
				}
			}
		} else {
			# $data['status'] = $this->mail_details();
			$results = array('status' => 'Failed try again...','user_array'=>$res_array);
			echo json_encode($results);
		}
	}
	
	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Rejeesh K.Nair
	 * @since           Version 1.0.0
	 * Date:            30-06-2017   
	 * Description:     Function to check Deep Linking URL
	 */
	public function deep_linking_url() {
		$event_id= $_GET['event_id'];
		$rsvp= $_GET['rsvp'];
		$rsvp_auth_key= $_GET['rsvp_auth_key'];
		if(isset($_GET['type']) && $_GET['type'] == APP){
			$type=$_GET['type'];
		} else {
			$_GET['type']='';
		}
	  
		$event_name='';
		$event_details='';
		$event_date='';
		$event_location='';
		$event_location;
		$event_type='';
		$latitude='';
		$longitude=''; 
		$event_city='';
		$event_time='';
		$event_going_count='';
		$event_invited_count='';
		$event_maybe_count='';
		$user_choice='';
		$user_cantgo_cnt='';
	  
		$user_det = $this->Api_model->get_datum('UserEvent', 'user_id', array('rsvp_auth_key' => $rsvp_auth_key,'event_id'=>$event_id), TRUE, 'id asc');
		if(isset($user_det) && !empty($user_det)){
			$where = array('user_id' => $user_det->user_id,'event_id' => $event_id);
			$update_array = array('user_choice' => $rsvp);
			$is_update = $this->Api_model->update("UserEvent", $update_array, $where);
			if(isset($is_update) && !empty($is_update)){
				$data['msg']='Your preference recorded successfully.';} else {
				$data['msg']='Something went wrong.';  
			}
			$ed = $this->Api_model->get_datum('Event', 'Event.id AS event_id,Event.name,Event.description,Event.starts_on,Event.city_id,Event.latitude,Event.longitude,Event.city_id,Event.place,Event.event_type_id', 
					array('Event.id' => $event_id), TRUE, 'id asc');
		
			if (isset($ed) && !empty($ed)) {
				$event_id = $ed->event_id;
				$event_name = $ed->name;
				$event_details = $ed->description;
				$event_location = $ed->place;
				$event_type =$ed->event_type_id;
				$latitude = $ed->latitude;
				$longitude = $ed->longitude;
				$city_id = $ed->city_id;
				$event_time=date("h:i A", strtotime($ed->starts_on));
				$event_date=date("d-F-Y", strtotime($ed->starts_on));

				$event_arr = $this->Api_model->get_datum('UserEvent', 'id,user_choice', array('user_id' => $user_det->user_id,'event_id'=>$ed->event_id), TRUE, 'id asc');

				if(isset($event_arr) && !empty($event_arr)){
					$user_choice = $event_arr->user_choice;
				} else {
					$user_choice = DEFAULT_STATUS; // user not choosing rsvp, status will be 0
				}

				$eventdate=date("Y-m-d H:i:s", strtotime($ed->starts_on));
				$current_date=$this->device_validation->get_cur_date_time(TRUE,TRUE);

				if($eventdate >= $current_date) {
					$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => GO));
					$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id));
					$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => MYBEGO));
					$user_cantgo_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'user_choice' => CANTGO));
				} else {
					$user_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'present' => ACTIVE,'late'=>INACTIVE));
					$user_may_go_cnt = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id,'present' => ACTIVE,'late'=>ACTIVE));
					$invited_user = $this->Api_model->count_detail('UserEvent', array('event_id' => $event_id, 'present' => MISSED,'late'=>DEFAULT_STATUS));
					$user_cantgo_cnt =0;
				}
			}

			if(isset($type) && $type ==APP){
				$results = array('status' => SUCCESS,'event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 
								'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 'latitude' => $latitude, 
								'longitude' => $longitude, 'event_city' => $city_id,'event_time'=>$event_time,'event_going_count' => $user_go_cnt, 
								'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'user_choice'=>$user_choice,
								'event_cantgo_count' => $user_cantgo_cnt);
				echo json_encode($results);die;
			} else { 
				$data['msg']='Success.';  
				$this->load->view('succees_page',$data);
			}
	   } else {
			if(isset($type) && $type ==APP){
				$results = array('status' => SUCCESS,'event_id' => $event_id, 'event_name' => $event_name, 'event_details' => $event_details, 
					'event_date' => $event_date, 'event_location' => $event_location, 'event_type' => $event_type, 'latitude' => $latitude, 
					'longitude' => $longitude, 'event_city' => $city_id,'event_time'=>$event_time,'event_going_count' => $user_go_cnt, 
					'event_invited_count' => $invited_user, 'event_maybe_count' => $user_may_go_cnt,'user_choice'=>$user_choice,
					'event_cantgo_count' => $user_cantgo_cnt);
				echo json_encode($results);die;
			} else {
				$data['msg']='Something went wrong.';  
				$this->load->view('succees_page',$data); 
			}
		}
	}

	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:    21-06-2017    
	 * Description:   push_notification
	 */
	public function push_notification($registration_ids,$message) {		
		$data = false;
		if(!empty($registration_ids)) {
			//Google cloud messaging GCM-API url
			$url = 'https://fcm.googleapis.com/fcm/send';
			$fields = array(
				'registration_ids' => $registration_ids,
				'data' => $message,
			);

			// Update your Google Cloud Messaging API Key
			// define("GOOGLE_API_KEY2", "AIzaSyCOsIxjMPfzN-X4b4HInmNTc3qBUp9jam8"); 
			define("GOOGLE_API_KEY2", "AAAA_jcFElg:APA91bHvMq8w7zoDafvLpYjSY2iAcF8ufDoOtAa5xuQrOxi7budFa7z2N6Jzv0b_5SOw7DMtZbXb1OScvMt49sUFV9UBvSasoWSjttFl2tCr8tqHj65y0LCjQwDwKaRYLhEMdrL6BIdM"); 
			$headers = array('Authorization: key=' . GOOGLE_API_KEY2, 'Content-Type: application/json');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
			if ($result === FALSE) {
				die('Curl failed: ' . curl_error($ch));
			}
			curl_close($ch);
			// let's check the response
			$data = json_decode($result);
			
			// dump($result, $data, $registration_ids, $message);
		}

		$log_file = '/home/makeadiff/public_html/apps/events-api/v1/log/api.log';
		$line = date("Y-m-d H:i:s") . ": " . json_encode($registration_ids) . " - " . $result . " - $message[title]\n";
		file_put_contents($log_file, $line, FILE_APPEND);

		return $data;
	}
	/**
	 * CodeIgniter
	 * @package         MAD App
	 * @author          Reshma Rajan
	 * @since           Version 1.0.0
	 * Date:    4-07-2017   
	 * Description:   send_email_common
	 */
	public function send_email_common() {   
		#$where         = array('status_msg'=>2);
		$limit = array('start'=>0,'length'=>50);
		$users=$this->Api_model->get_data('App_Event_Mail_Tracker','id,email_id,email_subject,email_greating,email_content,type','App_Events_MailTracker.status IN(2,3)',TRUE,'','',$limit);
		if(isset($users) && !empty($users)) {
			$i=0;
			foreach($users as $esdc) {      
				$this->load->library('mail'); 
			   echo $e_mailid   = $esdc->email_id;
				$email_subject  = $esdc->email_subject;
				$data['mail_gretting']  = $esdc->email_greating;
				$data['mail_content']   = $esdc->email_content;
				//$data['gtech_gretting']=$esdc->gtech_greating;
				if($e_mailid != '') {
					$content =  $this->load->view('mail_template',$data,TRUE);
					echo $is_mailsent= $this->mail->send_email($e_mailid,$email_subject,$content); 
				
					echo "<br>";
					if($is_mailsent) {
						$i++;
						$data_mail  = array('status'=>1,'response_mail'=>$is_mailsent);
						$this->Api_model->update('App_Event_Mail_Tracker',$data_mail,array('id'=> $esdc->id)); 
					} else {
						
						$data_mail  = array('status'=>3,'response_mail'=>3);
						$this->Api_model->update('App_Event_Mail_Tracker',$data_mail,array('id'=> $esdc->id)); 
						echo "<br>".$i.'fail';
						exit;
					}
				}
			}
			echo "<br>".$i;
		}
	}
}

/* End of file Api.php */