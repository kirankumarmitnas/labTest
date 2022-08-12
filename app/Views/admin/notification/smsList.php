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
$mediaStatusList=CommonMethods::getMediaStatusList(0);
$SMSErrorList=CommonMethods::getSMSErrorList(0);
$totalRecords=(isEmptyArray($result)>0) ? checkVariable($result['totalRecords'],0,'intval') : 0;
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-items-center justify-content-around">
<div class="col-md-12 col-12">
<p class="pageTitle fs-5">SMS List</p>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'notification/email/list'),array('name'=>'searchForm','class'=>'form row justify-content-start','method'=>'GET'));?>
<div class="col-md-2">
<label class="form-label text-dark">Status</label>
<?php
$status=checkVariable($get['status'],-11);
?>
<select class="form-select form-select-sm filter1" name="status" id="registerFrom">
<option <?php echo ($status==-1) ?  'selected' : '';?> value="">None</option>
<?php
if( isEmptyArray($mediaStatusList)>0)
{
foreach($mediaStatusList as $key=> $row) {
$name=checkVariable($row['name'],'','trim');
$id=checkVariable($row['id'],'','trim');
?>
<option <?php echo ($id==$status) ?  'selected' : '';?> value="<?php echo $id; ?>"><?php echo ucwords($name); ?></option>
<?php } } ?>
</select>
</div>

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
	<p>Total records found:<span class="badge bg-success ms-2"><?php echo $totalRecords;?></span></p>
	</div>
	<div class="col-md-12">
	<div class="table-responsive">
	<table class="table">
	<thead>
	<tr>
	<th scope="col" style="width:5%;">#</th>
	<th scope="col" style="width:15%;">Date</th>
	<th scope="col" style="width:35%;">Message for</th>
	<th scope="col" style="width:30%;">Send To</th>
	<th scope="col" style="width:10%;">Status</th>
	<th scope="col" style="width:10%;">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$mediaList=checkVariable($result['mediaList'],0);
	if(isEmptyArray($mediaList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($mediaList as $row)
		{
		$recipient=checkVariable($row['recipient'],'','trim');
		$subject=checkVariable($row['subject'],'','trim');
		$createdOn=checkVariable($row['dateTime'],0);
		$message=checkVariable($row['message'],'','trim');
		$messageFor=checkVariable($row['messageFor'],'','trim');
		$id=checkVariable($row['id'],0,'intval');
		$type=checkVariable($row['type'],0,'intval');
		$mStatus=checkVariable($row['mStatus'],0,'intval');	
		$info=searchValueInArray(array('data'=>$mediaStatusList,'search'=>array('id'=>$mStatus),'type'=>1,'isSingle'=>1));
		$statusName='';
		$statusCls='';
		if(isEmptyArray($info)>0){ 
		$statusName=checkVariable($info['name'],'','trim');
		$statusCls=checkVariable($info['className'],'','trim');
		}
		else
		{
			$statusName='fail';
			$statusCls='badge bg-danger';
		}
		
		$messageFor=(!empty($messageFor)) ? ucwords(str_replace('_',' ',$messageFor)) : '';
		?>
		<tr>
		<td><?php echo $i;?></td>
		<td data-sort="<?php echo strtotime($createdOn); ?>" ><span class="dateTime"><?php echo date("d-m-Y h:i A",strtotime($createdOn)); ?></span></td>
		<td><span class="messageFor"><span><?php echo $messageFor;?></span></td>
		<td><span class="sendTo"><span><?php echo $recipient;?></span></td>
		<td><span class="sendStatus" data-value="<?php echo $mStatus;?>"><span class="<?php echo $statusCls;?>"><?php echo $statusName;?></span></td>
		<td>
		<?php if($permissionsStatus>0 && in_array(1,$permissions)==true) { ?>
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
		<button type="button" data-id="<?php echo $id;?>"  title="view details" class="btn btn-sm btn-info viewBtn"><i class="fa fa-eye"></i></button>
		</div>
		<p class="d-none subject"><?php echo $subject;?></p>
		<p class="d-none mediaType"><?php echo $type;?></p>
		<textarea class="d-none messageText"><?php echo nl2br($message);?></textarea>
		<?php } ?>
		</td>
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
let errorList='<?php echo json_encode($SMSErrorList,JSON_HEX_APOS);?>';
if(errorList!='')
{
	errorList=$.parseJSON(errorList);
}
$(document).ready(function(e){
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
	});
	$('.select2').select2();
	$('.table').DataTable({"paging": false,"lengthChange": true,"searching": true,"ordering": true,"info": true,"autoWidth": false,});
	$(".datetime").datetimepicker({format: 'DD-MM-YYYY'});
	$("body").on("click","table tbody tr button.viewBtn",function(e){
		var t=$(this);
		var parent=t.parents('tr') || 0;
		var id=toNumber(t.attr("data-id") || 0);
		var date=parent.find(".dateTime").text() || '';
		var mediaType=parent.find(".mediaType").text() || '';
		var messageFor=parent.find(".messageFor").text() || '';
		var sendStatus=toNumber(parent.find(".sendStatus").attr("data-value") || 0);
		var sendStatusName=parent.find(".sendStatus").html() || '';
		var sendTo=parent.find(".sendTo").text() || '';
		var subject=parent.find(".subject").text() || '';
		var message=parent.find(".messageText").val() || '';
		if(id>0)
		{
			var txt='<div class="row member-info">'+
			'<div class="col-md-12">'+
			'<p><i class="fas fa-id-card me-2"></i><b>Message For:</b><span class="ms-2 d-val">'+messageFor+'</span></p>'+
			'</div>'+
			'<div class="col-md-12">'+
			'<p><i class="fas fa-user me-2"></i><b>Send To:</b><span class="ms-2 d-val">'+sendTo+'</span></p>'+
			'</div>'+
			'<div class="col-md-5">'+
			'<p><i class="fas fa-clock me-2"></i><b>Status:</b><span class="ms-2 d-val">'+sendStatusName+'</span></p>'+
			'</div>'+
			'<div class="col-md-7">'+
			'<p><i class="fas fa-clock me-2"></i><b>Date:</b><span class="ms-2 d-val">'+date+'</span></p>'+
			'</div>';
			if(sendStatus!=1)
			{
				if(sendStatus==0)
				{
					var errorMsg='SMS not sended at the server';
					txt+='<div class="col-md-12">'+
					'<p><i class="fas fa-exclamation-triangle text-danger me-2"></i><b>Fail Status:</b><span class="ms-2 d-val">'+errorMsg+'</span></p>'+
					'</div>';
				}
				else if(sendStatus==-1)
				{
					var errorMsg='SMS sening Failed at server';
					txt+='<div class="col-md-12">'+
					'<p><i class="fas fa-exclamation-triangle text-danger me-2"></i><b>Fail Status:</b><span class="ms-2 d-val">'+errorMsg+'</span></p>'+
					'</div>';
				}
				else
				{
					var result = arraySearchValue(1,errorList,sendStatus,'code');
					if($.isEmptyObject(result)==false)
					{
						result=result[0];
						var errorMsg=isset(result.msg) ? result.msg : '';
						if(errorMsg!='')
						{
						txt+='<div class="col-md-12">'+
						'<p><i class="fas fa-exclamation-triangle text-danger me-2"></i><b>Fail Status:</b><span class="ms-2 d-val">'+errorMsg+'</span></p>'+
						'</div>';
						}
					}
				}
			}
			if(mediaType==2)
			{
			txt+='<div class="col-md-12">'+
			'<p><i class="fas fa-receipt me-2"></i><b>Subject:</b><span class="ms-2 d-val">'+subject+'</span></p>'+
			'</div>';
			}
			txt+='<div class="col-md-12">'+
			'<p class="member-message"><i class="fas fa-envelope me-2"></i><b>Message:</b></p></div>'+
			'<div class="col-md-12 border p-4"  id="message-block">'+message+'</div>'+
			'</div>';
			if(txt!='')
			{
			dialogModel.find(".modal-title").text('Message Details');
			dialogModel.find(".modal-body .container-fluid").html(txt);
			dialogModel.find(".modal-footer").addClass('d-none');
			dialogBox.show();
			
			}
		}
		
	});
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>