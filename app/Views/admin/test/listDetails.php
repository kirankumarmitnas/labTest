<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
use App\Models\Admin\DoctorModel;
use App\Libraries\CommonMethods;
$request=\Config\Services::request();
$prePath=AdminConfig::get('prePath'); 
$permissions=AdminConfig::get('permissions');
$permissionsStatus=isEmptyArray($permissions);
echo view($prePath.'common/headerSection');
$get=$request->getGetPost();
$parameters=array('type'=>0);
$doctorModel = new DoctorModel();
$totalDoctor=$doctorModel->getList($parameters);
if($totalDoctor>0)
{
$parameters2=array('fetchField'=>" srNo,doctorName,designation",'action'=>1,'orderBy'=>' order by doctorName ASC ','isMultiple'=>1,'limit'=>$totalDoctor,'type'=>1);
$doctorList=$doctorModel->getList($parameters2);
}
$doctorListStatus=isEmptyArray($doctorList);
$genderTypes=CommonMethods::getGenderWiseTypes(0);
$genderTypesStatus=isEmptyArray($genderTypes);
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-items-center justify-content-around">
<div class="col-md-6 col-8">
<p class="pageTitle fs-5">Test List</p>
</div>
<div class="col-md-6 col-4 mb-3 text-end">
<a href="<?php echo site_url($prePath.'/test/add');?>" class="btn btn-outline-success btn-sm" ><i class="fas fa-plus me-2"></i>New Test</a>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'test/list'),array('name'=>'searchForm','class'=>'form row justify-content-start','method'=>'GET'));?>
<div class="col-md-3 mb-2">
<label  class="form-label text-dark">Doctor</label>
<?php
$doctor=checkVariable($get['doctor'],0,'intval');
?>
<select class="form-select form-select-sm" name="doctor">
<option <?php if($doctor<=0) { ?> selected <?php } ?> value="0">Doctor</option>
<?php if($doctorListStatus>0) { foreach($doctorList as $sDoctor) {
$doctorName=checkVariable($sDoctor['doctorName'],'','trim');
$designation=checkVariable($sDoctor['designation'],'','trim');
$srNo=checkVariable($sDoctor['srNo'],0,'intval');
if($srNo>0){ ?>
<option <?php if($doctor==$srNo) { ?> selected <?php } ?> value="<?php echo $srNo;?>"><?php echo $doctorName;?></option>
<?php } } }  ?>
</select>
</div>
<div class="col-md-3 mb-2">
<label  class="form-label text-dark"> Search</label>
<div class="input-group mb-3">
<input type="text" class="form-control form-control-sm"   name="search" maxlength="100" placeholder="Search by the patient name or mobile no" value="<?php echo checkVariable($get['search']);?>" />
</div>
</div>

<div class="col-md-1 mb-2">
<?php 
$sortBy=checkVariable($get['sortBy'],0,'intval'); 
?>
<label  class="form-label text-dark">Sort By</label>
<select name="sortBy" class="form-select form-select-sm">
<option <?php if($sortBy==0) { echo 'selected'; } ?> value="0">None</option>
<option <?php if($sortBy==1) { echo 'selected'; } ?> value="1">Year</option>
<option <?php if($sortBy==2) { echo 'selected'; } ?> value="2">Custom</option>
</select>
</div>
<div class="col-md-1 mb-2 <?php if(in_array($sortBy,array(0,2))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="1">
<label  class="form-label text-dark"> Year</label>
<input type="text" class="form-control year form-control-sm"   name="year" placeholder="Year" value="<?php echo checkVariable($get['year']);?>" />
</div>
<div class="col-md-1 mb-2 <?php if(in_array($sortBy,array(0,1))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="2">
<label  class="form-label text-dark"> From Date</label>
<input type="text" class="form-control dateTime form-control-sm"   name="fromDate" placeholder="From Date" value="<?php echo checkVariable($get['fromDate']);?>" />
</div>
<div class="col-md-1 mb-2 <?php if(in_array($sortBy,array(0,1))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="2">
<label  class="form-label text-dark"> To Date</label>
<input type="text" class="form-control dateTime form-control-sm" value="<?php echo checkVariable($get['toDate']);?>"  name="toDate" placeholder="To Date" />
</div>

