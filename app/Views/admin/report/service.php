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
$reportOptions=CommonMethods::getReportOptions(0);
$reportOptionsStatus=isEmptyArray($reportOptions);
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-items-center justify-content-around">
<div class="col-md-12 col-12">
<p class="pageTitle fs-5">Servicewise Report</p>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'report/service'),array('name'=>'searchForm','class'=>'form row justify-content-start','method'=>'GET'));?>

<?php if($reportOptionsStatus>0){ 
$yearList=checkVariable($reportOptions['yearList'],0);
$monthList=checkVariable($reportOptions['monthList'],0);
if(isEmptyArray($yearList)>0) {
$fYear=checkVariable($get['year'],0,'intval');	
?>
<div class="col-md-2 mb-2">
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
<div class="col-md-2 mb-2">
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

<div class="col-md-4 mb-2 ">
<label  class="form-label text-dark d-block">Export </label>
<div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
<button type="submit" class="btn btn-success btn-sm" ><i class="fa fa-search me-2"></i>Search</button>
<button type="button" class="btn btn-primary" name="exportPDFBtn"><i class="fa fa-print me-2"></i>Print</button>
<button type="button" class="btn btn-info" name="exportPDFBtn"><i class="fa fa-file-pdf me-2"></i>PDF</button>
<button type="button" class="btn btn-warning btn-sm" name="exportExcelBtn"><i class="fa fa-file-excel me-2"></i>Excel</button>
</div>
</div>

<input type="hidden" name="exportType" />
<input type="hidden" name="processType" value="3" />
<?php echo form_close();?>

<?php echo form_open_multipart(site_url($prePath.'report/export'),array('name'=>'reportFormData', 'class' => 'd-none','method'=>'POST','target'=>'_blank'));?>
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
	<th scope="col" style="width:10%;">#</th>
	<th scope="col" style="width:30%;">Service Name</th>
	<th scope="col" style="width:15%;">Total Count</th>
	<th scope="col" style="width:15%;">Amount</th>
	<th scope="col" style="width:15%;">Total Commission</th>
	<th scope="col" style="width:15%;">Total Earning</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$totalEarning=0;
	$totalAmount=0;
	$totalCommission=0;
	$reportList=checkVariable($result['reportList'],0);
	if(isEmptyArray($reportList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($reportList as $test)
		{	
		$totalServices=checkVariable($test['totalServices'],0,'intval');
		$amount=checkVariable($test['totalAmount'],0,'doubleval');
		$commission=checkVariable($test['totalDiscount'],0,'doubleval');
		$testName=checkVariable($test['testName'],0,'trim');
		$earning=($amount>0) ? ($amount-$commission) : 0;
		$totalEarning+=$earning;
		$totalAmount+=$amount;
		$totalCommission+=$commission;
		?>
		<tr>
		<td>
		<label class="form-check-label">
		<?php echo $i; ?>
		</label>
		</td>
		<td><span class="testName badge bg-primary"><?php echo $testName;?></span></td>
		<td><span class="totalServices badge bg-success"><?php echo $totalServices;?></span></td>
		<td><span class="totalAmount"><i class="fa-solid fa-indian-rupee-sign me-2"></i><?php echo convertIntoIndianRupesh($amount);?></span></td>
		<td><span class="totalDiscount"><i class="fa-solid fa-indian-rupee-sign me-2"></i><?php echo convertIntoIndianRupesh($commission);?></span></td>
		<td><span class="earning"><i class="fa-solid fa-indian-rupee-sign me-2"></i><?php echo convertIntoIndianRupesh($earning);?></span></td>
		</tr>
		<?php
		$i++;
		}
	}
	?>
	</tbody>
	<tbody>
	<tr>
	<td colspan="5"><p class="text-end"><b>Total Earning:</b></p></td>
	<td><p class="text-start"><i class="fa-solid fa-indian-rupee-sign me-2"></i><?php echo convertIntoIndianRupesh($totalEarning);?></p></td>
	</tr>
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
	$('button[name="exportExcelBtn"]').on("click",function(e){
		var t=$(this);
		var parent=t.parents('form') || 0;
		parent.find('input[name="exportType"]').val(1);
		//===========Form Data==========//
		var form2=$('form[name="reportFormData"]') || 0;
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
		//console.log(url);
		if($.trim(preURL).length>0 && $.trim(url).length>0)
		{
			$('body').find('.formIframe').remove();
			$('<iframe>', {
			src: url,
			id:  'myFrame',
			frameborder: 0,
			class : 'd-none  formIframe',
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
		var form2=$('form[name="reportFormData"]') || 0;
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
		//console.log(url);
		const data = [...formdata.entries()];
		if($.trim(preURL).length>0 && $.trim(url).length>0)
		{
			$('body').find('.formIframe').remove();
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