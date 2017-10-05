<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<body>
<div class="container">
    <div class="row centered-form">
        <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">MAD App Reports</h3>
                </div>
                <div class="panel-body" style="min-height: 400px;">
                    <div><a href="<?php echo base_url('attendance-agregator-report'); ?>">View Attendance Agregator Report</a></div>
                    <div><a href="<?php echo base_url('volunteer-participation?user_id=110380'); ?>">View Volunteer Participation Report</a></div>
                    <div><a href="<?php echo base_url('event-attendance-report'); ?>">View Event Attendance Report</a></div>
                    <div><a href="<?php echo base_url('participation-tracker'); ?>">View Participation Tracker</a></div>
                    <div><a href="<?php echo base_url('non-responsive-volunteer-report'); ?>">View Non Responsive volunteer Report</a></div>
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