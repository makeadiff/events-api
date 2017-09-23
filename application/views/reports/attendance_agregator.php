<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
<body>
    <div class="container">
        <a href="<?php echo base_url('reports/mad-reports'); ?>"><< Back</a>
        <div class="row centered-form" style="text-align: center">
            <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Attendance Agregator Report</h3>
                    </div>
                    <div class="panel-body">
                        <?php
                        if (validation_errors()) {
                            echo '<b style="color:red; text-align: center;">All fields are required.</b>';
                        }
                        echo '<b style="color:red; text-align: center;">' . $this->session->flashdata('message') . '</b>';
                        ?>

                        <?php
                        $attributes = array('id' => 'aa_reports', 'name' => 'aa_reports', 'autocomplete' => 'off', 'role' => 'form');
                        echo form_open_multipart('attendance-agregator-report', $attributes);
                        ?>
                        <div class="form-group">
                            <?php
                            $aa_report_choice_list = $this->config->item('aa_report_choice');
                            echo form_dropdown('aa_report_choice', $aa_report_choice_list, '', 'id ="aa_report_choice" class="form-control input-sm"');
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo form_multiselect('city[]', $city_det_list, '', 'id ="city" class="form-control input-sm"');
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo form_multiselect('event_type[]', $event_type_det_list, '', 'id ="event_type" class="form-control input-sm"');
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            $aa_report_timeframe = $this->config->item('aa_report_timeframe');
                            echo form_dropdown('timeframe', $aa_report_timeframe, '', 'id ="timeframe" class="form-control input-sm"');
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
        background: #36618c url(<?php echo base_url('/images/mad_bg.png') ?>) no-repeat fixed center center;
        background-size: 100%;
    }
</style>