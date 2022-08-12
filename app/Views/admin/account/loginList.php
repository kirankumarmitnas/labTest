<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
use App\Libraries\CommonMethods;
$request=\Config\Services::request();
$prePath=AdminConfig::get('prePath'); 
$permissions=AdminConfig::get('permissions');
$permissionsStatus=isEmptyArray($permissions);
echo view($prePath.'common/headerSection');
$get=$request->getGetPost();
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-items-center justify-content-around">
<div class="col-md-12 col-12">
<p class="pageTitle fs-5">Login Historys</p>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'user/login/historys'),array('name'=>'searchForm','class'=>'form row justify-content-start','method'=>'GET'));?>

<div class="col-md-2 ">
<div class="mb-2">
<label class="form-label ">Date</label>
<input type="text" name="dateTime" value="<?php echo checkVariable($get['dateTime'],'');?>" class="form-control form-control-sm datetime" placeholder="Date"/>
</div>
</div>
<div class="col-md-3">
<div class="mb-2">
<label  class="form-label text-dark"> Search</label>
<input type="text" class="form-control form-control-sm"   name="search" maxlength="100" placeholder="Search by name,mobile" value="<?php echo checkVariable($get['search']);?>" />
</div>
</div>

<div class="col-md-3 mt-md-4 mb-2">
<div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
<button type="submit" class="btn btn-success btn-sm" ><i class="fa fa-search me-2"></i>Search</button>
</div>
</div>

<input type="hidden" name="process" />
<?php echo form_close();?>
<?php echo form_open_multipart(site_url($prePath.'member/registration/list/export'),array('name'=>'registerForm', 'class' => 'd-none','method'=>'POST','target'=>'_blank'));?>
<?php echo form_close();?>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-12 p-0">
<div class="card">
  <div class="card-body">
	<div class="row">
	<div class="col-md-12">
	<div class="table-responsive">
	<table class="table">
	<thead>
	<tr>
	<th scope="col" style="width:5%;">#</th>
	<th scope="col" style="width:15%;">Date</th>
	<th scope="col" style="width:30%;">Username</th>
	<th scope="col" style="width:30%;">IP</th>
	<th scope="col" style="width:20%;">Browser</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$loginList=checkVariable($result['loginList'],0);
	if(isEmptyArray($loginList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($loginList as $row)
		{
		$userID=checkVariable($row['userID'],0,'trim');
		$visitorIP=checkVariable($row['visitorIP'],0,'trim');
		$username=checkVariable($row['username'],0,'trim');
		$visitorBrowser=checkVariable($row['visitorBrowser'],0,'trim');
		$createdOn=checkVariable($row['createdOn'],0);
		?>
		<tr>
		<td><?php echo $i;?></td>
		<td data-sort="<?php echo strtotime($createdOn); ?>" ><span class="dateTime"><?php echo date("d-m-Y h:i A",strtotime($createdOn)); ?></span></td>
		<td><span class="username"><?php echo $username; ?></span></td>
		<td><span class="visitorIP"><?php echo $visitorIP; ?></span></td>
		<td><span class="visitorBrowser"><?php echo $visitorBrowser; ?></span></td>
		</tr>
		<?php
		$i++;
		}
	}
	?>
	</tbody>
	</table>
	</div>
	</div>
	<div class="col-md-12">
	<?php
	$pagination=checkVariable($result['pagination'],0);
	if(isEmptyArray($pagination)>0)
	{
	$pagger=checkVariable($pagination['pager'],0);
	$pagerHTML=checkVariable($pagination['pagerHTML'],0);
	echo $pagerHTML;
	}
	?>
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
   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
	});
	$('.select2').select2();
	$('.table').DataTable({"paging": false,"lengthChange": true,"searching": true,"ordering": true,"info": true,"autoWidth": false,});
	$(".datetime").datetimepicker({format: 'DD-MM-YYYY'});
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>