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
$reportOptions=CommonMethods::getReportOptions(0);
$reportOptionsStatus=isEmptyArray($reportOptions);
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-items-center justify-content-around">
<div class="col-md-12 col-12">
<p class="pageTitle fs-5">Payment List</p>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'payment/list'),array('name'=>'searchForm','class'=>'form row justify-content-start','method'=>'GET'));?>
<div class="col-md-3 mb-2 d-none">
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

<div class="col-md-2 mb-2">
<?php 
$sortBy=checkVariable($get['sortBy'],0,'intval'); 
?>
<label  class="form-label text-dark">Sort By</label>
<select name="sortBy" class="form-select form-select-sm">
<option <?php if($sortBy==0) { echo 'selected'; } ?> value="0">None</option>
<option <?php if($sortBy==1) { echo 'selected'; } ?> value="1">Month/Year</option>
<option <?php if($sortBy==2) { echo 'selected'; } ?> value="2">Range</option>
</select>
</div>
<?php if($reportOptionsStatus>0){ 
$yearList=checkVariable($reportOptions['yearList'],0);
$monthList=checkVariable($reportOptions['monthList'],0);
if(isEmptyArray($yearList)>0) {
$fYear=checkVariable($get['year'],0,'intval');	
?>
<div class="col-md-2 mb-2 <?php if(in_array($sortBy,array(0,2))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="1">
<label  class="form-label text-dark">Year</label>
<select name="year" class="form-select form-select-sm" required>
<option <?php if($fYear==0) { echo 'selected'; } ?> value="0">None</option>
<?php foreach($yearList as $year) { ?>
<option <?php if($year==$fYear) { echo 'selected'; } ?> value="<?php echo $year;?>"><?php echo $year;?></option>
<?php }  ?>
</select>
</div>
<?php } 
if(isEmptyArray($monthList)>0) {
$fMonth=checkVariable($get['month'],0,'intval');		
?>
<div class="col-md-2 mb-2 <?php if(in_array($sortBy,array(0,2))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="1" >
<label  class="form-label text-dark">Month</label>
<select name="month" class="form-select form-select-sm">
<option <?php if($fMonth==0) { echo 'selected'; } ?> value="0">None</option>
<?php foreach($monthList as $month) { 
$monthID=checkVariable($month['id'],0,'intval');
$monthName=checkVariable($month['name'],0,'trim');
?>
<option <?php if($monthID==$fMonth) { echo 'selected'; } ?> value="<?php echo $monthID;?>"><?php echo $monthName;?></option>
<?php } ?>
</select>
</div>
<?php }  } ?>

<div class="col-md-2 mb-2 <?php if(in_array($sortBy,array(0,1))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="2">
<label  class="form-label text-dark"> From Date</label>
<input type="text" class="form-control dateTime form-control-sm"   name="fromDate" placeholder="From Date" value="<?php echo checkVariable($get['fromDate']);?>" />
</div>
<div class="col-md-2 mb-2 <?php if(in_array($sortBy,array(0,1))==true ) { ?> d-none <?php } ?> sortByBlcok" data-type="2">
<label  class="form-label text-dark"> To Date</label>
<input type="text" class="form-control dateTime form-control-sm" value="<?php echo checkVariable($get['toDate']);?>"  name="toDate" placeholder="To Date" />
</div>


<div class="col-md-3 mb-2 ">
<label  class="form-label text-dark d-block">Export </label>
<div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
<button type="submit" class="btn btn-success btn-sm" ><i class="fa fa-search me-2"></i>Search</button>
<button type="button" class="btn btn-primary" name="exportPDFBtn"><i class="fa fa-print me-2"></i>Print</button>
<button type="button" class="btn btn-info" name="exportPDFBtn"><i class="fa fa-file-pdf me-2"></i>PDF</button>
<button type="button" class="btn btn-warning btn-sm" name="exportExcelBtn"><i class="fa fa-file-excel me-2"></i>Excel</button>
</div>
</div>

