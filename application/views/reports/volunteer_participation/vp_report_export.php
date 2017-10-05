<?php
$filename = "Volunteer-Participation-Report";
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
                <td><b>Sl.No</b></td>
                <td><b>Name of Event</b> </td>
                <td><b>Attendance Status</b></td>
                <td><b>Date</b></td>
            </tr> 
            <?php
            $i = 1;
            if (isset($report_details) && !empty($report_details)) {
                $status_det = '';
                foreach ($report_details as $row) {
                    $event_name = $row->event_name;
                    $event_date = $row->event_date;
                    $present = $row->present;
                    $late = $row->late;
                    $user_choice = $row->user_choice;
                    if ($present == ACTIVE && $late == INACTIVE) {
                        $status_det = 'Ontime';
                    } elseif ($present == ACTIVE && $late == ACTIVE) {
                        $status_det = 'Late';
                    }elseif ($present == MISSED && $late == DEFAULT_STATUS) {
                        $status_det = 'Missed';
                    }elseif ($present == INACTIVE && $late == INACTIVE) {
                        $status_det = 'Invited';
                    }
                    ?> 
                    <tr>
                        <td> <?php echo $i; ?></td> 
                        <td> <?php echo $event_name; ?></td>
                        <td><?php echo $status_det; ?></td>
                        <td><?php echo $event_date; ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?> 
        </table>
    </body>
</html>