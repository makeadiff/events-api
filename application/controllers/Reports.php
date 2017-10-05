<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {
    public $year_start_date = '2017-04-01';

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
            $city_det_list['-1'] = 'Select All';
            if (isset($city_det) && !empty($city_det)) {
                foreach ($city_det as $cd) {
                    $city_det_list[$cd->id] = $cd->name;
                }
            }
            $data['city_det_list'] = $city_det_list;

            $event_type_det = $this->Reports_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
            $event_type_det_list[''] = 'Choose Event Type';
            $event_type_det_list['-1'] = 'Select All';
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
            $city_val_flag = 1;
            if (isset($data['city']) && !empty($data['city'])) {
                foreach ($data['city'] as $row) {
                    if($row == '-1'){
                      $city_val_flag = 2;  
                    }
                    $city_val.=$row . ',';
                }
                $city_val = rtrim($city_val, ',');
            }
            $event_type_val = '';
            $event_type_val_flag = 1;
            if (isset($data['event_type']) && !empty($data['event_type'])) {
                foreach ($data['event_type'] as $row) {
                    if($row == '-1'){
                      $event_type_val_flag = 2;  
                    }
                    $event_type_val.=$row . ',';
                }
                $event_type_val = rtrim($event_type_val, ',');
            }
            $column = 'EV.id,CT.name AS city_name,ET.name AS event_type,EV.name AS event_name,EV.starts_on AS event_startson';
            if($city_val_flag==2 && $event_type_val_flag = 2){
                $condition ='1=1';
            }elseif($city_val_flag==1 && $event_type_val_flag = 2){
                $condition = 'EV.city_id IN (' . $city_val . ')';
            }elseif($city_val_flag==2 && $event_type_val_flag = 1){
                $condition = 'EV.event_type_id IN (' . $event_type_val . ')';
            }else{
            $condition = 'EV.event_type_id IN (' . $event_type_val . ') AND EV.city_id IN (' . $city_val . ')';
            }
            $cur_date = date('Y-m-d');
            $cur_date_1day = date("Y-m-d", strtotime("- 1 day"));
            $cur_date_1week = date("Y-m-d", strtotime("- 1 week"));
            $cur_date_2week = date("Y-m-d", strtotime("- 2 week"));
            $cur_date_4week = date("Y-m-d", strtotime("- 4 day"));
            if ($data['timeframe'] == 1) {
                $condition .=' AND EV.starts_on > \'{$this->year_start_date}\'';
            } elseif ($data['timeframe'] == 2) {
                #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                $condition .=' AND DATE(EV.starts_on) < "' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1day . '"';
            } elseif ($data['timeframe'] == 3) {
                $condition .=' AND DATE(EV.starts_on) < "' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($data['timeframe'] == 4) {
                $condition .=' AND DATE(EV.starts_on) < "' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_2week . '"';
            } elseif ($data['timeframe'] == 5) {
                $condition .=' AND DATE(EV.starts_on) < "' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_4week . '"';
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
            print $this->Reports_model->db->last_query();
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
    public function volunteer_participation_report() {
        $data['user_id'] = $this->input->get('user_id');
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $data['from_date'] = $this->input->post_get('from_date');
            $data['to_date'] = $this->input->post_get('to_date');
            $data['vp_report_event_dd'] = $this->input->post_get('vp_report_event_dd');

            #Validation starts here
            #$this->form_validation->set_rules('from_date', 'From Date', 'required');
            #$this->form_validation->set_rules('to_date', 'To Date', 'required');
            $this->form_validation->set_rules('vp_report_event_dd', 'Event', 'required');
            if ($this->form_validation->run() === FALSE) {
                $city_det = $this->Reports_model->get_data('City', 'id,name', array(), TRUE, 'id asc');
                $city_det_list[''] = 'Choose City';
                if (isset($city_det) && !empty($city_det)) {
                    foreach ($city_det as $cd) {
                        $city_det_list[$cd->id] = $cd->name;
                    }
                }
                $event_type_det = $this->Reports_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
                $event_type_det_list[''] = 'Choose Event Type';
                #$city_det_list['-1'] = 'Select All';
                if (isset($event_type_det) && !empty($event_type_det)) {
                    foreach ($event_type_det as $etd) {
                        $event_type_det_list[$etd->id] = $etd->name;
                    }
                }
                $data['event_type_det_list'] = $event_type_det_list;
                $data['city_det_list'] = $city_det_list;

                $data['invited_events'] = $this->Reports_model->count_detail('UserEvent', array('user_id' => $data['user_id']));
                $data['attended'] = $this->Reports_model->count_detail('UserEvent', array('user_id' => $data['user_id'], 'present' => ACTIVE, 'late' => INACTIVE));
                $data['late'] = $this->Reports_model->count_detail('UserEvent', array('user_id' => $data['user_id'], 'present' => ACTIVE, 'late' => ACTIVE));
                $this->load->view('reports/volunteer_participation/volunteer_participation', $data);
            } else {
                $from_date = date("Y-m-d", strtotime($data['from_date']));
                $to_date = date("Y-m-d", strtotime($data['to_date']));
                $condition = array();
                if (isset($data['vp_report_event_dd']) && !empty($data['vp_report_event_dd'])) {
                    if ($data['vp_report_event_dd'] == 1) {
                        $condition = 'UE.user_id=' . $data['user_id'] . ' AND UE.present="' . ACTIVE . '" AND UE.late="' . INACTIVE . '"';
                    }
                    if ($data['vp_report_event_dd'] == 2) {
                        $condition = 'UE.user_id=' . $data['user_id'] . ' AND UE.user_choice="' . DEFAULT_STATUS . '" AND UE.present="' . MISSED . '" AND UE.late="' . DEFAULT_STATUS . '"';
                    }
                    if ($data['vp_report_event_dd'] == 3) {
                        $condition = 'UE.user_id=' . $data['user_id'] . ' AND UE.user_choice!="' . DEFAULT_STATUS . '" AND UE.present"' . MISSED . '" AND UE.late="' . DEFAULT_STATUS . '"';
                    }
                } else {
                    $condition = 'UE.user_id=' . $data['user_id'];
                }
                if ((isset($data['from_date']) && !empty($data['from_date'])) && (isset($data['to_date']) && !empty($data['to_date']))) {
                    $condition .=' AND DATE(EV.starts_on) >="' . $from_date . '" AND DATE(EV.starts_on) <= "' . $to_date . '"';
                }
                $column = 'EV.name AS event_name,DATE(EV.starts_on) AS event_date,UE.present AS present,UE.late AS late,UE.user_choice AS user_choice';

                $join = array(
                    array(
                        'table' => 'UserEvent AS UE',
                        'condition' => 'UE.event_id=EV.id',
                        'jointype' => 'left'
                    ),
                );
                $report_details = $this->Reports_model->get_data('Event AS EV', $column, $condition, TRUE, 'EV.id asc', $join);
                if (isset($report_details) && !empty($report_details)) {
                    $data['report_details'] = $report_details;
                    $this->load->view('reports/volunteer_participation/vp_report_export', $data);
                } else {
                    $this->session->set_flashdata('message', 'No records found.');
                    redirect('volunteer-participation?user_id=' . $data['user_id']);
                }
            }
        } else {
            echo "Invalid Access";
        }
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
        #$data['city'] = $this->input->get(); 
        $data['city'] = $this->input->post_get('city');
        $data['center'] = $this->input->post_get('center');
        $data['event_type'] = $this->input->post_get('event_type');

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
            $event_type_det = $this->Reports_model->get_data('Event_Type', 'id,name,status', array('status' => ACTIVE), TRUE, 'id asc');
            $event_type_det_list[''] = 'Choose Event Type';
            #$city_det_list['-1'] = 'Select All';
            if (isset($event_type_det) && !empty($event_type_det)) {
                foreach ($event_type_det as $etd) {
                    $event_type_det_list[$etd->id] = $etd->name;
                }
            }
            $data['event_type_det_list'] = $event_type_det_list;
            $data['city_det_list'] = $city_det_list;
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
            $center_val = '';
            if (isset($data['center']) && !empty($data['center'])) {
                foreach ($data['center'] as $row) {
                    $center_val.=$row . ',';
                }
                $center_val = rtrim($center_val, ',');
            }
            $column = 'UR.id,DATE(EV.starts_on) AS event_date,EV.name AS event_name,CT.name AS city_name,UR.name AS user_name,UE.present AS present,UE.late AS late,UE.user_choice AS user_choice';
            $condition = 'UR.city_id IN (' . $city_val . ') AND EV.event_type_id=' . $data['event_type'];
            $join = array(
                array(
                    'table' => 'UserEvent AS UE',
                    'condition' => 'UE.user_id=UR.id',
                    'jointype' => 'left'
                ),
                array(
                    'table' => 'Event AS EV',
                    'condition' => 'UE.event_id=EV.id',
                    'jointype' => 'left'
                ),
                array(
                    'table' => 'City AS CT',
                    'condition' => 'UR.city_id=CT.id',
                    'jointype' => 'left'
                ),
            );
            $report_details = $this->Reports_model->get_data('User AS UR', $column, $condition, TRUE, 'UR.id asc', $join);
            if (isset($report_details) && !empty($report_details)) {
                $data['report_details'] = $report_details;
                $this->load->view('reports/event_attendance/ea_report_export', $data);
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
     * Date:		19-09-2017	 
     * Description: 	Function to show participation tracker report as Excel.
     */
    public function participation_tracker_report() {
        #$data['city'] = $this->input->get(); 
        $data['city'] = $this->input->post_get('city');
        $data['center'] = $this->input->post_get('center');
        $data['timeframe'] = $this->input->post_get('timeframe');

        #Validation starts here
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('center', 'Center', 'required');
        $this->form_validation->set_rules('timeframe', 'Time Frame', 'required');
        if ($this->form_validation->run() === FALSE) {
            $city_det = $this->Reports_model->get_data('City', 'id,name', array(), TRUE, 'id asc');
            $city_det_list[''] = 'Choose City';
            if (isset($city_det) && !empty($city_det)) {
                foreach ($city_det as $cd) {
                    $city_det_list[$cd->id] = $cd->name;
                }
            }
            $data['city_det_list'] = $city_det_list;
            $this->load->view('reports/participation_tracker/participation_tracker', $data);
        } else {
            $column = 'DISTINCT(UR.id),UR.name AS user_name';
            $condition = 'UR.city_id =' . $data['city'] . ' AND UR.center_id=' . $data['center'];
            $cur_date = date('Y-m-d');
            $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
            $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
            $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
            $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
            if ($data['timeframe'] == 1) {
                #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($data['timeframe'] == 2) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
            } elseif ($data['timeframe'] == 3) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
            } elseif ($data['timeframe'] == 4) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
            }
            $join = array(
                array(
                    'table' => 'UserEvent AS UE',
                    'condition' => 'UE.user_id=UR.id',
                    'jointype' => 'inner'
                ),
                array(
                    'table' => 'Event AS EV',
                    'condition' => 'UE.event_id=EV.id',
                    'jointype' => 'inner'
                ),
            );
            $report_details = $this->Reports_model->get_data('User AS UR', $column, $condition, TRUE, 'UR.id asc', $join);
            if (isset($report_details) && !empty($report_details)) {
                $data['report_details'] = $report_details;
                $this->load->view('reports/participation_tracker/pt_report_export', $data);
            } else {
                $this->session->set_flashdata('message', 'No records found.');
                redirect('participation-tracker');
            }
        }
    }

    /**
     * CodeIgniter
     * @package         MAD App
     * @author          Rejeesh K.Nair
     * @since           Version 1.0.0
     * Date:		01-10-2017	 
     * Description: 	Function to show non responsive volunteer report as Excel.
     */
    public function non_responsive_volunteer_report() {
        $data['timeframe'] = $this->input->post_get('timeframe');
        
        #Validation starts here
        $this->form_validation->set_rules('timeframe', 'Time Frame', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('reports/non_responsive_volunteer_report/non_responsive_volunteer_report', $data);
        } else {
            $column = 'EV.id';
            $condition1 = '1=1';
            $cur_date = date('Y-m-d');
            $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
            $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
            $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
            $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
            if ($data['timeframe'] == 1) {
                #$condition1 .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($data['timeframe'] == 2) {
                $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
            } elseif ($data['timeframe'] == 3) {
                $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
            } elseif ($data['timeframe'] == 4) {
                $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
            }
             $join = array(
                array(
                    'table' => 'Event AS EV',
                    'condition' => 'UE.event_id=EV.id',
                    'jointype' => 'inner'
                ),
            );
            $data['total_invitation_sent'] = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition1, TRUE, 'UE.id asc', $join);
            
            
            $column = 'EV.id';
            $condition2 = 'UE.user_choice != "0"';
            $cur_date = date('Y-m-d');
            $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
            $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
            $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
            $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
            if ($data['timeframe'] == 1) {
                $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($data['timeframe'] == 2) {
                $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
            } elseif ($data['timeframe'] == 3) {
                $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
            } elseif ($data['timeframe'] == 4) {
                $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
            }
            $data['total_response_sent'] = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition2, TRUE, 'UE.id asc', $join);
            #if((isset($data['total_response_sent']) && !empty($data['total_response_sent'])) && (isset($data['total_invitation_sent']) && !empty($data['total_invitation_sent']))){
            if($data['total_response_sent'] && $data['total_invitation_sent']){
            $data['response_per'] = (count($data['total_response_sent'])/count($data['total_invitation_sent']))*100;
            }
            $condition='1=1';
            if ($data['timeframe'] == 1) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($data['timeframe'] == 2) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
            } elseif ($data['timeframe'] == 3) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
            } elseif ($data['timeframe'] == 4) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
            }
            $column = 'EV.id AS event_id,EV.name AS event_name,CT.name AS city_name,ET.name AS event_type';

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
                $this->load->view('reports/non_responsive_volunteer_report/nrvr_report_export', $data);
            } else {
                $this->session->set_flashdata('message', 'No records found.');
                redirect('non-responsive-volunteer-report');
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
            $condition = 'city_id IN (' . $cities . ') AND status =' . ACTIVE;
            $center_det = $this->Reports_model->get_data('Center', 'id,name', $condition, TRUE, 'id asc');
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
    }

}

/* End of file Api.php */