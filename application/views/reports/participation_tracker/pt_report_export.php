<?php
$filename = "Participation-Tracker-Report";
header("Cache-control: private");
header('Content-Encoding: UTF-8');
if($this->config->item('output_format') != 'html') {
    header("Content-Transfer-Encoding: binary");
    header("Content-type: application/msexcel; charset=UTF-8");
    header("Content-Disposition:attachment; filename=" . $filename . ".xls");
}?>
<html>
    <head>

    </head>
    <body>
        <table width="100%" align="left" border="0">
            <?php 
            $column = 'UR.id,UR.name AS user_name';
            $condition = 'UR.city_id =' . $city . ' AND UR.center_id=' . $center;
            $cur_date = date('Y-m-d');
            $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
            $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
            $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
            $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
            if ($timeframe == 1) {
                #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($timeframe == 2) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
            } elseif ($timeframe == 3) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
            } elseif ($timeframe == 4) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
            }
            $join = array(
                array(
                    'table' => 'Event AS EV',
                    'condition' => 'UE.event_id=EV.id',
                    'jointype' => 'left'
                ),
                array(
                    'table' => 'User AS UR',
                    'condition' => 'UE.user_id=UR.id',
                    'jointype' => 'left'
                ),
            );
            $tot_no_events_invited = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition, TRUE, 'UE.id asc', $join);
            
            $column = 'UR.id,UR.name AS user_name';
            $condition = 'UR.city_id =' . $city . ' AND UR.center_id=' . $center. ' AND UE.present=' . ACTIVE . ' AND UE.late=' . INACTIVE;;
            $cur_date = date('Y-m-d');
            $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
            $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
            $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
            $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
            if ($timeframe == 1) {
                #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
            } elseif ($timeframe == 2) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
            } elseif ($timeframe == 3) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
            } elseif ($timeframe == 4) {
                $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
            }
            $join = array(
                array(
                    'table' => 'Event AS EV',
                    'condition' => 'UE.event_id=EV.id',
                    'jointype' => 'left'
                ),
                array(
                    'table' => 'User AS UR',
                    'condition' => 'UE.user_id=UR.id',
                    'jointype' => 'left'
                ),
            );
            $tot_no_events_attended = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition, TRUE, 'UE.id asc', $join);
            ?>
            <tr>
                <td></td>
                <td>Overall Participation Level : <?php echo  '<b>'.count($tot_no_events_attended) .'</b>(Total events attended)/'.'<b>'.count($tot_no_events_invited).'</b>(Total invitations send)';?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Sl.No</td>
                <td><b>Volunteer Name</b></td>
                <td><b>Number of events invited</b></td>
                <td><b>Number of times responded</b></td>
                <td><b>Number of events attended</b></td>
                <td><b>Participation rate</b></td>
            </tr> 
            <?php
            $i = 1;
            if (isset($report_details) && !empty($report_details)) {
                foreach ($report_details as $row) {
                    $id = $row->id;
                    $user_name = $row->user_name;

                    $column = 'UR.id,UR.name AS user_name';
                    $condition = 'UR.city_id =' . $city . ' AND UR.center_id=' . $center . ' AND UE.user_id=' . $id;
                    $cur_date = date('Y-m-d');
                    $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
                    $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
                    $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
                    $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
                    if ($timeframe == 1) {
                        #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
                    } elseif ($timeframe == 2) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
                    } elseif ($timeframe == 3) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
                    } elseif ($timeframe == 4) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
                    }
                    /* $join = array(
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
                      ); */
                    $join = array(
                        array(
                            'table' => 'Event AS EV',
                            'condition' => 'UE.event_id=EV.id',
                            'jointype' => 'left'
                        ),
                        array(
                            'table' => 'User AS UR',
                            'condition' => 'UE.user_id=UR.id',
                            'jointype' => 'left'
                        ),
                    );
                    $no_events_invited = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition, TRUE, 'UE.id asc', $join);

                    $column = 'UR.id,UR.name AS user_name';
                    $condition = 'UR.city_id =' . $city . ' AND UR.center_id=' . $center . ' AND UE.user_id=' . $id . ' AND UE.user_choice != 0';
                    $cur_date = date('Y-m-d');
                    $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
                    $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
                    $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
                    $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
                    if ($timeframe == 1) {
                        #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
                    } elseif ($timeframe == 2) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
                    } elseif ($timeframe == 3) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
                    } elseif ($timeframe == 4) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
                    }
                    $join = array(
                        array(
                            'table' => 'Event AS EV',
                            'condition' => 'UE.event_id=EV.id',
                            'jointype' => 'left'
                        ),
                        array(
                            'table' => 'User AS UR',
                            'condition' => 'UE.user_id=UR.id',
                            'jointype' => 'left'
                        ),
                    );
                    $no_events_responded = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition, TRUE, 'UE.id asc', $join);

                    $column = 'UR.id,UR.name AS user_name';
                    $condition = 'UR.city_id =' . $city . ' AND UR.center_id=' . $center . ' AND UE.user_id=' . $id . ' AND UE.present=' . ACTIVE . ' AND UE.late=' . INACTIVE;
                    $cur_date = date('Y-m-d');
                    $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
                    $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
                    $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
                    $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
                    if ($timeframe == 1) {
                        #$condition .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
                    } elseif ($timeframe == 2) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
                    } elseif ($timeframe == 3) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
                    } elseif ($timeframe == 4) {
                        $condition .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
                    }
                    $join = array(
                        array(
                            'table' => 'Event AS EV',
                            'condition' => 'UE.event_id=EV.id',
                            'jointype' => 'left'
                        ),
                        array(
                            'table' => 'User AS UR',
                            'condition' => 'UE.user_id=UR.id',
                            'jointype' => 'left'
                        ),
                    );
                    $no_events_attended = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition, TRUE, 'UE.id asc', $join);

                    $participation_rate = (count($no_events_attended)/count($no_events_invited))*100 ;
                    ?> 
                    <tr>
                        <td> <?php echo $i; ?></td> 
                        <td> <?php echo $user_name; ?></td>
                        <td><?php echo count($no_events_invited); ?></td>
                        <td><?php echo count($no_events_responded); ?></td>
                        <td><?php echo count($no_events_attended); ?></td>
                        <td><?php echo $participation_rate; ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?> 
        </table>
    </body>
</html>