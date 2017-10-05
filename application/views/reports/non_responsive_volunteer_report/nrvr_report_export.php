<?php
$filename = "Non-Responsive-Volunteer-Report";
header("Cache-control: private");
header('Content-Encoding: UTF-8');
if($this->config->item('output_format') != 'html') {
    header("Content-Transfer-Encoding: binary");
    header("Content-type: application/msexcel; charset=UTF-8");
    header("Content-Disposition:attachment; filename=" . $filename . ".xls");
}
?>
<html>
    <head>

    </head>
    <body>
        <table width="100%" align="left" border="0">
            <tr>
                <td><b>Total event invitations sent</b></td>
                <td><b>Total Response </b></td>
                <td><b>Response %</b></td>
            </tr> 
            <tr>
                <td> <?php echo count($total_invitation_sent); ?></td> 
                <td> <?php echo count($total_response_sent); ?></td>
                <td><?php echo $response_per; ?></td>
            </tr>
            <tr>
                <td><b>Sl.No</b></td>
                <td><b>Name of Event</b></td>
                <td><b>Response %</b></td>
                <td><b>City </b></td>
                <td><b>Event Type </b></td>
            </tr>
            <?php
            $i = 1;
            if (isset($report_details) && !empty($report_details)) {
                $status_det = '';
                foreach ($report_details as $row) {
                    $event_id = $row->event_id;
                    $event_name = $row->event_name;
                    $event_city = $row->city_name;
                    $event_type = $row->event_type;

                    $column = 'EV.id';
                    $condition1 = 'EV.id='.$event_id;
                    $cur_date = date('Y-m-d');
                    $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
                    $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
                    $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
                    $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
                    if ($timeframe == 1) {
                        #$condition1 .=' AND EV.starts_on BETWEEN "' . $cur_date . '" AND "' . $cur_date_1day . '"';
                        $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
                    } elseif ($timeframe == 2) {
                        $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
                    } elseif ($timeframe == 3) {
                        $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
                    } elseif ($timeframe == 4) {
                        $condition1 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
                    }
                    $join = array(
                        array(
                            'table' => 'Event AS EV',
                            'condition' => 'UE.event_id=EV.id',
                            'jointype' => 'inner'
                        ),
                    );
                    $total_invitation_sent1 = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition1, TRUE, 'UE.id asc', $join);


                    $column = 'EV.id';
                    $condition2 = 'UE.user_choice != "0" AND EV.id='.$event_id;
                    $cur_date = date('Y-m-d');
                    $cur_date_1week = date("Y-m-d", strtotime("-1 week"));
                    $cur_date_1month = date("Y-m-d", strtotime("-1 month"));
                    $cur_date_3months = date("Y-m-d", strtotime("-3 month"));
                    $cur_date_1year = date("Y-m-d", strtotime("-1 year"));
                    if ($timeframe == 1) {
                        $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1week . '"';
                    } elseif ($timeframe == 2) {
                        $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1month . '"';
                    } elseif ($timeframe == 3) {
                        $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_3months . '"';
                    } elseif ($timeframe == 4) {
                        $condition2 .=' AND DATE(EV.starts_on) <"' . $cur_date . '" AND DATE(EV.starts_on) >= "' . $cur_date_1year . '"';
                    }
                    $total_response_sent1 = $this->Reports_model->get_data('UserEvent AS UE', $column, $condition2, TRUE, 'UE.id asc', $join);
                    $response_per='--';
                    if ($total_response_sent1 && $total_invitation_sent1) {
                        #$response_per= count($total_response_sent1).'---'.count($total_invitation_sent);
                        $response_per = (count($total_response_sent1) / count($total_invitation_sent)) * 100;
                    }
                    ?> 
                    <tr>
                        <td> <?php echo $i; ?></td> 
                        <td> <?php echo $event_name; ?></td>
                        <td><?php echo $response_per; ?></td>
                        <td><?php echo $event_city; ?></td>
                        <td><?php echo $event_type; ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?> 
        </table>
    </body>
</html>