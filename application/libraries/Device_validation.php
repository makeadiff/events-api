<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter
 * @package         Visit Kerala
 * @category		Library
 * @author          Subin Sakariya
 * @copyright       Copyright (c) 2015 - 2016, OrisysIndia, LLP.
 * @link            http://orisys.in
 * @since           Version 1.0
 * @filesource
 */
class Device_validation {

    function check_user_device() {
        $this->CI = & get_instance();
        $header = getAllHeaders();
        $agents = $header['User-Agent'];
        $test = "Dalvik";
        // $test="Mozilla";
        //$test="Apache";
        if (strpos($agents, $test) !== false) {
            return 1;
        } else {
            return 0;
        }
    }

    function key_generator($imei) {
        $string = rand(10, 100);
        $imei_key = $imei . $string;
        $data['key'] = sha1($imei_key);
        return $data['key'];
    }

    //function check_user_exist($id,$imei,$hash)
    function check_user_exist($imei) {
        //$data['id'] = $id;
        $data['imei'] = $imei;
        //$data['key'] = $hash;
        $CI = & get_instance();
        $CI->load->database();
        $CI->load->model('Api_model');
        $active = $CI->Api_model->check_key($data);
        //1 => User active , 0 => Inactive/Unknown user
        return $active;
    }

    function check_user_valid($imei, $gcmregid) {
        $data['imei'] = $imei;
        $data['gcmregid'] = $gcmregid;
        $CI = & get_instance();
        $CI->load->database();
        $CI->load->model('Api_model2');
        $active = $CI->Api_model2->user_check_valid($data);
        //1 => User active , 0 => Inactive/Unknown user
        return $active;
    }

    function check_hash_key_validation($user_id, $hash_key) {
        $CI = & get_instance();
        $CI->load->database();
        $CI->load->model('Api_model');
        #$active = $CI->Api_model2->user_check_valid($data);
        $joins = array(
                            array('table' => 'Push_Notification',
                                'condition' => 'Push_Notification.user_id=User.id',
                                'jointype' => 'LEFT'
                            )
                        );
        $is_valid_hash_key = $CI->Api_model->get_datum("User", 'User.id', array('User.id' => $user_id, 'Push_Notification.hash_key' => $hash_key),
                TRUE,'User.id asc',$joins);
        if (isset($is_valid_hash_key) && !empty($is_valid_hash_key)) {
            
        } else {
            $results = array('status' => VALIDATION_FAILED);
            echo json_encode($results);
            die;
        }
    }

    /**
     * CodeIgniter
     * @package         GTech
     * @author          Rejeesh K.Nair
     * @since           Version 1.0
     * Date:            15-02-2017 
     * Description:     Function to show date or time with specific format 
     */
    function get_cur_date_time($format = TRUE, $time = TRUE) {
        date_default_timezone_set('Asia/Calcutta');
        if ($format) {
            if ($time) {
                return date('Y-m-d H:i:s');
            } else {
                return date('Y-m-d');
            }
        } else {
            if ($time) {
                return date('d/m/Y H:i:s');
            } else {
                return date('d/m/Y');
            }
        }
    }

    /**
     * CodeIgniter
     * @package         GTech
     * @author          Rejeesh K.Nair
     * @since           Version 1.0
     * Date:            13-03-2017 
     * Description:     Function to convert common date format.
     */
    function common_date_conversion($date, $type = '') {
        if (isset($date) && !empty($date)) {
            if ($type == 1) {
                return date('m-d-Y', strtotime($date));
            } else {
                return date('Y-m-d', strtotime($date));
            }
        }
    }

}
