<?php
$filename = "Attendance-Agregator-Report";
header("Cache-control: private");
header('Content-Encoding: UTF-8');
header("Content-Transfer-Encoding: binary");
header("Content-type: application/msexcel; charset=UTF-8");
header("Content-Disposition:attachment; filename=" . $filename . ".xls");
?>
<html>
    <head>

    </head>
    <body>
        <table width="100%" align="left" border="0">
            <tr>
                <td>#</td>
                <td><b>City</td>
                <td><b>Event Type</td>
                <td><b>Date</td>
                <td><b>Event Name</td>
                <?php if ($aa_report_choice == 1) { ?>
                    <td><b>Going</td>
                    <td><b>May Be</td>
                    <td><b>Can't Go</td>
                <?php } elseif ($aa_report_choice == 2) { ?>
                    <td><b>Attended</td>
                    <td><b>Late</td>
                    <td><b>Missed</td>
                <?php } ?>
            </tr> 
            <?php
            $i = 1;
            //print_r($leader);die;
            if (isset($report_details) && !empty($report_details)) {
                $user_go_final_cnt = 0;
                $user_may_go_final_cnt = 0;
                $user_cantgo_final_cnt = 0;

                $user_attend_final_cnt = 0;
                $user_late_final_cnt = 0;
                $user_missed_final_cnt = 0;
                foreach ($report_details as $row) {
                    $city_name = $row->city_name;
                    $event_type = $row->event_type;
                    $event_startson = $row->event_startson;
                    $event_name = $row->event_name;
                    if ($aa_report_choice == 1) {
                        $user_go_cnt = $this->Reports_model->count_detail('UserEvent', array('event_id' => $row->id, 'user_choice' => GO));
                        $user_go_final_cnt = $user_go_final_cnt + $user_go_cnt;
                        $user_may_go_cnt = $this->Reports_model->count_detail('UserEvent', array('event_id' => $row->id, 'user_choice' => MYBEGO));
                        $user_may_go_final_cnt = $user_may_go_final_cnt + $user_may_go_cnt;
                        $user_cantgo_cnt = $this->Reports_model->count_detail('UserEvent', array('event_id' => $row->id, 'user_choice' => CANTGO));
                        $user_cantgo_final_cnt = $user_cantgo_final_cnt + $user_cantgo_cnt;
                    } elseif ($aa_report_choice == 2) {
                        $user_attend_cnt = $this->Reports_model->count_detail('UserEvent', array('event_id' => $row->id, 'present' => ACTIVE, 'late' => INACTIVE));
                        $user_attend_final_cnt = $user_attend_final_cnt + $user_attend_cnt;
                        $user_late_cnt = $this->Reports_model->count_detail('UserEvent', array('event_id' => $row->id, 'present' => ACTIVE, 'late' => ACTIVE));
                        $user_late_final_cnt = $user_late_final_cnt + $user_late_cnt;
                        $user_missed_cnt = $this->Reports_model->count_detail('UserEvent', array('event_id' => $row->id, 'present' => MISSED, 'late' => DEFAULT_STATUS));
                        $user_missed_final_cnt = $user_missed_final_cnt + $user_missed_cnt;
                    }
                    ?> 
                    <tr>
                        <td> <?php echo $i; ?></td> 
                        <td> <?php echo $city_name; ?></td>
                        <td><?php echo $event_type; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($event_startson)); ?></td>
                        <td><?php echo $event_name; ?></td>
                        <?php if ($aa_report_choice == 1) { ?>
                            <td><?php echo $user_go_cnt; ?></td>
                            <td><?php echo $user_may_go_cnt; ?></td>
                            <td><?php echo $user_cantgo_cnt; ?></td>
                        <?php } elseif ($aa_report_choice == 2) { ?>
                            <td><?php echo $user_attend_cnt; ?></td>
                            <td><?php echo $user_late_cnt; ?></td>
                            <td><?php echo $user_missed_cnt; ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?> 

            <tr>
                <td></td>
                <td><b>Total Count</b></td>
                <td></td>
                <td></td>
                <td></td>
                <?php if ($aa_report_choice == 1) { ?>
                    <td><b><?php echo $user_go_final_cnt; ?></b></td>
                    <td><b><?php echo $user_may_go_final_cnt; ?></b></td>
                    <td><b><?php echo $user_cantgo_final_cnt; ?></b></td>
                <?php } elseif ($aa_report_choice == 2) { ?>
                    <td><b><?php echo $user_attend_final_cnt; ?></b></td>
                    <td><b><?php echo $user_late_final_cnt; ?></b></td>
                    <td><b><?php echo $user_missed_final_cnt; ?></b></td>
                <?php } ?>
            </tr>
        </table>
    </body>
</html>