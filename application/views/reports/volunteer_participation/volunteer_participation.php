<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>-->



<body>
    <div class="container">
        <a href="<?php echo base_url('mad-reports'); ?>"><< Back</a>
        <div class="row centered-form" style="text-align: center">
            <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Volunteer Profile Report</h3>
                    </div>
                    <div class="panel-heading">
                        Number of events attended : <?php echo '<strong>' . $attended . '</strong>(Attended)/<strong>' . $invited_events . '</strong>(Invited)'; ?> <br>                    
                        Number of events late : <?php echo '<strong>' . $late . '</strong>(Late)/<strong>' . $attended . '</strong>(Attended)'; ?>
                    </div>
                    <div class="panel-body">
                        <?php
                        if (validation_errors()) {
                            echo '<b style="color:red; text-align: center;">All fields are required.</b>';
                        }
                        echo '<b id="error_msg" style="color:red; text-align: center;">' . $this->session->flashdata('message') . '</b>';
                        ?>

                        <?php
                        $attributes = array('id' => 'vp_reports', 'name' => 'vp_reports', 'autocomplete' => 'off', 'role' => 'form');
                        echo form_open_multipart('volunteer-participation?user_id=' . $user_id, $attributes);
                        ?>
                        <div class="form-group">
                                <?php echo form_label('From'); ?>
                                <div class="input-group date form_datetime col-md-12" id="form_datetime" data-date="<?php echo date("Y-m-d"); ?>T05:25:07Z" data-date-format="M dd, yyyy hh:ii" data-link-field="dtp_input1">
                                    <input class="form-control" size="16" type="text" name="from_date" value="" readonly>
                                    
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" class="form-control" id="start_date" value="" />
                                <label class="tip" id="startdate_err"></label>
                        </div>
                        <div class="form-group">
                                <?php echo form_label('To'); ?>
                                <div class="input-group date form_datetime col-md-12" id="form_datetime1" data-date="<?php echo date("Y-m-d"); ?>T05:25:07Z" data-date-format="M dd, yyyy hh:ii" data-link-field="dtp_input1">
                                    <input class="form-control" size="16" type="text" name="to_date" value="" readonly>
                                    
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                                <input type="hidden" class="form-control" id="start_date" value="" />
                                <label class="tip" id="startdate_err"></label>
                        </div>
                        <div class="form-group">
                            <?php
                            $vp_report_event_dd_list = $this->config->item('vp_report_event_dd');
                            echo form_dropdown('vp_report_event_dd', $vp_report_event_dd_list, '', 'id ="vp_report_event_dd" class="form-control input-sm"');
                            ?>
                        </div>
                        <input type="submit" value="Generate CSV" class="btn btn-info btn-block" id="submit_btn">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<style>
    body{
        background: #36618c url(<?php echo base_url(); ?>images/mad_bg.png) no-repeat fixed center center;
        background-size: 100%;
    }
</style>

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap-datepicker.css">

<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap-datepicker.js" charset="UTF-8"></script> 

<script>
    /* $(function () {
     $('#datetimepicker1').datetimepicker({
     format: 'YYYY-MM-DD'
     });
     });
     */
    $(function () {
        $('#form_datetime').datepicker({ format: 'mm/dd/yyyy',autoclose: true,});
        $('#form_datetime1').datepicker({ format: 'mm/dd/yyyy',autoclose: true,});
    });
    /*$(document).ready(function () {
     $("#aa_reports").validate({
     onkeyup: false,
     rules: {
     aa_report_choice: {
     required: true
     },
     city: {
     required: true
     },
     event_type: {
     required: true
     },
     timeframe : {  required : true,
     }
     },
     messages: {
     aa_report_choice: {
     required: "Please specify your First Name "
     },
     city: {
     required: "Please specify your Last Name "
     },
     event_type: {
     required: "Please specify your Salutation "
     },
     timeframe  : { required :'Please specify your Email Address', 
     },
     },errorClass: "error-tootip"
     });
     
     });*/


</script>