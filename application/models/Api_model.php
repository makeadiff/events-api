<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter
 * An open source applicaon development framework for PHP 4.3.2 or newer
 * CodeIgniter
 * @package         MAD App
 * @author          Rejeesh K.Nair
 * @since           Version 1.0.0
 * Date:            02-06-2017	 
 * Description: 	
 */
class Api_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	Functions for login process
     */
    public function login_data($data) {
        #$username = $this->db->escape($data['email_id']);
        $username = $data['email_id'];
        $this->db->select('UR.id, UR.title, UR.name, UR.email, UR.mad_email, UR.phone, UR.password, UR.city_id, GROUP_CONCAT(",", G.type) AS group_type', TRUE);
        $this->db->from('User AS UR');
        $this->db->join('UserGroup AS UG', 'UR.id=UG.user_id');
        $this->db->join('`Group` AS G', 'UG.group_id=G.id');
        $this->db->where("(UR.email='$username'", NULL,FALSE);
        $this->db->or_where("UR.mad_email='$username')", NULL, FALSE);
        $this->db->where("UR.status", ACTIVEUSER);
        $this->db->where("UR.user_type", 'volunteer');
        // print $this->db->last_query();

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	
     */
    public function get_imei($data) {
        $this->db->select('name,email');
        $this->db->from('User');
        #echo $this->db->last_query();
        $query = $this->db->get();
        if ($query->num_rows() > 0):
            return $query->row();
        else:
            return FALSE;
        endif;
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	
     */
    public function insert_imei($data) {
        if (isset($data['user_id']) && $data['user_id'] != '') {
            $detil = array(
                'status' => USER_INACTIVE
            );
            $this->db->where('user_id', $data['user_id']);
            $id = $this->db->update('Mobile_Api', $detil);
        }
        $details = array(
            'user_id' => $data['id'],
            'imei' => $data['imei'],
            'gcm_id' => $data['gcm_id'],
            'key' => $data['key']
        );
        $this->db->set($details);
        $this->db->insert('Mobile_Api', $details);
        $ins_id = $this->db->insert_id();
        return $ins_id;
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	
     */
    public function register($data) {
        $details = array(
            'user_name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['mobile'],
            'source' => MOBILE,
            'mobile_otp' => $data['mobile_otp'],
            'referral_id' => $data['referral_id'],
            'created_at' => date("Y-m-d H:i:s")
        );
        if (isset($data['fb_id']) && !empty($data['fb_id'])) {
            $details['fb_id'] = $data['fb_id'];
        }
        if (isset($data['age']) && !empty($data['age'])) {
            $details['age'] = $data['age'];
        }
        if (isset($data['fb_image']) && !empty($data['fb_image'])) {
            $details['fb_image'] = $data['fb_image'];
        }
        if (isset($data['app_version']) && !empty($data['app_version'])) {
            $details['app_version'] = $data['app_version'];
        }
        if (isset($data['os']) && !empty($data['os'])) {
            $details['os'] = $data['os'];
        }
        $this->db->set($details);
        $this->db->insert('User', $details);
        $ins_id = $this->db->insert_id();
        if (isset($ins_id) && $ins_id != '') {
            $details = array(
                'user_id' => $ins_id,
                'imei' => $data['imei'],
                'key' => $data['key']
            );
            if (isset($data['gcm_id']) && !empty($data['gcm_id'])) {
                $details['gcm_id'] = $data['gcm_id'];
            }
            $this->db->set($details);
            $this->db->insert('Mobile_Api', $details);
            $id = $this->db->insert_id();
        }
        #echo $this->db->last_query();
        return $ins_id;
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	
     */
    public function check_key($data) {
        $this->db->select('id');
        $this->db->from('Mobile_Api');
        //$this->db->where('user_id',$data['id']);
        $this->db->where('imei', $data['imei']);
        //$this->db->where('key',$data['key']);
        $this->db->where('status', ACTIVE_STATUS);
        $this->db->limit(LIMIT);
        $content = $this->db->get();
        return count($content->result());
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	
     */
    public function get_all_package_details() {
        $this->db->select('Package.id,Package.package_name,Package.operator,Package.operator_details,Package.location_details,Package.description,Package.package_price,Package.minimum_amount,Package.startdate,Package.package_end_time,Package.package_status,Package.share_count,attachments.image,attachments.package_id,source_url,TIMEDIFF(Package.package_end_time,Package.startdate) AS time_left', FALSE);
        $this->db->from('Package');
        #$this->db->where('attachments.main', MAIN_ATTACH);
        $this->db->where('Package.package_status', ACTIVE);
        $this->db->join('attachments', 'attachments.package_id = Package.id AND attachments.main=' . MAIN_ATTACH, 'left');
        #$this->db->join('attachments', 'Package.id = attachments.package_id', 'left');
        $this->db->limit(LIMIT);
        $content = $this->db->get();
        #echo $this->db->last_query();
        return $content->result();
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017
     * Description     Common function to get a single row data
     */
    function get_datum($table, $fields = '*', $where = array(), $type = TRUE, $order_by = '', $joins = array()) {
        if ((is_array($where) && count($where) > 0) or ( !is_array($where) && trim($where) != ''))
            $this->db->where($where);
        if ($order_by)
            $this->db->order_by($order_by);
        if (is_array($joins) && count($joins) > 0) {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['jointype']);
            }
        }
        $this->db->select($fields);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            if ($type) {
                return $query->row();
            } else {
                return $query->row_array();
            }
        } else {
            return FALSE;
        }
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017
     * Description    Common function to get a full row data
     */
    function get_data($table, $fields = '*', $where = array(), $type = TRUE, $order_by = '', $joins = array(), $limit = array(), $like = array(), $or_like = array(), $search_value = NULL, $search_like = NULL, $where_in = NULL, $where_in_data = NULL, $group_by = NULL, $escapespl = NULL) {
        if ((is_array($where) && count($where) > 0) or ( !is_array($where) && trim($where) != ''))
            $this->db->where($where);
        if (is_array($joins) && count($joins) > 0) {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['jointype']);
            }
        }
        //like clause
        if (is_array($like) && count($like) > 0) {
            $this->db->group_start();
            foreach ($like as $k => $v) {
                $this->db->like($v['column'], $v['value']);
            }
            $this->db->group_end();
        }
        // or_like clause
        if (is_array($or_like) && count($or_like) > 0) {
            $this->db->group_start();
            foreach ($or_like as $k => $v) {
                $this->db->or_like($v['column'], $v['value']);
            }
            $this->db->group_end();
        }
        //search a value in different column
        if ($search_value != NULL && $search_value != '') {
            if (is_array($search_like) && count($search_like) > 0) {
                $this->db->group_start();
                foreach ($search_like as $k) {
                    $this->db->or_like($k, $search_value);
                }
                $this->db->group_end();
            }
        }
        //Where in clause
        if ($where_in != NULL && (!empty($where_in_data))) {
            $this->db->where_in($where_in, $where_in_data);
        }
        if (is_array($limit) && count($limit) > 0) {
            $this->db->limit($limit['length'], $limit['start']);
        }
        if ($order_by)
            $this->db->order_by($order_by);
        if ($group_by)
            $this->db->group_by($group_by);
        if ($escapespl == TRUE) {
            $this->db->select($fields, FALSE);
        } else {
            $this->db->select($fields);
        }
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            if ($type) {
                return $query->result();
            } else {
                return $query->result_array();
            }
        } else {
            return FALSE;
        }
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description     Common function to update data.
     */
    function update($table, $data, $where) {
        if (!empty($data)) {
            $this->db->where($where, "", FALSE);
            $this->db->set($data);
            $update = $this->db->update($table);
            //echo $this->db->last_query();
            if ($update) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description    Common function to save data.
     */
    function save_data($table, $data) {
        if ((isset($table) && !empty($table)) && (isset($data) && !empty($data))) {
            $add = $this->db->insert($table, $data);
            if ($add) {
                return $this->db->insert_id();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description: 	Common function to get count.
     */
    function count_detail($table,$condition=array(),$fields = 'id') {
        if ((isset($table) && !empty($table))) {
            $this->db->select($fields);
            $this->db->from($table);
            $this->db->where($condition);
            $data = $this->db->count_all_results();
            // print $this->db->last_query();
            return $data;
        } else {
            return FALSE;
        }
    }

    /**
     * CodeIgniter
     * Model
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		02-06-2017	 
     * Description     Common function to delete data.
     */
    function delete($table, $where = array()) {
        if (!empty($table)) {
            if ($this->db->delete($table, $where)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }


    function get_teachers_in_center($center_id) {
        $users = $this->db->query("SELECT U.id, U.name, U.title, U.email, U.phone, U.city_id, $center_id AS center_id
                                FROM User U 
                                INNER JOIN UserBatch UB ON UB.user_id=U.id 
                                INNER JOIN Batch B ON UB.batch_id=B.id 
                                WHERE U.status='1' AND U.user_type='volunteer' AND B.center_id=$center_id AND B.year=2017 AND B.status='1'
                                ORDER BY U.name")->result();

        // :TODO: Add mentors to this list?
        return $users;
    }

}
