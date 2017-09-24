<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:            15-09-2017	 
     * Description:     Reports controller constructor
     */
    function __construct() {
        parent::__construct();
        $this->load->model(array('Reports_model'));
        $this->load->helper(array('url', 'string'));
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:            15-09-2017	 
     * Description:     Index page.
     */
    public function index() {
        echo "Invalid Access";
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		19-09-2017	 
     * Description: 	Function to show all reports.
     */
    public function mad_reports() {
        $data = array();
        $this->load->view('reports/all_reports', $data);
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		15-09-2017	 
     * Description: 	Function to show attendance agregator report as Excel.
     */
    public function attendance_agregator_report() {
        $data['aa_report_choice'] = $this->input->post_get('aa_report_choice');
        $data['city'] = $this->input->post_get('city');
        $data['event_type'] = $this->input->post_get('event_type');
        $data['timeframe'] = $this->input->post_get('timeframe');

        #Validation starts here
        $this->form_validation->set_rules('aa_report_choice', 'Select One', 'required');
        $this->form_validation->set_rules('city[]', 'City', 'required');
        $this->form_validation->set_rules('event_type[]', 'Event Type', 'required');
        $this->form_validation->set_rules('timeframe', 'Time Frame', 'required');
        if ($this->form_validation->run() === FALSE) {
            $city_det = $this->Reports_model->get_data('City', 'id,name', array(), TRUE, 'id asc');
            $city_det_list[''] = 'Choose City';
            #$city_det_list['-1'] = 'Select All';
            if (isset($city_det) && !empty($city_det)) {
                foreach ($city_det as $cd) {
                    $city_det_list[$cd->id] = $cd->name;
                }
            }
            $data['city_det_list'] = $city_det_list;

            $event_type_det = $this->Reports_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
            $event_type_det_list[''] = 'Choose Event Type';
            #$city_det_list['-1'] = 'Select All';
            if (isset($event_type_det) && !empty($event_type_det)) {
                foreach ($event_type_det as $etd) {
                    $event_type_det_list[$etd->id] = $etd->name;
                }
            }
            $data['event_type_det_list'] = $event_type_det_list;

            $this->load->view('reports/attendance_agregator/attendance_agregator', $data);
        } else {
            #$data['event_type'][0];
            $city_val = '';
            if (isset($data['city']) && !empty($data['city'])) {
                foreach ($data['city'] as $row) {
                    $city_val.=$row . ',';
                }
                $city_val = rtrim($city_val, ',');
            }
            $event_type_val = '';
            if (isset($data['event_type']) && !empty($data['event_type'])) {
                foreach ($data['event_type'] as $row) {
                    $event_type_val.=$row . ',';
                }
                $event_type_val = rtrim($event_type_val, ',');
            }
            $column = 'EV.id,CT.name AS city_name,ET.name AS event_type,EV.name AS event_name,EV.starts_on AS event_startson';
            $condition = 'EV.event_type_id IN (' . $event_type_val . ') AND EV.city_id IN (' . $city_val . ')';
            $cur_date = date('Y-m-d');
            $cur_date_1day = date("Y-m-d", strtotime("+ 1 day"));
            $cur_date_1week = date("Y-m-d", strtotime("+ 1 week"));
            $cur_date_2week = date("Y-m-d", strtotime("+ 2 week"));
            $cur_date_4week = date("Y-m-d", strtotime("+ 4 day"));
            if ($data['timeframe'] == 1) {
                $condition .=' AND EV.starts_on >"' . $cur_date . '"';
            } elseif ($data['timeframe'] == 2) {
                #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                $condition .=' AND DATE(EV.starts_on) >"' . $cur_date . '" AND DATE(EV.starts_on) <= "' . $cur_date_1day . '"';
            } elseif ($data['timeframe'] == 3) {
                $condition .=' AND DATE(EV.starts_on) >"' . $cur_date . '" AND DATE(EV.starts_on) <= "' . $cur_date_1week . '"';
            } elseif ($data['timeframe'] == 4) {
                $condition .=' AND DATE(EV.starts_on) >"' . $cur_date . '" AND DATE(EV.starts_on) <= "' . $cur_date_2week . '"';
            } elseif ($data['timeframe'] == 5) {
                $condition .=' AND DATE(EV.starts_on) >"' . $cur_date . '" AND DATE(EV.starts_on) <= "' . $cur_date_4week . '"';
            }
            $join = array(
                array(
                    'table' => 'City AS CT',
                    'condition' => 'EV.city_id=CT.id',
                    'jointype' => 'left'
                ),
                array(
                    'table' => 'Event_Type AS ET',
                    'condition' => 'ET.id=EV.event_type_id',
                    'jointype' => 'left'
                )
            );
            $report_details = $this->Reports_model->get_data('Event AS EV', $column, $condition, TRUE, 'EV.id asc', $join);
            if (isset($report_details) && !empty($report_details)) {
                $data['report_details'] = $report_details;
                 $this->load->view('reports/attendance_agregator/aa_report_export', $data);
            } else {
                $this->session->set_flashdata('message', 'No records found.');
                redirect('attendance-agregator-report');
            }
        }
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		19-09-2017	 
     * Description: 	Function to show volunteer profile report as Excel.
     */
    public function volunteer_profile_report() {
        
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		19-09-2017	 
     * Description: 	Function to show event attendance report as Excel.
     */
    public function event_attendance_report() {
        $data['city'] = $this->input->post_get('city');
        $data['event_type'] = $this->input->post_get('event_type');
        $data['center'] = $this->input->post_get('center');

        #Validation starts here
        $this->form_validation->set_rules('city[]', 'City', 'required');
        $this->form_validation->set_rules('center[]', 'Center', 'required');
        $this->form_validation->set_rules('event_type', 'Event Type', 'required');
        if ($this->form_validation->run() === FALSE) {
            $city_det = $this->Reports_model->get_data('City', 'id,name', array(), TRUE, 'id asc');
            $city_det_list[''] = 'Choose City';
            if (isset($city_det) && !empty($city_det)) {
                foreach ($city_det as $cd) {
                    $city_det_list[$cd->id] = $cd->name;
                }
            }
            $data['city_det_list'] = $city_det_list;

            $event_type_det = $this->Reports_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
            $event_type_det_list[''] = 'Choose Event Type';
            if (isset($event_type_det) && !empty($event_type_det)) {
                foreach ($event_type_det as $etd) {
                    $event_type_det_list[$etd->id] = $etd->name;
                }
            }
            $data['event_type_det_list'] = $event_type_det_list;
            $this->load->view('reports/event_attendance/event_attendance', $data);
        } else {
            #$data['event_type'][0];
            $city_val = '';
            if (isset($data['city']) && !empty($data['city'])) {
                foreach ($data['city'] as $row) {
                    $city_val.=$row . ',';
                }
                $city_val = rtrim($city_val, ',');
            }
            $event_type_val = $data['event_type'];
            // if (isset($data['event_type']) && !empty($data['event_type'])) {
            //     foreach ($data['event_type'] as $row) {
            //         $event_type_val.=$row . ',';
            //     }
            //     $event_type_val = rtrim($event_type_val, ',');
            // }
            
            $column = 'EV.id,CT.name AS city_name,ET.name AS event_type,EV.name AS event_name,EV.starts_on AS event_startson';
            $condition = 'EV.event_type_id IN (' . $event_type_val . ') AND EV.city_id IN (' . $city_val . ')';
            
            $join = array(
                array(
                    'table' => 'City AS CT',
                    'condition' => 'EV.city_id=CT.id',
                    'jointype' => 'left'
                ),
                array(
                    'table' => 'Event_Type AS ET',
                    'condition' => 'ET.id=EV.event_type_id',
                    'jointype' => 'left'
                )
            );

            $report_details = $this->Reports_model->get_data('Event AS EV', $column, $condition, TRUE, 'EV.id asc', $join);
            // print $this->Reports_model->db->last_query(); 
            // print_r($data);
            // print_r($report_details);
            // exit;
            if (isset($report_details) && !empty($report_details)) {
                $data['report_details'] = $report_details;
                $data['aa_report_choice'] = 1;

                $this->load->view('reports/aa_report_export', $data);
            } else {
                $this->session->set_flashdata('message', 'No records found.');
                redirect('event-attendance-report');
            }
        }
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		21-09-2017	 
     * Description: 	Function to return center based on city.
     */
    public function get_center() {
        $cities = $this->input->post('cities');
        if (isset($cities) && !empty($cities)) {
            $cities = urldecode($cities);
            $condition = 'city_id IN (' . $cities . ') AND status ='.ACTIVE;
            $center_det = $this->Reports_model->get_data('Center', 'id,name',$condition, TRUE, 'id asc');
            if (isset($center_det) && !empty($center_det)) {
                foreach ($center_det as $row) {
                    $data[$row->id] = $row->name;
                }
            }
            $result['succ'] = 1;
            $result['result'] = $data;
            echo json_encode($result);
            exit;
        } else {
            $result['succ'] = 0;
            die;
        }










        if (!validate_numeric($distri_id)) {
            $result['succ'] = 'err';
            $result['message'] = l('invalid_input_text');
        }
        $distri_id = intkillstring($this->common_model->safe_html($distri_id));
        $where = array('"District_Code"' => $distri_id);
        $result['succ'] = 1;
        $data = $this->common_model->prepare_select_box_data('M00_Block', 'Block_code,Block_Name', $where, '', 'Block_Name');
        $result['result'] = $data;
        echo json_encode($result);
        exit;
    }

}

/* End of file Api.php */