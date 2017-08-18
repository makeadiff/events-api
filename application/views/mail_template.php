<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Please join us</title>
</head>

<body style="background-color:#f9f9f9;">
<style>
@import url('https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700');
* {font-family: 'Roboto Condensed', sans-serif;}
a {text-decoration:none;}
.email-content{background-color:#fff;}
.join-withus {text-transform:uppercase;margin:0;padding:15px 0}
@media (min-width: 768px) {.join-withus {font-size:40px;} .email-content{padding:30px;}}
@media (max-width: 768px) {.join-withus {font-size:30px;} .email-content{padding:5px 10px;}}
</style>
<div style="width:100%;max-width:600px;margin: 15px;">
  <div style="text-align:center;"><img src="http://esmarti.com/demo/madapp/images/mad-logo.png" alt="MAD Logo"/></div>
  <div style="background-color:#fff;margin-top:10px;border-radius:5px;-webkit-border-radius:5px;">
    <div style="background-color:#ED1849;color:#fff;text-align:center;padding:10px 0;border-radius:5px 5px 0 0;-webkit-border-radius:5px 5px 0 0;"><h1 style="" class="join-withus">Join with us</h1></div>
    <div class="email-content" style="padding:10px;">
       <?php if(isset($mail_gretting) && $mail_gretting!=''){ ?>
                <p><?php echo $mail_gretting; ?>,</p>
                <?php } ?> 
                 <?php if(isset($mail_content)){ ?>
                  <?php echo $mail_content; ?> 
               <?php } ?>
        
        
    </div>
    <div style="background-color:#000;padding:10px;text-align:center;font-size:11px;color:rgba(255,255,255,0.8);border-radius:0 0 5px 5px;-webkit-border-radius:0 0 5px 5px;">
    <p>&copy; 2017 | Make A Difference </p>
    <p>#16 C, 1st B Main, 14th C Cross,<br>
Sector 6, HSR Layout,<br>

Bangalore, India - 560102<br>

EMAIL: contact@makeadiff.in </p>
    </div>
  </div>
</div>
</body>
</html>

<!--
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Please join us</title>
</head>

<body style="background-color:#f9f9f9;">
<style>
@import url('https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700');
* {font-family: 'Roboto Condensed', sans-serif;}
a {text-decoration:none;}
</style>
<div style="width:100%;max-width:600px;margin: auto;">
  <div style="text-align:center;"><img src="http://madapp.esmarti.com/images/mad-logo.png" alt="MAD Logo"/></div>
  <div style="background-color:#fff;margin-top:10px;border-radius:5px;-webkit-border-radius:5px;">
    <div style="background-color:#ED1849;color:#fff;text-align:center;padding:0;border-radius:5px 5px 0 0;-webkit-border-radius:5px 5px 0 0;"><h1 style="font-size:40px;text-transform:uppercase;margin:0;padding:15px 0">Join with us</h1></div>
    <div style="padding:30px; background-color:#fff;">
        <?php if(isset($mail_gretting) && $mail_gretting!=''){ ?>
                <p><?php echo $mail_gretting; ?>,</p>
                <?php } ?> 
                 <?php if(isset($mail_content)){ ?>
                  <?php echo $mail_content; ?> 
                <p>--</p>     
        </div>
        <div style="background-color:#000;padding:10px;text-align:center;font-size:11px;color:rgba(255,255,255,0.8);border-radius:0 0 5px 5px;-webkit-border-radius:0 0 5px 5px;">
    <p>&copy; 2017 | Make A Difference </p>
    <p>#16 C, 1st B Main, 14th C Cross,<br>
Sector 6, HSR Layout,<br>

Bangalore, India - 560102<br>

EMAIL: contact@makeadiff.in </p>
    </div>
    <?php } ?> 
  </div>
</div>
</body>
</html>
-->



 
  
