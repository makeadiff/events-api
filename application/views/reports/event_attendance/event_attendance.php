<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
<body>
	<div class="container">
		<a href="<?php echo base_url('mad-reports'); ?>">&lt;&lt; Back</a>
		<div class="row centered-form" style="text-align: center">
			<div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Event Attendance Report</h3>
					</div>
					<div class="panel-body">
						<?php
						if (validation_errors()) {
							echo '<b style="color:red; text-align: center;">All fields are required.</b>';
						}
						echo '<b id="error_msg" style="color:red; text-align: center;">' . $this->session->flashdata('message') . '</b>';
						?>

						<?php
						$attributes = array('id' => 'ea_reports', 'name' => 'ea_reports', 'autocomplete' => 'off', 'role' => 'form');
						echo form_open_multipart('event-attendance-report', $attributes);
						?>
						<div class="form-group">
							<?php
							echo form_multiselect('city[]', $city_det_list, '', 'id ="city" onblur="get_center();" class="form-control input-sm"');
							?>
						</div>
						<div class="form-group">
							<?php
							$center_det_list[''] = 'Choose Center';
							echo form_dropdown('center[]', $center_det_list, '', 'id ="center" class="form-control input-sm"');
							?>
						</div>
						<div class="form-group">
							<?php
							echo form_dropdown('event_type', $event_type_det_list, '', 'id ="event_type" class="form-control input-sm"');
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
<script>
function get_center() {
	var city_vals = $('select#city').val();
	if (city_vals != '') {
		$.ajax({
			type: 'post',
			url: 'get_center',
			data: "cities=" + encodeURIComponent(city_vals),
			success: function (response) {
				var obj = JSON.parse(response);
				if (obj.succ == 1) {
					$('#center').attr({
						'multiple': true,
					});
					$('#center').html("<option =''>Chose Center</option>");
					for (var field in obj.result) {
						$('#center').append($('<option>', {
							value: field,
							text: obj.result[field]
						}));
					}
				} else {
					$('#error_msg').html('No rows found.');
				}
			},
			error: function (response) {
				$('#error_msg').html('Something went wrong.');
			}
		});
	}
	else {
		$('#error_msg').html('Something went wrong.');
	}
}
</script>