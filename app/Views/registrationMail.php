<?php 
$result=checkVariable($result,0);
$templateType=(isEmptyArray($result)>0) ? checkVariable($result['templateType'],0,'intval') : 0;
$logo1=base_url('assets/images/emailLogo/goacon.png');
$logo2=base_url('assets/images/emailLogo/iocon.png');
$logo3=base_url('assets/images/emailLogo/vvof.png');
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="x-apple-disable-message-reformatting">
<title>CME-2022</title>
<!--[if mso]>
<style>
table {border-collapse:collapse;border-spacing:0;border:none;margin:0;}
div, td {padding:0;}
div {margin:0 !important;}
</style>
<noscript>
<xml>
<o:OfficeDocumentSettings>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
</noscript>
<![endif]-->
<style>
table, td, div, h1, p {
font-family: Arial, sans-serif;
}
@media screen and (max-width: 530px) {
.unsub {
display: block;
padding: 8px;
margin-top: 14px;
border-radius: 6px;
background-color: #555555;
text-decoration: none !important;
font-weight: bold;
}
.col-lge {
max-width: 100% !important;
}
}
@media screen and (min-width: 531px) {
.col-sml {
max-width: 27% !important;
}
.col-lge {
max-width: 73% !important;
}
}
</style>
</head>
<body style="margin:0;padding:0;word-spacing:normal;background-color:#939297;">
<div role="article" aria-roledescription="email" lang="en" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#939297;">
<table role="presentation" style="width:100%;border:none;border-spacing:0;">
<tr>
<td align="center" style="padding:0;">
<!--[if mso]>
<table role="presentation" align="center" style="width:600px;">
<tr>
<td>
<![endif]-->
<?php if($templateType==1) { ?>
<table role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;">
<tr>
<td style="padding:30px;background-color:#ffffff;">
<h1 style="font-size:25px;margin-bottom:30px;text-align:center;"><img style="width:120px;" src="<?php echo base_url('assets/images/emailThumsUpIcon.png');?>" /></h1>
<p style="font-size:25px;margin-bottom:30px;text-align:center;">Congratulation!</p>
<p style="margin-bottom:10px;text-align:center;">Your vaccination certificate is uploaded successfully.</p>
<p style="margin-bottom:10px;text-align:center;">Thank You for making this conference a safe destination.</p>
<p style="margin-bottom:10px;margin-top:25px;text-align:center;">- Team GSS 2022</p>
</td>
</tr>
</table>
<?php }  else { 
$doctorName=(isEmptyArray($result)>0) ? checkVariable($result['doctorName'],'','trim') : '';
$memberNo=(isEmptyArray($result)>0) ? checkVariable($result['memberNo'],'','trim') : '';
$message=(isEmptyArray($result)>0) ? checkVariable($result['message'],'','trim') : '';
?>
<table role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;">
<tr>
<td style="padding:15px 15px 15px 15px;text-align:center;font-size:25px;font-weight:bold;background-color:#007fff;
color: white;line-height:1px;">
<h1 style="text-align:center;">GSS-2022</h1>
</td>
</tr>
<tr>
<td style="padding:30px;background-color:#ffffff;">
<p style="font-size:20px;margin-bottom:30px;margin-to:30px;">Congratulation!</p>
<p style="font-size:20px;margin-bottom:20px;">Dear <b><?php echo $doctorName;?></b></p>
<p style="margin-bottom:10px;">You have been registered successfully for Global Surgeons Summit 2022.</p>
<p style="margin-bottom:10px;">You Registration No. is <b><?php echo $memberNo;?></b>.</p>
<p style="margin-bottom:10px;">For more information please feel free to connect us on gss2022vapi@gmail.com.</p>
<p style="margin-bottom:10px;">Date: <b>10th & 11th June 2022</b>.</p>
<p style="margin-bottom:10px;">Time: <b>8am onwards.</b></p>
<p style="margin-bottom:10px;">Venue: <b>Meril Academy, Vapi.</b></p>
<p style="margin-bottom:10px;">- Thank you.</p>
</td>
</tr>
<tr>
<td style="padding-top:25px;text-align:center;background-color:#393186;padding-bottom:25px;">
<p style="margin:0 0 8px 0;font-size:25px;font-weight:bold;color:#ff9800;">Team Global Surgeons Summit</p>
<p style="font-size:10px;font-weight:bold;color: #fff;margin: 0;">Organised by:-Association of Minimal Access Surgeons of India (AMASI)Vapi Surgeons Association</p>
</td>
</tr>
</table>
<?php } ?>
<!--[if mso]>
</td>
</tr>
</table>
<![endif]-->
</td>
</tr>
</table>
</div>
</body>
</html>