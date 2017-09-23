<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:		07-07-2015	 
     * Description: 	Package controller constructor
     */
    function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'upload', 'form'));
        $this->load->library('form_validation');
        $this->load->library('api_validation');
        $this->load->library('session');
        $this->load->model('cron_model');
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          Chinnu
     * @since           Version 1.0.0
     * Date:		22-8-2016	 
     * Description: 	Send Push notification.
     */
    public function push_noti() {
        $data['crr_date'] = date("Y-m-d");
        $data['notification_types'] = '';
        $nouser = 0;
        $data['push_id'] = $this->input->post('push_id');
        $data['details'] = $this->cron_model->get_cron_details1($data);
        if (!empty($data['details'])) {
            foreach ($data['details'] as $row) {
                $data['pack_id'] = $row->pack_id;
                $data['msg_id'] = $row->id;
                $data['user_type'] = $row->user_type;
                $data['msg'] = unserialize($row->message);
                $data['push_packet'] = $row->notification_type;
                $data['parent_id'] = $row->parent_id;
                $data['user_id'] = $row->user_id;
                $data['otp_verified'] = $row->otp_verified;
                $data['app_version'] = $row->app_version;
                $data['status'] = $row->status;
//                $data['send_users'] = $row->user_count;
            }
//            echo "dd"; exit;
            if ($data['parent_id'] != 0 && $data['user_id'] != '' && $data['status'] == 2) {
                //To Resend once failed GCM ids
                $data['users'] = unserialize($data['user_id']);
            } else {
                if (isset($data['msg']['img_url']) && !empty($data['msg']['img_url'])) {
                    $img_url = $data['msg']['img_url'];
                } else {
                    $img_url = '';
                }
                $data['offset'] = $data['msg']['from'];
                $data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => $img_url, "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
                if (isset($data['user_type']) && $data['user_type'] != '') {
                    if ($data['user_type'] == REGISTERD) {
                        $data['users'] = $this->cron_model->get_mobile_users($data);
                    } elseif ($data['user_type'] == UNREGISTERD) {
                        $data['users'] = $this->cron_model->get_unreg_users($data);
                    } elseif ($data['user_type'] == REGISTERD_NEVER_BID) {
                        $data['packid'] = $data['pack_id'];
                        $data['users'] = $this->cron_model->get_mob_selected_user($data);

                    } elseif ($data['user_type'] == ALL_USERS) {
                       $data['users'] = $this->cron_model->get_all_users($data);
                        
                     /*  if($data['app_version']=='3.0.0')
                       {
                           $data['users1'] = $this->cron_model->get_all_users($data);
                           $data['users2'] = $this->cron_model->get_all_users($data);
                           $data['users3'] = $this->cron_model->get_all_users($data);
                           $data['users4'] = $this->cron_model->get_all_users($data);
                           $data['users5'] = $this->cron_model->get_all_users($data);
                           $data['users6'] = $this->cron_model->get_all_users($data);
                           $data['users7'] = $this->cron_model->get_all_users($data);
                           $data['users8'] = $this->cron_model->get_all_users($data);
                           $data['users9'] = $this->cron_model->get_all_users($data);
                           $data['users10'] = $this->cron_model->get_all_users($data);
                           $data['users11'] = $this->cron_model->get_all_users($data);
                           $data['users12'] = $this->cron_model->get_all_users($data);
                           $data['users13'] = $this->cron_model->get_all_users($data);
                           $data['users14'] = $this->cron_model->get_all_users($data);
                           $data['users15'] = $this->cron_model->get_all_users($data);
                           $data['users16'] = $this->cron_model->get_all_users($data);
                           $data['users17'] = $this->cron_model->get_all_users($data);
                           $data['users18'] = $this->cron_model->get_all_users($data);
                           $data['users19'] = $this->cron_model->get_all_users($data);
                           $data['users20'] = $this->cron_model->get_all_users($data);
                           $data['users21'] = $this->cron_model->get_all_users($data);
                           $data['users22'] = $this->cron_model->get_all_users($data);
                           $data['users23'] = $this->cron_model->get_all_users($data);
                           $data['users24'] = $this->cron_model->get_all_users($data);
                           $data['users25'] = $this->cron_model->get_all_users($data);
                           $data['users26'] = $this->cron_model->get_all_users($data);
                           $data['users27'] = $this->cron_model->get_all_users($data);
                           
                          
                           $data['users'] = array_merge($data['users1'], $data['users2'], $data['users3'], $data['users4'], $data['users5'], $data['users6'], $data['users7'], $data['users8'], $data['users9'], $data['users10'], $data['users11'], $data['users12'], $data['users13'], $data['users14'], $data['users15'], $data['users16'], $data['users17'], $data['users18'], $data['users19'], $data['users20'],$data['users21'], $data['users22'], $data['users23'], $data['users24'], $data['users25'], $data['users26'], $data['users27']);
                       }
                       else 
                       {
                           $data['users'] = $this->cron_model->get_all_users($data);
                       }*/
                    }
                } else {
                    $data['users'] = $this->cron_model->get_all_users($data);

                }
                if(isset($data['users'])&& !empty($data['users']))
                {
                    $data['user_details'] = serialize($data['users']);
                    $update_stat = $this->cron_model->update_user_details($data,UPDATE_GCM_STAT);
                }
                else 
                {
                    $data['user_details'] = '';
                    $nouser = 1;
                    $update_stat = $this->cron_model->update_user_details($data,7);
                }
                
            }
            if($nouser==0)
            {
                    //die('Stop Here...');
        //            print_r($data['users']);
        //            exit;
                    $da['failed'] = array();
                    $da['canonical'] = array();
                    $da['failure'] = 0;
                    $da['success'] = 0;
                    $i = 0;
                    $j = 0;
                    $da = $this->send_push_notification($data);
                    print_r($da);
                    if (!empty($da['failed'])) {
        //                $object = new stdClass();
                        foreach ($da['failed'] as $key => $value) {
                            $object = new stdClass();
                            $object->gcm_id = $value;
                            $fail[$i++] = $object;
                        }
                        $data['failed_gcm'] = serialize($fail);
                        $data['serialize_msg'] = serialize($data['msg']);
        //                foreach ($da['failed'] as $value)
        //                {
        //                    echo $value."<br><br>";
        //                }exit;
                        if ($data['parent_id'] != 0 && $data['user_id'] != '' && $data['status'] == 2) {
                            $data['stat'] = 4;
                            $f_status = $this->cron_model->insert_failed_gcm($data);
                            $f_status1 = $this->cron_model->update_failed_user_status($da);
                        } else {
                            $data['stat'] = 2;
                            $f_status = $this->cron_model->insert_failed_gcm($data);
                        }
                    }
                    if (!empty($da['canonical'])) {
                        $update_stat = $this->cron_model->update_with_canonical_id($da);
                    }
                    $data['success'] = $da['success'];
                    $data['failure'] = $da['failure'];
                    if($data['failure']==''||$data['failure']==NULL)
                    {
                        $data['failure']=0;
                    }
        //            $data['error_gcm'] = $da['error'];
                    $update_stat = $this->cron_model->update_results($data);
            }
            else 
            {
                echo "No user";
            }
        } else {
            echo "Failed";
        }
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          Chinnu
     * @since           Version 1.0.0
     * Date:		26-8-2016	 
     * Description: 	Activate new package
     */
    public function package_activate() {
        $pack_status = $this->cron_model->check_package_end_time();
//        print_r($pack_status);
        if ($pack_status['status'] == 1 || $pack_status['status'] == 2) {
            //No Active package OR Active package expired
             $new_active_pack = $this->cron_model->activate_latest_package($pack_status['pack_id']);
            if (isset($new_active_pack) && $new_active_pack != FALSE) {
                $new_active_pack = $this->cron_model->update_package_push_status($new_active_pack);
//                echo "ss";
            }
        }
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:		22-10-2015	 
     * Description: 	Send Push notification.
     */
    public function push_noti_old() {
        $data['crr_date'] = date("Y-m-d");
        $data['notification_types'] = REGISTERD;
        $data['details'] = $this->cron_model->get_cron_details1($data);
        if (!empty($data['details'])) {
            //$b= print_r($data['details'],true);
            //mail("subin.s@orisys.in","Push_notification_status",$b);
            foreach ($data['details'] as $row) {
                $data['pack_id'] = $row->pack_id;
                $data['msg_id'] = $row->id;
                $data['user_type'] = $row->user_type;
                $data['msg'] = unserialize($row->message);
                $data['push_packet'] = $row->notification_type;
            }

            if (($data['user_type'] == PUSH_WINNERS) || ($data['user_type'] == ACTIVEUSER)) {
                $data['user_count'] = $this->cron_model->get_mobile_users_count();
            } else {
                $data['packid'] = $data['pack_id'];
                $user_count = $this->cron_model->get_mob_selected_user_count($data);
                $data['user_count'] = count($user_count);
            }
            $data['instance_val'] = ($data['user_count']) / PACKET;
            $data['instance'] = ceil($data['instance_val']);
            //$start=
            $data['offset'] = $data['msg']['from'];
            #echo $data['msg']['img_url'];die;
            #$data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => $data['msg']['img_url'], "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
            $data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => '', "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
            $n = $data['instance'];
            for ($i = 0; $i < $n; $i++) {
                if (($data['user_type'] == PUSH_WINNERS) || ($data['user_type'] == ACTIVEUSER)) {
                    $data['users'] = $this->cron_model->get_mobile_users($data);
                } else {
                    $data['packid'] = $data['pack_id'];
                    $data['users'] = $this->cron_model->get_mob_selected_user($data);
                }
                $data['user_details'] = serialize($data['users']);
                //$a= print_r($data['users'],true);
                //mail("subin.s@orisys.in","Push_notification_status",$a);
                $update_stat = $this->cron_model->update_user_details($data);
            }
            $status = $this->send_push_notification($data);
            print_r($status);
            //print_r($data['users']);
        } else {
            echo "Failed";
        }
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:		22-10-2015	 
     * Description: 	Send Push notification.
     */
    public function unregistred_push() {
        $data['crr_date'] = date("Y-m-d");
        $data['notification_types'] = UNREGISTERD;
        $data['details'] = $this->cron_model->get_cron_details1($data);
        if (!empty($data['details'])) { //$b= print_r($data['details'],true);
            //mail("subin.s@orisys.in","Push_notification_status",$b);
            foreach ($data['details'] as $row) {
                $data['msg_id'] = $row->id;
                $data['user_type'] = $row->user_type;
                $data['msg'] = unserialize($row->message);
                $data['push_packet'] = $row->notification_type;
            }
            $data['user_count'] = $this->cron_model->get_unreg_users_count();
            $data['instance_val'] = ($data['user_count']) / PACKET;
            $data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => $data['msg']['img_url'], "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
            $data['instance'] = ceil($data['instance_val']);
            //$start=
            $data['offset'] = $data['msg']['from'];
            #echo $data['msg']['img_url'];die;
            $n = $data['instance'];
            for ($i = 0; $i < $n; $i++) {
                $data['users'] = $this->cron_model->unreg_users($data);
                $data['user_details'] = serialize($data['users']);
                //$a= print_r($data['users'],true);
                //mail("subin.s@orisys.in","Push_notification_status",$a);
                $update_stat = $this->cron_model->update_user_details($data);
            }
            $status = $this->send_push_notification($data);
            print_r($status);
        } else {
            echo "Failed";
        }
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:		28-10-2015	 
     * Description: 	Send Push notification manually
     */
    public function push_send() {
        $data['push_id'] = $this->input->post('push_id');
        $data['details'] = $this->cron_model->get_cron_details_manually($data);
        if (!empty($data['details'])) {
            foreach ($data['details'] as $row) {
                $data['msg_id'] = $row->id;
                $data['user_type'] = $row->user_type;
                $data['msg'] = unserialize($row->message);
                $data['push_packet'] = $row->notification_type;
            }
            $data['user_count'] = $this->cron_model->get_unreg_users_count();
            $data['instance_val'] = ($data['user_count']) / PACKET;
            $data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => $data['msg']['img_url'], "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
            $data['instance'] = ceil($data['instance_val']);
            $data['offset'] = $data['msg']['from'];
            $n = $data['instance'];
            for ($i = 0; $i < $n; $i++) {
                $data['users'] = $this->cron_model->unreg_users($data);
                $data['user_details'] = serialize($data['users']);
                $update_stat = $this->cron_model->update_user_details($data);
            }
            $status = $this->send_push_notification($data);
            // print_r($status);
        } else {
            echo "Failed";
        }
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		11-8-2015	 
     * Description: 	Send Push notification manually
     */
    public function push_send_registered() {
        $data['push_id'] = $this->input->post('push_id');
        $data['details'] = $this->cron_model->get_cron_details_reg_users($data);
        if (isset($data['details']) && !empty($data['details'])) {
            foreach ($data['details'] as $row) {
                $data['pack_id'] = $row->pack_id;
                $data['msg_id'] = $row->id;
                $data['user_type'] = $row->user_type;
                $data['msg'] = unserialize($row->message);
                $data['push_packet'] = $row->notification_type;
            }

            if (($data['user_type'] == PUSH_WINNERS) || ($data['user_type'] == ACTIVEUSER)) {
                $data['user_count'] = $this->cron_model->get_mobile_users_count();
            } else {
                $data['packid'] = $data['pack_id'];
                $user_count = $this->cron_model->get_mob_selected_user_count($data);
                $data['user_count'] = count($user_count);
            }
            $data['instance_val'] = ($data['user_count']) / PACKET;
            $data['instance'] = ceil($data['instance_val']);
            //$start=
            $data['offset'] = $data['msg']['from'];
            #echo $data['msg']['img_url'];die;
            $data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => $data['msg']['img_url'], "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
            $n = $data['instance'];
            for ($i = 0; $i < $n; $i++) {
                if (($data['user_type'] == PUSH_WINNERS) || ($data['user_type'] == ACTIVEUSER)) {
                    $data['users'] = $this->cron_model->get_mobile_users($data);
                } else {
                    $data['packid'] = $data['pack_id'];
                    $data['users'] = $this->cron_model->get_mob_selected_user($data);
                }
                $data['user_details'] = serialize($data['users']);
                //$a= print_r($data['users'],true);
                //mail("subin.s@orisys.in","Push_notification_status",$a);
                $update_stat = $this->cron_model->update_user_details($data);
            }
            $status = $this->send_push_notification($data);
            #print_r($status);
            //print_r($data['users']);
        } else {
            echo "Failed";
        }
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:			22-10-2015	 
     * Description: 	Send Push notification.
     */
    public function send_push_notification($data) {
        //$registatoin_ids[]='APA91bE6LFpniBlnmZNQdCGwViPwywHXH7aTJegZptQpVwAJBsFaw5WZ5FZFYDUrZRstMn1_NmQSSN4r-y7_g3Rl42kJVf1Y2RdBExcwDvLtLmzbgg7_eMxPgyUj3LTlW1TMMirh6oMN';
        $da['failed'] = array();
        $da['canonical'] = array();
        $da['failure'] = 0;
        $da['success'] = 0;
        $da['error'] = '';
        $i = 0;
        $j = 0;

        // Update your Google Cloud Messaging API Key
        define("GOOGLE_API_KEY", "AIzaSyBzqoScwGUiTU_2Dg6O8g38xXo1rtttrUs");

        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY, 'Content-Type: application/json');
        //Google cloud messaging GCM-API url
        $url = 'https://android.googleapis.com/gcm/send';
        print_r($data['users']);
        foreach ($data['users'] as $row) {
            $registatoin_ids = array();
            $gcm_id = $row->gcm_id;
            if ($gcm_id != '') {
                $registatoin_ids[] = $gcm_id;
            }
            if (!empty($registatoin_ids)) {
                $msg = json_encode($data['pushMessage']);
                $message = json_decode($msg);

                $fields = array(
                    'registration_ids' => $registatoin_ids,
                    'data' => $message,
                );
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
                    $da['error'] = $da['error'].$gcm_id.',';
//                    return $da;
//                    die('Curl failed: ' . curl_error($ch));
                }
                curl_close($ch);
                $resl = json_decode($result);
                print_r($resl);
                echo "<br><br>";
                $i++;
                $da['canonical_ids'] = $resl->canonical_ids;
                $success = $resl->success;
                $failure = $resl->failure;
                if ($failure > 0) {
                    $da['failed'][] = $gcm_id;
                    $da['failure']++;
                    
                } else {
                    $da['success']++;
                }
                if ($da['canonical_ids'] > 0) {
                    foreach ($resl->results as $key => $value) {
                        if (isset($value->registration_id) && $value->registration_id!='') {
                            $da['canonical'][$j]['gcm_id'] = $gcm_id;
                            $da['canonical'][$j]['canonical_ids'] = $value->registration_id;
                            $j++;
                        }
                    }
                }
            }
        }
        return $da;
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:			22-10-2015	 
     * Description: 	Send Push notification.
     */
    public function send_push_notification_old($data) {
        //$registatoin_ids[]='APA91bE6LFpniBlnmZNQdCGwViPwywHXH7aTJegZptQpVwAJBsFaw5WZ5FZFYDUrZRstMn1_NmQSSN4r-y7_g3Rl42kJVf1Y2RdBExcwDvLtLmzbgg7_eMxPgyUj3LTlW1TMMirh6oMN';

        foreach ($data['users'] as $row) {
            $gcm_id = $row->gcm_id;
            if ($gcm_id != '') {
                $registatoin_ids[] = $gcm_id;
            }
        }
        print_r($registatoin_ids);
        echo "<br><br>";
        //$registatoin_ids[]='APA91bE6LFpniBlnmZNQdCGwViPwywHXH7aTJegZptQpVwAJBsFaw5WZ5FZFYDUrZRstMn1_NmQSSN4r-y7_g3Rl42kJVf1Y2RdBExcwDvLtLmzbgg7_eMxPgyUj3LTlW1TMMirh6oMN';
        if (!empty($registatoin_ids)) {
            $msg = json_encode($data['pushMessage']);
            $message = json_decode($msg);

            //Google cloud messaging GCM-API url
            $url = 'https://android.googleapis.com/gcm/send';
            $fields = array(
                'registration_ids' => $registatoin_ids,
                'data' => $message,
            );

            // Update your Google Cloud Messaging API Key
            // define("GOOGLE_API_KEY", "AIzaSyB7NWlAKhg9DqidoASy1qPcGyiRrI4CYGc"); 	
            define("GOOGLE_API_KEY", "AIzaSyBzqoScwGUiTU_2Dg6O8g38xXo1rtttrUs");

            $headers = array(
                'Authorization: key=' . GOOGLE_API_KEY, 'Content-Type: application/json');

            //         print_r($fields);
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
            $resl = json_decode($result);

            $data['success'] = $resl->success;
            $data['failure'] = $resl->failure;
            $update_stat = $this->cron_model->update_results($data);
            return $resl;
        }
        return;
    }

    public function test() {
        $registatoin_ids[] = 'APA91bEIraCmqcoPXqXTTFyPwi3rTJZl-oBAVkPdGRMo5yb_dI-BktEkMpbV87gNlSvnP3PXlsdkRVafv0ZoKGUzyPuUcDPmflTd1KVivD4ZO2V5fhZHWQu-wGY__MOB5K1QCdBmARup';
        $data['pushMessage'] = 'test for single';
        print_r($registatoin_ids);
        echo "<br><br>";
        if (!empty($registatoin_ids)) {
            $msg = json_encode($data['pushMessage']);
            $message = json_decode($msg);

            //Google cloud messaging GCM-API url
            $url = 'https://android.googleapis.com/gcm/send';
            $fields = array(
                'registration_ids' => $registatoin_ids,
                'data' => $message,
            );

            // Update your Google Cloud Messaging API Key
            define("GOOGLE_API_KEY", "AIzaSyBzqoScwGUiTU_2Dg6O8g38xXo1rtttrUs");

            $headers = array(
                'Authorization: key=' . GOOGLE_API_KEY, 'Content-Type: application/json');
            //         print_r($fields);
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
            $resl = json_decode($result);
            print_r($resl);
//            $data['success'] = $resl->success;
//            $data['failure'] = $resl->failure;
//            return $resl;
        }
//        return;
    }
    public function push_noti_one_signal() {
        $data['crr_date'] = date("Y-m-d");
        $data['notification_types'] = '';
        $nouser = 0;
        $data['push_id'] = $this->input->post('push_id');
        $data['details'] = $this->cron_model->get_cron_details1($data);
        print_r($data['details']);
        if (!empty($data['details'])) {
            foreach ($data['details'] as $row) {
                $data['pack_id'] = $row->pack_id;
                $data['msg_id'] = $row->id;
                $data['user_type'] = $row->user_type;
                $data['msg'] = unserialize($row->message);
                $data['push_packet'] = $row->notification_type;
                $data['parent_id'] = $row->parent_id;
                $data['user_id'] = $row->user_id;
                $data['otp_verified'] = $row->otp_verified;
                $data['app_version'] = $row->app_version;
                $data['status'] = $row->status;
//                $data['send_users'] = $row->user_count;
            }
//            echo "dd"; exit;
            if ($data['parent_id'] != 0 && $data['user_id'] != '' && $data['status'] == 2) {
                //To Resend once failed GCM ids
                $data['users'] = unserialize($data['user_id']);
            } else {
                if (isset($data['msg']['img_url']) && !empty($data['msg']['img_url'])) {
                    $img_url = $data['msg']['img_url'];
                } else {
                    $img_url = '';
                }
                $data['offset'] = $data['msg']['from'];
                $data['pushMessage'] = array("title" => PUSH_NOTI_TITLE, "message" => $data['msg']['message'], "type" => $data['msg']['type'], "img_url" => $img_url, "package_id" => $data['msg']['package_id'], "price" => $data['msg']['price']);
                if (isset($data['user_type']) && $data['user_type'] != '') {
                    if ($data['user_type'] == REGISTERD) {
                        $data['users'] = $this->cron_model->get_mobile_users($data);
                    } elseif ($data['user_type'] == UNREGISTERD) {
                        $data['users'] = $this->cron_model->get_unreg_users($data);
                    } elseif ($data['user_type'] == REGISTERD_NEVER_BID) {
                        $data['packid'] = $data['pack_id'];
                        $data['users'] = $this->cron_model->get_mob_selected_user($data);

                    } elseif ($data['user_type'] == ALL_USERS) {
                       $data['users'] = $this->cron_model->get_all_users($data);
                    }
                } else {
                    $data['users'] = $this->cron_model->get_all_users($data);

                }
                print_r($data['users']);
                if(isset($data['users'])&& !empty($data['users']))
                {
                    $data['user_details'] = serialize($data['users']);
//                    $update_stat = $this->cron_model->update_user_details($data,UPDATE_GCM_STAT);
                }
                else 
                {
                    $data['user_details'] = '';
                    $nouser = 1;
                    $update_stat = $this->cron_model->update_user_details($data,7);
                }
                
            }
            if($nouser==0)
            {
                    //die('Stop Here...');
        //            print_r($data['users']);
        //            exit;
                    $da['failed'] = array();
                    $da['canonical'] = array();
                    $da['failure'] = 0;
                    $da['success'] = 0;
                    $i = 0;
                    $j = 0;
                    $da = $this->send_one_signal($data);
                    print_r($da);
                   /* if (!empty($da['failed'])) {
        //                $object = new stdClass();
                        foreach ($da['failed'] as $key => $value) {
                            $object = new stdClass();
                            $object->gcm_id = $value;
                            $fail[$i++] = $object;
                        }
                        $data['failed_gcm'] = serialize($fail);
                        $data['serialize_msg'] = serialize($data['msg']);
        //                foreach ($da['failed'] as $value)
        //                {
        //                    echo $value."<br><br>";
        //                }exit;
                        if ($data['parent_id'] != 0 && $data['user_id'] != '' && $data['status'] == 2) {
                            $data['stat'] = 4;
                            $f_status = $this->cron_model->insert_failed_gcm($data);
                            $f_status1 = $this->cron_model->update_failed_user_status($da);
                        } else {
                            $data['stat'] = 2;
                            $f_status = $this->cron_model->insert_failed_gcm($data);
                        }
                    }
                    if (!empty($da['canonical'])) {
                        $update_stat = $this->cron_model->update_with_canonical_id($da);
                    }
                    $data['success'] = $da['success'];
                    $data['failure'] = $da['failure'];
                    if($data['failure']==''||$data['failure']==NULL)
                    {
                        $data['failure']=0;
                    }
        //            $data['error_gcm'] = $da['error'];
                    $update_stat = $this->cron_model->update_results($data);*/
            }
            else 
            {
                echo "No user";
            }
        } else {
            echo "Failed";
        }
    }
     public function send_one_signal($data) 
     {
        foreach ($data['users'] as $row) {
            $gcm_id = $row->gcm_id;
            if ($gcm_id != '') {
                $registatoin_ids[] = $gcm_id;
            }
        }
//        $msg = json_encode($data['pushMessage']);
//            $message = json_decode($msg);
             $content = $data['pushMessage'];
             $content['en'] = 'Chinnu';
//         $content = array(
//            "en" => 'Title'
//            );

          $fields = array(
            'app_id' => "7e9b4978-082a-49bd-89c4-75b77563833d",
//            'included_segments' => array('All'),
            'include_android_reg_ids' =>$registatoin_ids,
            'data' => $data['pushMessage'],
            'contents' => $content
          );

          $fields = json_encode($fields);
          print("\nJSON sent:\n");
          print($fields);

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                 'Authorization: Basic NDE0ZTE5ODQtZjVlMi00ZTdjLThkZTYtZWIxOGJkYzRlOTIz'));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
          curl_setopt($ch, CURLOPT_HEADER, FALSE);
          curl_setopt($ch, CURLOPT_POST, TRUE);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

          $response = curl_exec($ch);
          curl_close($ch);

          return $response;
     }
}

/* End of file package.php */