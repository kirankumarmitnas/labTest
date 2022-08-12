<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
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
<div class="col-md-6 col-8">
<p class="pageTitle fs-5">Doctor List</p>
</div>
<div class="col-md-6 col-4 mb-3 text-end">
<button type="button" data-bs-toggle2="offcanvas" data-bs-target2="#canvasModel" aria-controls2="canvasModel" class="btn btn-outline-success btn-sm" name="addDoctorBtn"><i class="fas fa-plus me-2"></i>New Doctor</button>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'customer/list'),array('name'=>'searchForm','class'=>'form row justify-content-between','method'=>'GET'));?>
<div class="col-md-4 mb-2">
<label  class="form-label text-dark"> Search</label>
<div class="input-group mb-3">
<input type="text" class="form-control form-control-sm"   name="search" maxlength="100" placeholder="Search by name,mobile" value="<?php echo checkVariable($get['search']);?>" />
<button type="submit" class="btn btn-success btn-sm" ><i class="fa fa-search me-2"></i>Search</button>
</div>
</div>
<div class="col-md-4 mb-2 d-none">
<label  class="form-label text-dark d-block">Export </label>
<div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
<button type="button" class="btn btn-info" name="exportPDFBtn"><i class="fa fa-file-pdf me-2"></i>PDF</button>
<button type="button" class="btn btn-warning btn-sm" name="exportExcelBtn"><i class="fa fa-file-excel me-2"></i>Excel</button>
</div>
</div>