<div class="col-md-2 mb-2 mt-md-4">
<button type="submit" class="btn btn-success btn-sm" ><i class="fa fa-search me-2"></i>Search</button>
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
	<th scope="col" style="width:10%;">Date</th>
	<th scope="col" style="width:25%;">Doctor Name</th>
	<th scope="col" style="width:30%;">Patient Name</th>
	<th scope="col" style="width:10%;">Mobile No</th>
	<th scope="col" style="width:10%;">Gender</th>
	<th scope="col" style="width:10%;">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$testList=checkVariable($result['testList'],0);
	if(isEmptyArray($testList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($testList as $test)
		{
		$srNo=checkVariable($test['srNo'],0,'intval');
		$patientName=checkVariable($test['patientName'],0,'trim');
		$mobileNo=checkVariable($test['mobileNo'],0,'trim');
		$gender=checkVariable($test['gender'],0,'intval');
		$age=checkVariable($test['age'],0,'intval');
		$testID=checkVariable($test['testID'],0,'trim');
		$doctorID=checkVariable($test['doctorID'],0,'intval');
		$labDate=checkVariable($test['labDate'],0);
		$labDate=(!empty($labDate)) ? date("d-m-Y",strtotime($labDate)) : '';
		$doctorName='';
		$genderName='';
		if($doctorListStatus>0)
		{
			
			$info=searchValueInArray(array('data'=>$doctorList,'search'=>array('srNo'=>$doctorID),'type'=>1,'isSingle'=>1));
			if(isEmptyArray($info)>0){ 
			$doctorName=checkVariable($info['doctorName'],'','trim');
			}
		}
		if($genderTypesStatus>0)
		{
			$info=searchValueInArray(array('data'=>$genderTypes,'search'=>array('id'=>$gender),'type'=>1,'isSingle'=>1));
			if(isEmptyArray($info)>0){ 
			$genderName=checkVariable($info['name'],'','trim');
			}
		}
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
		<td><span class="labDate"><?php echo $labDate;?></span></td>
		<td><span class="doctorName"><?php echo $doctorName;?></span></td>
		<td><span class="patientName"><?php echo $patientName;?></span></td>
		<td><span class="mobileNo"><?php echo $mobileNo;?></span></td>
		<td><span class="gender"><?php echo $genderName;?></span><br><span class="age badge bg-primary"><?php echo $age;?></span></td>
		<td>
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
		<?php if($permissionsStatus>0 && in_array(1,$permissions)==true) { ?>
		<button data-value="<?php echo $srNo; ?>" class="btn btn-warning btn-sm" name="viewBtn"><i class="fas fa-eye "></i></button>
		<?php } ?>
		<?php if($permissionsStatus>0 && in_array(3,$permissions)==true) { ?>
		
		<a href="<?php echo site_url($prePath.'test/update/'.$srNo); ?>" class="btn btn-secondary btn-sm "> <i class="fas fa-edit "></i></a>
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
function getTestDetails(type=0,me=0,parent=0,testID=0)
{
	if(testID>0)
	{	
	var formdata={'testID':testID,'isSevices':1};
	$.ajax({url: "<?php echo site_url($prePath.'/test/details');?>",type: "POST",data:formdata,cache:false,
	beforeSend:function(){ me.prop("disabled",true);},
	error:function(){ me.prop("disabled",false); },
	complete:function(){ me.prop("disabled",false); },
	success: function(data)   
	{
		me.prop("disabled",false);
		if(data!='' && data.length>1)
		{
			var result=$.parseJSON(data);
			if($.isEmptyObject(result)==false)
			{
				var table=parent.find("table#testServiceList") || 0;
				var txt='';
				var totalAmount=0;
				var commisionAmount=0;
				$.each(result,function(ind,obj2){
					var testName=isset(obj2.testName) ? obj2.testName : '';
					var amount=isset(obj2.amount) ? toAmount(obj2.amount) : 0;
					var discountValue=isset(obj2.discountValue) ? toAmount(obj2.discountValue) : 0;
					commisionAmount+=discountValue;
					totalAmount+=amount;
					txt+='<tr>'+
					'<td>'+(ind+1)+'</td>'+
					'<td>'+testName+'</td>'+
					'<td><i class="fas fa-indian-rupee-sign mx-2"></i>'+amount+'</td>'+
					'<td><i class="fas fa-indian-rupee-sign mx-2"></i>'+discountValue+'</td>'+
					'</tr>';
				});
				var txt2='<tr>'+
					'<td colspan="2"></td>'+
					'<td>Total Amount=<i class="fas fa-indian-rupee-sign mx-2"></i>'+totalAmount+'</td>'+
					'<td>Total Commission=<i class="fas fa-indian-rupee-sign mx-2"></i>'+commisionAmount+'</td>'+
					'</tr>';
				table.find("tbody").html(txt);
				table.find("tfoot").html(txt2);
				dialogBox.show();
			}
		}
		
	}
	});
	}	
}
$(document).ready(function(e){
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
	});
	$('.select2').select2();
	$('.table').DataTable({"paging": false,"lengthChange": true,"searching": true,"ordering": true,"info": true,"autoWidth": false,});
	$(".dateTime").datetimepicker({format: 'DD-MM-YYYY'});
	$(".year").datetimepicker({format: 'YYYY'});
	$('form[name="searchForm"] select[name="sortBy"]').on("change",function(e){
		var t=$(this);
		var parent=t.closest("form") || 0;
		var type=toNumber(t.val() || 0);
		parent.find('sortByBlcok').addClass("d-none");
		if(type==1)
		{
			parent.find('.sortByBlcok[data-type="1"]').removeClass("d-none").find("input").val('');
			parent.find('.sortByBlcok[data-type="2"]').addClass("d-none").find("input").val('');
		}
		else if(type==2)
		{
			parent.find('.sortByBlcok[data-type="1"]').addClass("d-none").find("input").val('');
			parent.find('.sortByBlcok[data-type="2"]').removeClass("d-none").find("input").val('');
		}
		
	});
	$('body').on('click','button[name="viewBtn"]',function(e){
		var t=$(this);
		var parent=t.closest('tr') || 0;
		var testID=toNumber(t.attr("data-value") || 0);
		var labDate=parent.find(".labDate").text() || '';
		var doctorName=parent.find(".doctorName").text() || '';
		var patientName=parent.find(".patientName").text() || '';
		var mobileNo=parent.find(".mobileNo").text() || '';
		var gender=parent.find(".gender").text() || '';
		var age=parent.find(".age").text() || '';
		if(testID>0)
		{
			var input='<div class="row">'+
			'<div class="col-md-12 mb-2">'+
			'<p class="dPatientName displayValue">Patient Name: <b>'+patientName+'</b></p>'+
			'</div>'+
			'<div class="col-md-4 mb-2">'+
			'<p class="dMobileNo displayValue">Mobile No: <b>'+mobileNo+'</b></p>'+
			'</div>'+
			'<div class="col-md-4 mb-2">'+
			'<p class="dGender displayValue">Gender: <b>'+gender+'</b></p>'+
			'</div>'+
			'<div class="col-md-4 mb-2">'+
			'<p class="dAge displayValue"> Age: <b>'+age+'</b></p>'+
			'</div>'+
			'<div class="col-md-8 mb-2">'+
			'<p class="dDoctorName displayValue"> Doctor Name: <b>'+doctorName+'</b></p>'+
			'</div>'+
			'<div class="col-md-4 mb-2">'+
			'<p class="dDate displayValue"> Date: <b>'+labDate+'</b></p>'+
			'</div>'+
			'<div class="col-md-12 mb-2">'+
			'<div class="table-responsive">'+
			'<table class="table table-bordered align-middle" id="testServiceList" >'+
			'<thead>'+
			'<tr>'+
			'<th colspan="5" width="100%" class="text-start">Service Details</th>'+
			'</tr>'+
			'<tr>'+
			'<th width="10%">#</th>'+
			'<th width="50%">Service Name</th>'+
			'<th width="20%">Amount</th>'+
			'<th width="20%">Commission Amount </th>'+
			'</tr>'+
			'</thead>'+
			'<tbody></tbody>'+
			'<tfoot></tfoot>'+
			'</table>'+
			'</div>'+
			'</div>'+
			'</div>';
			if(input!='')
			{
				input=$.parseHTML(input);
				input=$(input);
				dialogModel.find(".modal-title").text('Test Details');
				dialogModel.find(".modal-body .container-fluid").html(input);
				dialogModel.find(".modal-dialog").addClass('modal-xl modal-dialog-scrollable');
				dialogModel.find(".modal-footer").addClass('d-none');
				//dialogBox.show();
				getTestDetails(0,t,dialogModel,testID);
			}
		}
	});
	$("body").on("click",'button[name="removeBtn"]',function(e){
		var t=$(this);
		var testID=toNumber(t.attr('data-value') || 0);
		if(testID>0)
		{
		$.confirm({	
		title: 'Confirm!',	
		content: 'Are you sure?',
		buttons: {
		cancel: function () {},
		yes: {
		text: 'Yes', // With spaces and symbols
		action: function () {
		var formdata={'testID':testID};
		$.ajax({url: "<?php echo site_url($prePath.'/test/remove');?>",type: "POST",data:formdata,cache:false,
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
				content: 'Invalid service ID',
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