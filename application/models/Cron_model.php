<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter
 * An open source applicaon development framework for PHP 4.3.2 or newer
 *
 * @package        Visit Kerala
 * @author         SUBIN SAKARIYA
 * @since           Version 1.0
 * @date            09-07-2015
 * @filesourme 
 */
class Cron_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            21-10-2015
     * Description:     get pushnotification details.
     */
    public function get_cron_details($data) {
        $this->db->select('id,message,user_type,push_pack,image,instance');
        $this->db->from('gcm_notification');
        $this->db->like('created_at', $data['crr_date']);
        $this->db->order_by("id", "desc");
        $this->db->limit(LIMIT);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * CodeIgniter
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0.0
     * Date:			16-09-2015	 
     * Description: 	Push notification for winners
     */
    public function latest_winner($data) {
        $this->db->select('winner.user_id,winner.amount,user.user_name,package.id,package.package_name,package.package_price,attachments.image,attachments.main');
        $this->db->from('winner');
        $this->db->join('user', 'user.id = winner.user_id', 'left');
        $this->db->join('package', 'package.id = winner.pack_id', 'left');
//        $this->db->join('attachments', 'attachments.package_id = package.id', 'left');
        $this->db->join('attachments', 'attachments.package_id = package.id AND attachments.main=' . MAIN_ATTACH, 'left');
        if (isset($data['pack_id']) && $data['pack_id'] != '' && $data['pack_id'] != NULL) {
            $this->db->where('winner.pack_id', $data['pack_id']);
        }
        $this->db->where('package.package_status!=', ACTIVE);
//        $this->db->where('attachments.main', MAIN_ATTCH);
        $this->db->order_by("winner.updated_at", "desc");
        $this->db->limit(LIMIT);
        $content = $this->db->get()->result();
        return $content;
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            21-10-2015
     * Description:     get pushnotification details.
     */
    public function get_mobile_users_count($otp_verified = '',$app_version=NULL) {
        $this->db->select('mobile_api.gcm_id,mobile_api.id');
        $this->db->from('mobile_api');
        $this->db->join('user', 'user.id = mobile_api.user_id');
        if (isset($otp_verified) && $otp_verified != '' && $otp_verified == 1) {
            $this->db->where('user.status=', 1);
        }
        if (isset($app_version) && $app_version != NULL && $app_version != '' && $app_version==1) {
            $this->db->where('user.app_version', '');
        }
        else if (isset($app_version) && $app_version != NULL && $app_version != '')
        {
            $this->db->like('user.app_version', $app_version);
        }
        $this->db->where('mobile_api.status', ACTIVE_PUSH_USER);
        $this->db->where('mobile_api.gcm_status', ACTIVE_PUSH_USER);
        $this->db->where('mobile_api.gcm_id!=', '');
        $content = $this->db->get();
//        print_r($content->result());
        return count($content->result());
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            21-10-2015
     * Description:     get pushnotification limited packet gcm_id.
     */
    public function get_mobile_users($data) {
        /* if($data['push_packet']==FIRST_INSTANCE)
          {
          $offset =FIRST_INSTANCE;
          }
          else
          {
          $offset = (PACKET * $data['push_packet'])+NEXT_ID;
          } */
        $num = PACKET;
        $this->db->select('gcm_id');
        $this->db->from('mobile_api');
        $this->db->join('user', 'user.id = mobile_api.user_id');
        if (isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1) {
            $this->db->where('user.status=', 1);
        }
        if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '' && $data['app_version']==1) {
            $this->db->where('user.app_version', '');
        }
        else if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')
        {
            $this->db->like('user.app_version', $data['app_version']);
        }
        $this->db->where('mobile_api.status', ACTIVE_PUSH_USER);
        $this->db->where('mobile_api.gcm_status', ACTIVE_PUSH_USER);
        $this->db->where('gcm_id!=', '');
        $this->db->limit($num, $data['offset']);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            22-10-2015
     * Description:    Update count for each packet passing instance.
     */
    public function update_instance($data) {
        $instance = $data['inst'];
        $detil = array(
            'instance' => $instance
        );
        $this->db->where('id', $data['Noti_id']);
        $this->db->limit(LIMIT);
        $this->db->update('gcm_notification', $detil);
        return ($this->db->affected_rows());
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            22-10-2015
     * Description:     get all mobile user for push notification
     */
    public function cron_get_mobile_users($i) {
        $num = 1;
        $offset = $i + $num;
        $this->db->select('user_id,imei,gcm_id');
        $this->db->from('mobile_api');
        $this->db->where('status!=', INVALID_PUSH_USER);
        $this->db->where('gcm_id!=', '');
        $this->db->limit($num, $offset);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0
     * @date            14-07-2015 
     * @description   	Get all Package details.
     */
    public function get_active_package() {
        $this->db->select('package.id,package.package_name,package.operator,package.description,package.package_price,package.minimum_amount,package.startdate,package.package_end_time,package.package_status,package.share_count,attachments.image,attachments.package_id,source_url,TIMEDIFF(package.package_end_time,package.startdate) AS time_left', FALSE);
        $this->db->from('package');
//        $this->db->where('attachments.main', MAIN_ATTACH);
        $this->db->where('package.package_status', ACTIVE);
//        $this->db->join('attachments', 'package.id = attachments.package_id', 'left');
        $this->db->join('attachments', 'attachments.package_id = package.id AND attachments.main=' . MAIN_ATTACH, 'left');
        $this->db->limit(LIMIT);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0
     * Date:            15-09-2015
     * Description:     get all selected mobile user for send push-notification
     */
    public function get_selected_user_id($data) {
        $id = $data['packid'];
        //return $content=$this->db->query("")
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0
     * Date:            15-09-2015
     * Description:     get all selected mobile user for send push-notification
     */
    public function get_mob_selected_user_count($data) {
        $id = $data['packid'];
        $users = $this->db->query("SELECT user_id FROM `package_amount` WHERE `pack_id` = $id")->result();
        $u_id = '';
        foreach ($users as $row) {
            $u_id .= $row->user_id . ",";
        }
        $u_id = rtrim($u_id, ',');

        $this->db->select('mobile_api.gcm_id');
        $this->db->from('mobile_api');
        $this->db->join('user', 'user.id=mobile_api.user_id');
        if (isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1) {
            $this->db->where('user.status', ACTIVEUSER);
        }
        if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '' && $data['app_version']==1) {
            $this->db->where('user.app_version', '');
        }
        else if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')
        {
            $this->db->like('user.app_version', $app_version);
        }
        $this->db->where('mobile_api.status', ACTIVE_PUSH_USER);
        $this->db->where('mobile_api.gcm_status', ACTIVE_PUSH_USER);
        $this->db->where('mobile_api.gcm_id!=', '');
        $this->db->where_not_in('mobile_api.user_id', $u_id);
        $content = $this->db->distinct();
        $content = $this->db->get();
        $count = count($content->result());
        if ($count) {
            return $count;
        } else {
            return FALSE;
        }

        /* if (isset($data['otp_verified']) && $data['otp_verified'] != '') 
          {
          $content = $this->db->query("select COUNT(DISTINCT mobile_api.gcm_id) AS count  from `mobile_api` JOIN user on user.id=mobile_api.user_id where user.status = 1 and mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN ($u_id)")->row()->count;
          }
          else
          {
          $content = $this->db->query("select DISTINCT mobile_api.gcm_id from `mobile_api` where mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN ($u_id)")->result();
          } */
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0
     * Date:            15-09-2015
     * Description:     get all selected mobile user for send push-notification
     */
    /* public function get_mob_selected_user($data) {
      $num = PACKET;
      $id = $data['packid'];
      $users = $this->db->query("SELECT user_id FROM `package_amount` WHERE `pack_id` = $id")->result();
      $u_id = '';
      foreach ($users as $row) {
      $u_id .= $row->user_id . ",";
      }
      $u_id = rtrim($u_id, ',');
      $offset = $data['offset'];
      $this->db->select('mobile_api.gcm_id');
      $this->db->from('mobile_api');
      if (isset($data['otp_verified']) && $data['otp_verified'] != ''&& $data['otp_verified'] == 1)
      {
      $this->db->join('user', 'user.id=mobile_api.user_id');
      $this->db->where('user.status', ACTIVEUSER);
      }
      $this->db->where('mobile_api.status', ACTIVE_PUSH_USER);
      $this->db->where('mobile_api.gcm_status', ACTIVE_PUSH_USER);
      $this->db->where('mobile_api.gcm_id!=', '');
      $this->db->where_not_in('mobile_api.user_id',$u_id);
      $this->db->limit($num, $offset);
      $content = $this->db->distinct();
      $content = $this->db->get();
      return $content->result();
      if($content)
      {

      return $content;
      }else{
      return FALSE;
      }

      /* if (isset($data['otp_verified']) && $data['otp_verified'] != '')
      {
      $content = $this->db->query("select DISTINCT mobile_api.gcm_id from `mobile_api` JOIN user on user.id=mobile_api.user_id where user.status = 1 and mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN (".$u_id.") LIMIT ".$offset.",".$num."")->result();
      }
      else
      {
      $content = $this->db->query("select DISTINCT mobile_api.gcm_id from `mobile_api` where mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN (".$u_id.") LIMIT ".$offset.",".$num."")->result();
      }

      } */
    public function get_mob_selected_user($data) {
        $num = PACKET;
        $id = $data['packid'];
        $offset = $data['offset'];
         $join = " WHERE ";
        if ((isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1)||(isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')) {
            $join = " JOIN user ON user.id = mobile_api.user_id WHERE ";
        } else {
            $join = " WHERE ";
        }
        if (isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1) {
            $join =$join. " user.status = ".ACTIVEUSER. "AND ";
        }
        if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '' && $data['app_version']==1) {
            $join =$join. " user.app_version = '' AND ";
        }
        else if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')
        {
            $join =$join. " user.app_version LIKE ".$data['app_version']." AND ";
        }
        $content = $this->db->query("select DISTINCT mobile_api.gcm_id from `mobile_api` ".$join." mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN (SELECT user_id FROM package_amount WHERE pack_id =$id) LIMIT " . $offset . "," . $num . "");
        
        
        
        
        /*
        if (isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1) {
            $content = $this->db->query("select DISTINCT mobile_api.gcm_id from `mobile_api` JOIN user on user.id=mobile_api.user_id where user.status = 1 and mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN (SELECT user_id FROM package_amount WHERE pack_id =$id) LIMIT " . $offset . "," . $num . "");
        } else {
            $content = $this->db->query("select DISTINCT mobile_api.gcm_id from `mobile_api` where mobile_api.gcm_id != '' and mobile_api.status=0 and mobile_api.user_id NOT IN (SELECT user_id FROM package_amount WHERE pack_id =$id) LIMIT " . $offset . "," . $num . "");
        }*/
        if ($content->num_rows() > 0) {
            return $content->result();
        } else {
            return FALSE;
        }
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.0
     * Date:            13-10-2015
     * Description:     get all unregistered mobile users... 
     */
    public function get_unreg_users_count($app_version=NULL) {
        $this->db->select('imei,gcm_id');
        $this->db->from('install_mobile_api');
        $this->db->where('gcm_status', ACTIVE_PUSH_USER);
        $this->db->where('gcm_id!=', '');
        if (isset($app_version) && $app_version != NULL && $app_version != '' && $app_version==1) 
        {
            $this->db->where('install_mobile_api.app_version', '');
        }
        else if (isset($app_version) && $app_version != NULL && $app_version != '')
        {
            $this->db->like('install_mobile_api.app_version', $app_version);
        }
        $content = $this->db->get();
        return count($content->result());
    }

    /**
     * CodeIgniter
     * @package         KSFAC
     * @author          Chinnu
     * @since           Version 1.1.0
     * Date:            22-08-2016
     * Description:     get all unregistered mobile users... 
     */
    public function get_unreg_users($data) {
        $num = PACKET;
        $this->db->select('gcm_id');
        $this->db->from('install_mobile_api');
        $this->db->where('gcm_status!=', INVALID_PUSH_USER);
        $this->db->where('gcm_id!=', '');
        if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '' && $data['app_version']==1) {
            $this->db->where('install_mobile_api.app_version', '');
        }
        else if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')
        {
            $this->db->like('install_mobile_api.app_version', $data['app_version']);
        }
        //$this->db->limit($num,$offset);
        $this->db->limit($num, $data['offset']);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * CodeIgniter
     * @package         KSFAC
     * @author          Deepak
     * @since           Version 1.1.0
     * Date:            13-10-2015
     * Description:     get all unregistered mobile users... 
     */
    public function unreg_users($data) {
        /* if($data['push_packet']==FIRST_INSTANCE)
          {
          $offset =FIRST_INSTANCE;
          }
          else
          {
          $offset = (PACKET * $data['push_packet'])+NEXT_ID;
          } */
        $num = PACKET;
        $this->db->select('id,imei,gcm_id');
        $this->db->from('install_mobile_api');
        $this->db->where('gcm_status!=', INVALID_PUSH_USER);
        $this->db->where('gcm_id!=', '');
        //$this->db->limit($num,$offset);
        $this->db->limit($num, $data['offset']);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            21-10-2015
     * Description:     get pushnotification details.
     */
    public function get_cron_details1($data) {
        $status = array(0, 2);
        $crr_date = date("Y-m-d H:i:s");
        $this->db->select('id,parent_id,user_id,pack_id,message,user_type,notification_type,otp_verified,status,app_version');
        $this->db->from('push_msgs');
//        $this->db->like('created_at', $data['crr_date']);
        $this->db->order_by("id", "asc");
        $this->db->where_in('status', $status);
        if (isset($data['notification_types']) && $data['notification_types'] != '' && $data['notification_types'] != NULL) {
            $this->db->where('notification_type=', $data['notification_types']);
        }
        if (isset($data['push_id']) && $data['push_id'] != '' && $data['push_id'] != NULL) {
            $this->db->where('id=', $data['push_id']);
        }
//        $this->db->where("push_msgs.schedule<=NOW()");
         $this->db->where("push_msgs.schedule <=",$crr_date);
        $this->db->limit(LIMIT);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:    Update user_id and notification sending status..
     */
    public function update_user_details($data,$status=UPDATE_GCM_STAT) {
        $detil = array(
            'user_id' => $data['user_details'],
            'status' => $status
//            'user_count' => $data['send_users']
        );
//        if (isset($data['close_status']) && $data['close_status'] != '' && $data['close_status'] != NULL) 
//        {
//            $detil['status'] = $data['close_status'];
//        }
        $this->db->where('id', $data['msg_id']);
        $this->db->limit(LIMIT);
        $this->db->update('push_msgs', $detil);
        return ($this->db->affected_rows());
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:    Update sending result;
     */
    public function update_results($data) {
        $detil = array(
            'success' => $data['success'],
            'failure' => $data['failure']
//            'error_gcm' => $data['error_gcm']
        );
        $this->db->where('id', $data['msg_id']);
        $this->db->limit(LIMIT);
        $this->db->update('push_msgs', $detil);
        return ($this->db->affected_rows());
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          SUBIN SAKARIYA
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:    Get push notification details...
     */
    public function get_cron_details_manually($data) {
        $this->db->select('id,pack_id,message,user_type,status,notification_type');
        $this->db->from('push_msgs');
        $this->db->where('id=', $data['push_id']);
        $this->db->limit(LIMIT);
        $content = $this->db->get();
        return $content->result();
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          Rejeesh K.Nair
     * @since           Version 1.1.0
     * Date:            21-10-2015
     * Description:     get push notification details for Registered user.
     */
    public function get_cron_details_reg_users($data) {
        $this->db->select('id,pack_id,message,user_type,notification_type');
        $this->db->from('push_msgs');
        $this->db->where('id=', $data['push_id']);
        $this->db->order_by("id", "asc");
        //$this->db->where('status=', 0);
        $this->db->limit(LIMIT);
        $query = $this->db->get();
        if ($query->num_rows() > 0):
            return $query->result();
        else:
            return FALSE;
        endif;
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:     Insert failed gcm entries for second try
     */
    public function insert_failed_gcm($data) {
        $detil = array(
            'status' => 3
        );
        $this->db->where('id', $data['msg_id']);
        $this->db->limit(LIMIT);
        $this->db->update('push_msgs', $detil);

        $details = array(
            'parent_id' => $data['msg_id'],
            'user_id' => $data['failed_gcm'],
            'message' => $data['serialize_msg'],
            'user_type' => $data['user_type'],
            'pack_id' => $data['pack_id'],
            'notification_type' => $data['push_packet'],
            'otp_verified' => $data['otp_verified'],
            'status' => $data['stat'],
            'created_at' => date("Y-m-d H:i:s")
        );
        $this->db->set($details);
        $this->db->insert('push_msgs', $details);
        $ins_id = $this->db->insert_id();
        return $ins_id;
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:     Get registered and unregistered users
     */
    public function get_all_users($data) {
        $num = PACKET;
        $offset = $data['offset'];
        if ((isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1)||(isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')) {
            $join = " JOIN user ON user.id = MA.user_id WHERE ";
            $join_im = "";
        } else {
            $join = " WHERE ";
            $join_im = "";
        }
        if (isset($data['otp_verified']) && $data['otp_verified'] != '' && $data['otp_verified'] == 1) {
            $join =$join. " user.status = ".ACTIVEUSER. "AND ";
        }
        if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '' && $data['app_version']==1) {
            $join =$join. " user.app_version = '' AND ";
            $join_im =$join_im. " IM.app_version = '' AND ";
        }
        else if (isset($data['app_version']) && $data['app_version'] != NULL && $data['app_version'] != '')
        {
            $join =$join. " user.app_version LIKE '".$data['app_version']."' AND ";
            $join_im =$join_im. " IM.app_version LIKE '".$data['app_version']."' AND ";
        }
        $result = $this->db->query("(SELECT MA.gcm_id FROM mobile_api AS MA" . $join . " 
            MA.status= 0 AND MA.gcm_status= 0 AND MA.gcm_id!='') UNION 
            (SELECT IM.gcm_id FROM install_mobile_api AS IM
            WHERE " . $join_im . " IM.gcm_status= 0 AND IM.gcm_id!='' ) LIMIT " . $offset . " , " . $num . "")->result();
        return $result;
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:     Get registered and unregistered users
     */
    public function update_with_canonical_id($data) {
        foreach ($data['canonical'] as $value) {
            $this->db->select('id');
            $this->db->from('mobile_api');
            $this->db->where('gcm_id', $value['gcm_id']);
            $this->db->limit(LIMIT);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $detil = array(
                    'gcm_id' => $value['canonical_ids']
                );
                $this->db->where('gcm_id', $value['gcm_id']);
                $this->db->limit(LIMIT);
                $this->db->update('mobile_api', $detil);
            }
            $this->db->select('id');
            $this->db->from('install_mobile_api');
            $this->db->where('gcm_id', $value['gcm_id']);
            $this->db->limit(LIMIT);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $detil1 = array(
                    'gcm_id' => $value['canonical_ids']
                );
                $this->db->where('gcm_id', $value['gcm_id']);
                $this->db->limit(LIMIT);
                $this->db->update('install_mobile_api', $detil1);
            }
        }
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            22-08-2016
     * Description:     Update status of users when two attempt of sending push to them fails
     */
    public function update_failed_user_status($data) {
        foreach ($data['failed'] as $value) {
            $this->db->select('id');
            $this->db->from('mobile_api');
            $this->db->like('gcm_id', $value);
            $this->db->limit(LIMIT);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $detil = array(
                    'status' => 3
                );
                $this->db->like('gcm_id', $value);
                $this->db->limit(LIMIT);
                $this->db->update('mobile_api', $detil);
            }
            $this->db->select('id');
            $this->db->from('install_mobile_api');
            $this->db->like('gcm_id', $value);
            $this->db->limit(LIMIT);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $detil1 = array(
                    'gcm_status' => 3
                );
                $this->db->like('gcm_id', $value);
                $this->db->limit(LIMIT);
                $this->db->update('install_mobile_api', $detil1);
            }
        }
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            26-108-2016
     * Description:     check active package end time and deactivate if expired
     */
    public function check_package_end_time() {
        $crr_date = date("Y-m-d H:i:s");
        $this->db->select('id');
        $this->db->from('package');
        $this->db->where('package.package_status', ACTIVE);
        $this->db->order_by("id", "desc");
        $this->db->limit(LIMIT);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $data) {
                $active_pack = $data->id;
            }
            $this->db->select('id');
            $this->db->from('package');
//            $this->db->where("TIMEDIFF(package.package_end_time,NOW()) < 0");
            $this->db->where("package.package_end_time <=",$crr_date);
            $this->db->where('package.id', $active_pack);
            $this->db->order_by("id", "desc");
            $this->db->limit(LIMIT);
            $query1 = $this->db->get();
//            print_r($query1->result());
            if ($query1->num_rows() > 0) {
                //Active package expired
                $detil1 = array(
                    'package_status' => INACTIVE
                );
                $this->db->where('package.id', $active_pack);
                $this->db->limit(LIMIT);
                $this->db->update('package', $detil1);
                return array("status" => 2, "pack_id" => $active_pack);
            } else {
                //Active package not expired
                return array("status" => 3, "pack_id" => '');
            }
        } else {
            //No Active package
            return array("status" => 1, "pack_id" => '');
        }
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:     activate latest package
     */
    public function activate_latest_package($last_pack_id = '') {
        $crr_date = date("Y-m-d H:i:s");
        //deactivate all active packages
        $detil1 = array(
            'package_status' => INACTIVE
        );
        $this->db->where('package.package_status', ACTIVE);
        $this->db->update('package', $detil1);

        //Find first added package with start date and end date between current time
        $this->db->select('id');
        $this->db->from('package');
//        $this->db->where("TIMEDIFF(package.startdate,NOW()) <= 0");
//        $this->db->where("TIMEDIFF(package.package_end_time,NOW()) >= 0");
        
        $this->db->where("package.startdate <= ",$crr_date);
        $this->db->where("package.package_end_time >= ",$crr_date);
        $this->db->where('package.package_status', INACTIVE);
        if (isset($last_pack_id) && $last_pack_id != '') {
            $this->db->where('package.id>', $last_pack_id);
        }
        $this->db->order_by("id", "asc");
        $this->db->limit(LIMIT);
        $query1 = $this->db->get();
        if ($query1->num_rows() > 0) {
            foreach ($query1->result() as $data) {
                $new_active_pack = $data->id;
                $detil2 = array(
                    'package_status' => ACTIVE
                );
                $this->db->where('package.id', $new_active_pack);
                $this->db->update('package', $detil2);
                return $new_active_pack;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Model
     * @package         Visit Kerala
     * @author          CHINNU
     * @since           Version 1.1.0
     * Date:            26-10-2015
     * Description:     update package push status on package activation
     */
    public function update_package_push_status($pack_id) {
        if (isset($pack_id) && $pack_id != NULL && $pack_id != '') {
            $detil1 = array(
                'status' => ACTIVE_STATUS
            );
            $this->db->where('push_msgs.pack_id', $pack_id);
            $this->db->where('push_msgs.notification_type', PUSH_NEW_PACK);
            $this->db->where('push_msgs.status', 5);
            $this->db->update('push_msgs', $detil1);
            if ($this->db->affected_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}