<input type="hidden" name="process" />
<?php echo form_close();?>
<?php echo form_open_multipart(site_url($prePath.'order/list/export'),array('name'=>'orderFormData', 'class' => 'd-none','method'=>'POST','target'=>'_blank'));?>
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
	<th scope="col" style="width:25%;">Doctor Name</th>
	<th scope="col" style="width:15%;">Designation</th>
	<th scope="col" style="width:25%;">Hospital/Clinic</th>
	<th scope="col" style="width:15%;">Mobile No</th>
	<!--th scope="col" style="width:10%;">Status</th-->
	<th scope="col" style="width:15%;">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$doctorList=checkVariable($result['doctorList'],0);
	if(isEmptyArray($doctorList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($doctorList as $doctor)
		{
			//doctorName,designation,hospitalName,mobileNo,doctorStatus
		$designation=checkVariable($doctor['designation'],0,'trim');
		$srNo=checkVariable($doctor['srNo'],0,'intval');
		$doctorName=checkVariable($doctor['doctorName'],0,'trim');
		$hospitalName=checkVariable($doctor['hospitalName'],0,'trim');
		$mobileNo=checkVariable($doctor['mobileNo'],0,'trim');
		$doctorStatus=checkVariable($doctor['doctorStatus'],0,'intval');
		$updatedOn=checkVariable($doctor['updatedOn'],0);
		?>
		<tr>
		<td>
		<label class="form-check-label">
		<?php echo $i; ?>
		<?php if($permissionsStatus>0 && in_array(4,$permissions)==true) { ?>
		<button data-value="<?php echo $srNo; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="remove Category Details" type="button" class="btn btn-outline-danger  btn-sm ms-1" name="removeBtn"><i class="fas fa-trash "></i></button>
		<?php } ?>
		</label>
		</td>
		<td><span class="doctorName"><?php echo $doctorName;?></td>
		<td><span class="designation"><?php echo $designation;?></span></td>
		<td><span class="hospitalName"><?php echo $hospitalName;?></span></td>
		<td><span class="mobileNo"><?php echo $mobileNo;?></span>
		<?php  if($doctorStatus==1) { ?>
		<button type="button"  data-value="<?php echo $srNo;?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Inactive" class="btn btn-xs no-btn statusBtn noBtn doctorStatusBtn d-none"><i class="fas fa-toggle-on active"></i></button>
		<?php } else { ?>
		<button type="button"  data-value="<?php echo $srNo;?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Active" class=" btn btn-xs no-btn statusBtn noBtn doctorStatusBtn d-none"><i class="fas fa-toggle-off inactive"></i></button>
		<?php } ?>								
		</td>
		<td>
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
		<?php if($permissionsStatus>0 && in_array(1,$permissions)==true) { ?>
		<a href="<?php echo site_url($prePath.'stall/quotation/details/view/'.$srNo); ?>" class="btn btn-secondary btn-sm d-none"><i class="fas fa-eye "></i></a>
		<?php } ?>
		<?php if($permissionsStatus>0 && in_array(3,$permissions)==true) { ?>
		<button data-value="<?php echo $srNo; ?>" class="btn btn-warning btn-sm" name="editBtn"><i class="fas fa-edit "></i></button>
		<?php } ?>
		</div>
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
$(document).ready(function(e){
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
	});
	$('.select2').select2();
	$('.table').DataTable({"paging": false,"lengthChange": true,"searching": true,"ordering": true,"info": true,"autoWidth": false,});
	$("body").on("click","button.doctorStatusBtn",function(e){
	var t=$(this);
	var doctorStatus=(t.find(".active").length>0) ? 0 : 1;
	var doctorID=toNumber(t.attr('data-value') || 0)
	if(doctorID>0)
	{
		$.confirm({	
		title: 'Confirm!',	
		content: 'Are you sure?',
		buttons: {
		cancel: function () {},
		yes: {
		text: 'Yes', // With spaces and symbols
		action: function () {
		var formdata={'doctorID':doctorID,'doctorStatus':doctorStatus};
		$.ajax({url: "<?php echo site_url($prePath.'/doctor/status');?>",type: "POST",data:formdata,cache:false,
		beforeSend:function(){ 
		t.prop("disabled",true);
		},
		error:function(){ t.prop("disabled",false); },
		complete:function(){ t.prop("disabled",false); },
		success: function(data)   
		{
			t.prop("disabled",false);
			var status=0;
			if(data.trim()!='')
			{
				var result=$.parseJSON(data);
				if($.isPlainObject(result)==true && $.isEmptyObject(result)==false)
				{
					status=toNumber(result.status || 0);
				}
			}
			if(status==1)
			{
				//(t.find(".active").length>0) ? 0 : 1;
				if(doctorStatus==1)
				{	
					t.attr("data-original-title","Inactive").find("i").removeClass("inactive fa-toggle-off").addClass("active fa-toggle-on");
				}
				else
				{		
					t.attr("data-original-title","Active").find("i").removeClass("fa-toggle-on active").addClass("inactive fa-toggle-off");
				}
			}
			else if(error==-1)
			{
				$.each(error_info,function(field,msg){
					if(field.trim()!='' && msg.trim()!='')
					{
						t.find("[name='"+field+"']").focus().parent().find(".field-errors").removeClass("hide").html(msg);
					}
				});
			}
			else
			{
				$.alert({
				title: 'Error!',
				content: 'Internal Error Occur!',
				});
			}
		}
		});	
		
		}
		}
		}
		});
	}
	});
	$('button[name="addDoctorBtn"]').on('click',function(e){
		var t=$(this);
		var input='<div class="row">'+
		'<form action="#" name="saveDoctorForm" class="col-md-12" method="POST" enctype="multipart/form-data" accept-charset="utf-8">'+
		'<div class="mb-3">'+
		'<label class="form-label">Doctor Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm" name="doctorName" maxlength="200" placeholder="Doctor Name " required />'+
		'</div>'+
		'<div class="mb-3">'+
		'<label class="form-label">Designation<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm " maxlength="50" name="designation" placeholder="Designation"  required />'+
		'</div>'+
		'<div class="mb-3">'+
		'<label class="form-label">Hospital/Clinic<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm " maxlength="100" name="hospitalName" placeholder="Hospital Name" required />'+
		'</div>'+
		'<div class="mb-3">'+
		'<label class="form-label">Mobile No<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm onlyNumber" maxlength="20" name="mobileNo" placeholder="Mobile No" required />'+
		'</div>'+
		'<div class="mb-3 text-start">'+
		'<button type="submit" class="btn btn-sm btn-success">'+
		'<span><i class="fas fa-save me-2"></i>Save</span>'+
		'<span class="d-none"><i class="fa-solid fa-circle-notch animate__rotateIn animate__animated animate__infinite  animate__faster me-2"></i>Processing</span>'+
		'</button>'+
		'</div>'+
		'</form>'+
		'</div>';
		if(input!='')
		{
			input=$.parseHTML(input);
			input=$(input);
		}
		dialogModel.find(".modal-title").text('New Doctor Details');
		dialogModel.find(".modal-body .container-fluid").html(input);
		dialogModel.find(".modal-footer").addClass('d-none');
		dialogBox.show();
	});
	$('body').on('click','button[name="editBtn"]',function(e){
		var t=$(this);
		var parent=t.closest('tr') || 0;
		var doctorID=toNumber(t.attr("data-value") || 0);
		var doctorName=parent.find('.doctorName').text() || '';
		var mobileNo=parent.find('.mobileNo').text() || '';
		var hospitalName=parent.find('.hospitalName').text() || '';
		var designation=parent.find('.designation').text() || '';
		if(doctorID>0)
		{
			var input='<div class="row">'+
			'<form action="#" name="updateDoctorForm" class="col-md-12" method="POST" enctype="multipart/form-data" accept-charset="utf-8">'+
			'<input type="hidden" name="doctorID"   />'+
			'<div class="mb-3">'+
			'<label class="form-label">Doctor Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm" name="doctorName" maxlength="200" placeholder="Doctor Name " required />'+
			'</div>'+
			'<div class="mb-3">'+
			'<label class="form-label">Designation<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm " maxlength="50" name="designation" placeholder="Designation"  required />'+
			'</div>'+
			'<div class="mb-3">'+
			'<label class="form-label">Hospital/Clinic<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm " maxlength="100" name="hospitalName" placeholder="Hospital Name" required />'+
			'</div>'+
			'<div class="mb-3">'+
			'<label class="form-label">Mobile No<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm onlyNumber" maxlength="20" name="mobileNo" placeholder="Mobile No" required />'+
			'</div>'+
			'<div class="mb-3 text-start">'+
			'<button type="submit" class="btn btn-sm btn-warning">'+
			'<span><i class="fas fa-save me-2"></i>Update</span>'+
			'<span class="d-none"><i class="fa-solid fa-circle-notch animate__rotateIn animate__animated animate__infinite  animate__faster me-2"></i>Processing</span>'+
			'</button>'+
			'</div>'+
			'</form>'+
			'</div>';
			if(input!='')
			{
				input=$.parseHTML(input);
				input=$(input);
				input.find('input[name="doctorName"]').val(doctorName);
				input.find('input[name="designation"]').val(designation);
				input.find('input[name="hospitalName"]').val(hospitalName);
				input.find('input[name="mobileNo"]').val(mobileNo);
				input.find('input[name="doctorID"]').val(doctorID);
			}
			dialogModel.find(".modal-title").text('Edit Doctor Details');
			dialogModel.find(".modal-body .container-fluid").html(input);
			dialogModel.find(".modal-footer").addClass('d-none');
			dialogBox.show();
		}
	});
	$("body").on("submit",'form[name="saveDoctorForm"]',function(e){
		e.preventDefault();
		var t=$(this);
		var submitBtn=t.find('button[type="submit"]');
		$.ajax({
		url: "<?php echo site_url($prePath.'/doctor/add');?>",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function(data) {
			submitBtn.prop("disabled",false).children("span").eq(0).removeClass("d-none");
			submitBtn.children("span").eq(1).addClass("d-none");
			var status=0;
			var msg='';
			if(data!='' && data.length>1)
			{
				var result=$.parseJSON(data);
				if($.isEmptyObject(result)==false)
				{
					status=isset(result.status) ? toNumber(result.status) : 0;
					msg=isset(result.status) ? result.msg : '';
				}
			}
			if(status==1)
			{
				window.location.reload();
			}
			else if($.inArray(status,[-1,-2])!== -1)
			{
				$.alert({
				title: 'Error!',
				content: msg,
				});
			}
			else if(status==-11)
			{
				$.each(msg,function(field,info){
					if(field.trim()!='' && info.trim()!='')
					{
						var validation='<div id="validationServerUsernameFeedback" class="invalid-feedback">'+info+' </div>';
						t.find("[name='"+field+"']").focus().addClass('is-invalid').parent().append(validation);
					}
				});
			}
			else
			{
				$.alert({
				title: 'Error!',
				content: 'Internal Error Occur!',
				});
			}
		},
		error: function() {
			submitBtn.prop("disabled",false).children("span").eq(0).removeClass("d-none");
			submitBtn.children("span").eq(1).addClass("d-none");
		},
		beforeSend: function() {
			submitBtn.prop("disabled",true).children("span").eq(0).addClass("d-none");
			submitBtn.children("span").eq(1).removeClass("d-none");
			t.find("input,select").removeClass("is-invalid");
			t.find(".invalid-feedback").remove();
		},
		});
	});
	$("body").on("submit",'form[name="updateDoctorForm"]',function(e){
		e.preventDefault();
		var t=$(this);
		var submitBtn=t.find('button[type="submit"]');
		$.ajax({
		url: "<?php echo site_url($prePath.'/doctor/update');?>",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function(data) {
			submitBtn.prop("disabled",false).children("span").eq(0).removeClass("d-none");
			submitBtn.children("span").eq(1).addClass("d-none");
			var status=0;
			var msg='';
			if(data!='' && data.length>1)
			{
				var result=$.parseJSON(data);
				if($.isEmptyObject(result)==false)
				{
					status=isset(result.status) ? toNumber(result.status) : 0;
					msg=isset(result.status) ? result.msg : '';
				}
			}
			if(status==1)
			{
				window.location.reload();
			}
			else if($.inArray(status,[-1,-2])!== -1)
			{
				$.alert({
				title: 'Error!',
				content: msg,
				});
			}
			else if(status==-11)
			{
				$.each(msg,function(field,info){
					if(field.trim()!='' && info.trim()!='')
					{
						var validation='<div id="validationServerUsernameFeedback" class="invalid-feedback">'+info+' </div>';
						t.find("[name='"+field+"']").focus().addClass('is-invalid').parent().append(validation);
					}
				});
			}
			else
			{
				$.alert({
				title: 'Error!',
				content: 'Internal Error Occur!',
				});
			}
		},
		error: function() {
			submitBtn.prop("disabled",false).children("span").eq(0).removeClass("d-none");
			submitBtn.children("span").eq(1).addClass("d-none");
		},
		beforeSend: function() {
			submitBtn.prop("disabled",true).children("span").eq(0).addClass("d-none");
			submitBtn.children("span").eq(1).removeClass("d-none");
			t.find("input,select").removeClass("is-invalid");
			t.find(".invalid-feedback").remove();
		},
		});
	});	
	
	$("body").on("click",'button[name="removeBtn"]',function(e){
		var t=$(this);
		var doctorID=toNumber(t.attr('data-value') || 0);
		if(doctorID>0)
		{
		$.confirm({	
		title: 'Confirm!',	
		content: 'Are you sure?',
		buttons: {
		cancel: function () {},
		yes: {
		text: 'Yes', // With spaces and symbols
		action: function () {
		var formdata={'doctorID':doctorID};
		$.ajax({url: "<?php echo site_url($prePath.'/doctor/remove');?>",type: "POST",data:formdata,cache:false,
		beforeSend:function(){ 
		t.prop("disabled",true);
		},
		error:function(){ t.prop("disabled",false); },
		complete:function(){ t.prop("disabled",false); },
		success: function(data)   
		{
			t.prop("disabled",false);
			var status=0;
			var msg='';
			if(data!='' && data.length>1)
			{
				var result=$.parseJSON(data);
				if($.isEmptyObject(result)==false)
				{
					status=isset(result.status) ? toNumber(result.status) : 0;
					msg=isset(result.status) ? result.msg : '';
				}
			}
			if(status==1)
			{
				window.location.reload();
			}
			else if(status==-2)
			{
				$.alert({
				title: 'Warning',
				content: 'Invalid Member ID',
				});
				t.find("input").val('');
			}
			else
			{
				$.alert({
				title: 'Error!',
				content: 'Internal Error Occur!',
				});
			}
		}
		});	
		}
		}
		}
		});	
		}
	});
	
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>