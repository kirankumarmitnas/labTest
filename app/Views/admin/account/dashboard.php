<?php
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use CodeIgniter\Pager\PagerRenderer;
$request=\Config\Services::request();
$prePath=AdminConfig::get('prePath'); 
helper('cookie');
echo view($prePath.'common/headerSection'); 
//$statisticResult=CommonMethods::getDashboardStatistics(0);
$timing = new \Config\Timing();
$today = $timing->today;
$isDownload=0;
$backupDownload=get_cookie('backupDownload');
if(empty($backupDownload))
{
	$endTime=(60*60*(24-intval(date("H"))));
	$cookie = array('name'=> 'backupDownload','value'  => uniqid(),'expire' => $endTime);
	set_cookie($cookie);
	$isDownload=1;
}
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-self-start justify-content-start">
<div class="col-md-12 mb-2">
<p class="h4">Dashboard</p>
</div>
<?php
$totalRegistration=0;
$totalAmount=0;
$offlineRegistration=0;
$onlineRegistration=0;
$totalAccompanying=0;
$userPermissions=0;
$userPermissionsStatus=0;
$totalDelegates=0;
$totalStudents=0;
$onlineAmount=0;
if(isEmptyArray($statisticResult)>0)
{
$userPermissions=checkVariable($statisticResult['userPermissions'],0);	
$userPermissionsStatus=isEmptyArray($userPermissions);
$totalRegistration=checkVariable($statisticResult['registration']['total'],0,'intval');
$offlineRegistration=checkVariable($statisticResult['registration']['offline'],0,'intval');
$onlineRegistration=checkVariable($statisticResult['registration']['online'],0,'intval');
$totalAccompanying=checkVariable($statisticResult['accompanying']['total'],0,'intval');
$totalAmount=checkVariable($statisticResult['registration']['amount'],0,'doubleval');
$onlineAmount=checkVariable($statisticResult['registration']['amount'],0,'onlineAmount');
$totalDelegates=checkVariable($statisticResult['registration']['dalegates'],0,'intval');
$totalStudents=checkVariable($statisticResult['registration']['students'],0,'intval');
}
$statisticsList=array(
array('title'=>'Total Registration','value'=>$totalRegistration,'icon'=>'fa fa-hospital-user mr-2 fa-1x','background'=>'bg-gradient bg-primary','menuID'=>2),
array('title'=>'Total Amount','value'=>convertIntoIndianRupesh($totalAmount),'icon'=>'fa-solid fa-indian-rupee-sign mr-2 fa-1x','background'=>'bg-gradient bg-success','menuID'=>49),
array('title'=>'Online Registration','value'=>$onlineRegistration,'icon'=>'fas fa-globe-americas mr-2 fa-1x','background'=>'bg-gradient bg-warning','menuID'=>2),
array('title'=>'Online Receive Amount','value'=>convertIntoIndianRupesh($onlineAmount),'icon'=>'fas fa-indian-rupee-sign  mr-2 fa-1x','background'=>'bg-gradient bg-success','menuID'=>2),
array('title'=>'Offline Registration','value'=>$offlineRegistration,'icon'=>'fas fa-house-user mr-2 fa-1x','background'=>'bg-gradient bg-info','menuID'=>2),
array('title'=>'Total Accompanying','value'=>$totalAccompanying,'icon'=>'fas fa-users mr-2 fa-1x','background'=>'bg-gradient bg-danger','menuID'=>2),
array('title'=>'Total Delegates','value'=>$totalDelegates,'icon'=>'fas fa-user-md mr-2 fa-1x','background'=>'bg-gradient bg-success','menuID'=>2),
array('title'=>'Total Students','value'=>$totalStudents,'icon'=>'fas fa-user-graduate mr-2 fa-1x','background'=>'bg-gradient bg-primary','menuID'=>2),


);
foreach($statisticsList as $list){
	$extra=checkVariable($list['extra'],'');
	$extraValue=checkVariable($list['extraValue'],0,'intval');
	$menuID=checkVariable($list['menuID'],0,'intval');
	if($menuID>0 )//$userPermissionsStatus>0 && && in_array($menuID,$userPermissions)==true 
	{
?>

<div class="col-md-3">
<div class="card m-2 <?php echo checkVariable($list['background'],'');?>  shadow-sm">
<div class="card-body">
<div class="d-flex">
<div class="me-auto  p-2 bd-highlight">
<p class=" text-uppercase text-white fs-12 fw-bold"><?php echo checkVariable($list['title'],'');?></p>
<p class="mb-0  text-white"><?php echo checkVariable($list['value'],'');if(!empty($extra)){ echo '('.$extra.')'; } if(!empty($extraValue)){ echo ' / <span class="text-muted">'.$extraValue.'</span>'; } ?></p>
</div>
<div class="p-2 bd-highlight"><i class="text-white <?php echo checkVariable($list['icon'],'');?>"></i></div>
</div>
</div>
</div>
</div>	
<?php } } ?>
</div>

<div class="row p-3 align-self-start justify-content-start">
<div class="col-md-6">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th colspan="3">
<p class="text-start mb-0 h5">Registration List</p>
</th>
</tr>
<tr>
<th>#</th>
<th>Date</th>
<th>Total Registration</th>
</tr>
</thead>
<tbody>
<?php
if(isEmptyArray($statisticResult)>0 )//&& $userPermissionsStatus>0 && $menuID>0 && in_array(2,$userPermissions)==true
{
	$datewiseList=checkVariable($statisticResult['registration']['datewise'],0);
	if(isEmptyArray($datewiseList)>0)
	{
		foreach($datewiseList as $key=>$row)
		{
			$found=checkVariable($row['found'],0,'intval');
			$createdOn=checkVariable($row['createdOn'],0);
			$createdOn=(!empty($createdOn)) ? strtotime($createdOn) : 0;
			?>
			<tr>
			<td><?php echo ($key+1);?></td>
			<td><?php echo (!empty($createdOn)) ? date("d-M, Y",$createdOn) : '';?></td>
			<td class="text-center"><span class="badge <?php echo ($createdOn==$today) ? 'animated infinite flash bg-danger' : 'bg-info';?>  animate"><?php echo $found;?></span></td>
			</tr>
			<?php
		}
	}
}
?>

</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

</div>
</section>
<script type="text/javascript">
$(document).ready(function(e){
   <?php if($isDownload==1) { ?>
   $('<iframe>', {
   src: '<?php echo site_url($prePath."db/backup/download");?>',
   id:  'myFrame',
   frameborder: 0,
   class : 'hide',
   scrolling: 'no'
   }).appendTo('body.authenticate');
   <?php } ?>
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>