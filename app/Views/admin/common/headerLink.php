<?php 
use App\Libraries\AdminConfig;
$pageTitle=AdminConfig::get('title');
$username=(isEmptyArray($user)>0) ? checkVariable($user['username'],'','trim') : '';
$websiteDetails = new \Config\WebsiteDetails();
$projectName=checkVariable($websiteDetails->projectName,'','trim');
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta Http-Equiv="Cache" content="no-cache">
<meta Http-Equiv="Pragma-Control" content="no-cache">
<meta Http-Equiv="Cache-directive" Content="no-cache">
<meta Http-Equiv="Pragma-directive" Content="no-cache">
<meta Http-Equiv="Cache-Control" Content="no-cache">
<meta Http-Equiv="Pragma" Content="no-cache">
<meta Http-Equiv="Expires" Content="0">
<meta Http-Equiv="Pragma-directive: no-cache">
<meta Http-Equiv="Cache-directive: no-cache">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
<meta name="robots" content="noindex, nofollow"/>
<title><?php echo (!empty($pageTitle)) ? trim($pageTitle) : $projectName;?></title>
<!-- Custom fonts for this template-->
<link rel="stylesheet" type="text/css"  href2="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" href="<?php echo base_url('assets/packages/fontawesome6.1.0/css/all.min.css');?>" />
<link rel="stylesheet"  type="text/css"  href="<?php echo base_url('assets/css/animate.css'); ?>" />
<link rel="stylesheet"  type="text/css"  href="<?php echo base_url('assets/css/animate.4.1.1.min.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/packages/bootstrap-5.2.0-beta1/css/bootstrap.min.css');?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/packages/bootstrap-5.2.0-beta1/css/bootstrap-grid.min.css');?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/packages/bootstrap-5.2.0-beta1/css/bootstrap-reboot.min.css');?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.css?v='.time());?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-datetimepicker.min.css');?>"/>
<link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/logo.png');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/packages/jquery-confirm/jquery-confirm.min.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/packages/datatable/datatables.min.css');?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/packages/select2/css/select2.min.css');?>"/>
<script src="<?php echo base_url('assets/packages/jquery/jquery.min.js');?>"  ></script>
</head>
<body class="c-app <?php if(!empty($username)) { ?> authenticate <?php } ?>">