<input type="hidden" name="exportType" />
<?php echo form_close();?>
<?php echo form_open_multipart(site_url($prePath.'payment/list/export'),array('name'=>'formData', 'class' => 'd-none','method'=>'POST','target'=>'_blank'));?>
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
	<th scope="col" style="width:8%;">#</th>
	<th scope="col" style="width:10%;">Date</th>
	<th scope="col" style="width:20%;">Patient Name</th>
	<th scope="col" style="width:50%;">Services</th>
	<th scope="col" style="width:12%;">Amount</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$paymentList=checkVariable($result['paymentList'],0);
	if(isEmptyArray($paymentList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($paymentList as $payment)
		{
		$patientName=checkVariable($payment['patientName'],0,'trim');
		$services=checkVariable($payment['services'],0,'trim');
		if(!empty($services))
		{
			$services='<span class="bg-primary badge me-1">'.str_replace('||','</span><span class="me-1 bg-primary badge">',$services).'</span>';
		}
		$totalAmount=checkVariable($payment['totalAmount'],0,'doubleval');
		$doctorID=checkVariable($payment['doctorID'],0,'intval');
		$labDate=checkVariable($payment['labDate'],0);
		$labDate=(!empty($labDate)) ? date("d-m-Y",strtotime($labDate)) : '';
		$doctorName='';
		if($doctorListStatus>0)
		{
			
			$info=searchValueInArray(array('data'=>$doctorList,'search'=>array('srNo'=>$doctorID),'type'=>1,'isSingle'=>1));
			if(isEmptyArray($info)>0){ 
			$doctorName=checkVariable($info['doctorName'],'','trim');
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
		<td><span class="patientName"><?php echo $patientName;?></span></td>
		<td><span class="services"><?php echo $services;?></span></td>
		<td><i class="fa-solid fa-indian-rupee-sign me-1"></i><?php echo convertIntoIndianRupesh($totalAmount);?></td>
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
		parent.find('.sortByBlcok').addClass("d-none").find("input,select").val('');
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
	$('button[name="exportExcelBtn"]').on("click",function(e){
		var t=$(this);
		var parent=t.parents('form') || 0;
		parent.find('input[name="exportType"]').val(1);
		//===========Form Data==========//
		var form2=$('form[name="formData"]') || 0;
		form2.empty();
		form2.attr("method","GET");
		var forms=parent.find('input,select');
		$.each(forms,function(i,obj){
			form2.append($("<input>").attr("name",$(this).attr("name")).val($(this).val()).attr("type","hidden"));
		});	
		var preURL=form2.attr("action") || '';
		var formdata=new FormData(form2[0]);
		const queryString = new URLSearchParams(formdata).toString();
		var url=preURL+'?'+queryString;
		if($.trim(preURL).length>0 && $.trim(url).length>0)
		{
			$('body').find('.formIframe').remove();
			$('<iframe>', {
			src: url,
			id:  'myFrame',
			frameborder: 0,
			class : 'd-none formIframe',
			scrolling: 'no'
			}).appendTo('body.authenticate');
		}
		//form2[0].submit();
	});
	$('button[name="exportPDFBtn"]').on("click",function(e){
		var t=$(this);
		var parent=t.parents('form') || 0;
		parent.find('input[name="exportType"]').val(2);
		//===========Form Data==========//
		var form2=$('form[name="formData"]') || 0;
		form2.empty();
		form2.attr("method","GET");
		var forms=parent.find('input,select');
		$.each(forms,function(i,obj){
			form2.append($("<input>").attr("name",$(this).attr("name")).val($(this).val()).attr("type","hidden"));
		});
		var preURL=form2.attr("action") || '';
		var formdata=new FormData(form2[0]);
		const queryString = new URLSearchParams(formdata).toString();
		var url=preURL+'?'+queryString;
		const data = [...formdata.entries()];
		//const asString = data.map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`).join('&');
		//console.log(asString);
		if($.trim(preURL).length>0 && $.trim(url).length>0)
		{
			$('body').find('.formIframe').remove();
			/*var iframe = document.createElement('iframe');  
			iframe.style.visibility = "hidden"; 
			iframe.src = url;  
			iframe.class = 'formIframe d-none';        
			document.body.appendChild(iframe);  
			iframe.contentWindow.focus();       
			iframe.contentWindow.print(); */
			$('<iframe>', {
			src: url+'#toolbar=1',
			id:  'myFrame',
			type:"application/pdf",
			frameborder: 0,
			class : 'd-none  formIframe',
			scrolling: 'no'
			}).appendTo('body.authenticate');
			var myFrame=$("#myFrame");
			myFrame[0].contentWindow.focus();       
			myFrame[0].contentWindow.print();
		}
		//form2[0].submit();	
	});
	
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>