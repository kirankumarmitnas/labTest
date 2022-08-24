<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
use App\Models\Admin\CategoryModel;
use App\Libraries\CommonMethods;
use App\Models\Admin\DoctorModel;
use App\Models\Admin\ServiceModel;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
$session = \Config\Services::session(); 
$errors=$session->getFlashdata('errors');
$status=0;
$msg=0;
if(isEmptyArray($errors)>0)
{
	$status=checkVariable($errors['status'],0,'intval');
	$msg=checkVariable($errors['msg'],0);
}
$categoryModel = new CategoryModel();
$doctorModel = new DoctorModel();
$serviceModel = new ServiceModel();
$genderTypes=CommonMethods::getGenderWiseTypes(0);
$parameters=array('type'=>0);
$categoryList=0;
$doctorList=0;
$serviceList=0;
$totalCategory=$categoryModel->getList($parameters);
if($totalCategory>0)
{
$parameters2=array('fetchField'=>" srNo,name ",'action'=>1,'orderBy'=>' order by cOrder ASC ','isMultiple'=>1,'limit'=>$totalCategory,'type'=>1);
$categoryList=$categoryModel->getList($parameters2);
}
$totalDoctor=$doctorModel->getList($parameters);
if($totalDoctor>0)
{
$parameters2=array('fetchField'=>" srNo,doctorName,designation,hospitalName,mobileNo ",'action'=>1,'orderBy'=>' order by doctorName ASC ','isMultiple'=>1,'limit'=>$totalDoctor,'type'=>1);
$doctorList=$doctorModel->getList($parameters2);
}
$totalService=$serviceModel->getList($parameters);
if($totalService>0)
{
$parameters2=array('fetchField'=>" srNo,testName,categoryID, amount, discountType,discountValue ",'action'=>1,'orderBy'=>' order by testName ASC ','isMultiple'=>1,'limit'=>$totalService,'type'=>1);
$serviceList=$serviceModel->getList($parameters2);
}
$categoryListStatus=isEmptyArray($categoryList);
$doctorListStatus=isEmptyArray($doctorList);
$serviceListStatus=isEmptyArray($serviceList);
$request=\Config\Services::request();
$prePath=AdminConfig::get('prePath'); 
$permissions=AdminConfig::get('permissions');
$permissionsStatus=isEmptyArray($permissions);
echo view($prePath.'common/headerSection');

?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-items-center justify-content-around">
<div class="col-md-12 col-12">
<p class="pageTitle fs-5">Add Test Details</p>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'test/add'),array('name'=>'testForm','class'=>'form row justify-content-start','method'=>'POST'));?>

<?php if(in_array($status,array(1,0,-11))==false) { ?>
<div class="col-md-12 mt-3 col-ms-12">
<div class="alert alert-warning alert-dismissible fade show" role="alert">
   <?php echo $msg; ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
</div>
<?php }  ?>

<div class="col-md-12">
<div class="row">
<div class="col-md-12 mt-3">
<p class="fw-bold">Patient Details:-</p>
</div>
<div class="col-md-2 mb-2">
<label  class="form-label text-dark">referred Doctor Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>
<?php
$doctor=old('doctor');
?>
<select class="form-select form-select-sm importantFields <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('doctor',$msg)==true){ echo 'is-invalid'; } ?>" name="doctor" >
<option <?php if($doctor<=0) { ?> selected <?php } ?> value="">Doctor</option>
<?php if($doctorListStatus>0) { foreach($doctorList as $doctorInfo) {
$doctorName=checkVariable($doctorInfo['doctorName'],'','trim');
$designation=checkVariable($doctorInfo['designation'],'','trim');
$hospitalName=checkVariable($doctorInfo['hospitalName'],'','trim');
$srNo=checkVariable($doctorInfo['srNo'],0,'intval');
if($srNo>0){
?>
<option <?php if($doctor==$srNo) { ?> selected <?php } ?> value="<?php echo $srNo;?>"><?php echo $doctorName.'('.$designation.')';?></option>
<?php } } }  ?>
</select>
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('doctor',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['doctor'],'','trim');?></div><?php } ?>
</div>

<div class="col-md-2">
<div class="mb-2">
<label  class="form-label text-dark">Patient Name:-<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>
<input type="text" class="form-control form-control-sm importantFields <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('patientName',$msg)==true){ echo 'is-invalid'; } ?>"   name="patientName" maxlength="150" placeholder="Patient Name" value="<?php echo old('patientName') ?>"  />
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('patientName',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['patientName'],'','trim');?></div><?php } ?>

</div>
</div>

<div class="col-md-2">
<div class="mb-2">
<label  class="form-label text-dark">Mobile No:-<i class="ms-1 fa-solid fa-asterisk requiredLable"></i></label>
<input type="text" class="form-control form-control-sm onlyNumber validateMobile importantFields <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('mobileNo',$msg)==true){ echo 'is-invalid'; } ?>"   name="mobileNo" maxlength="15" placeholder="Mobile No" value="<?php echo old('mobileNo') ?>"  />
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('mobileNo',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['mobileNo'],'','trim');?></div><?php } ?>
</div>
</div>

<div class="col-md-1">
<div class="mb-2">
<label  class="form-label text-dark"> Age:-<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>
<input type="text" class="form-control form-control-sm onlyNumber importantFields <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('age',$msg)==true){ echo 'is-invalid'; } ?>"   name="age" maxlength="3" placeholder="Age" value="<?php echo old('age') ?>"   />
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('age',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['age'],'','trim');?></div><?php } ?>
</div>
</div>

<div class="col-md-1">
<div class="mb-3">
<?php
$testDate=old('testDate');
$testDate=(empty(trim($testDate))) ? date("d-m-Y") : $testDate;
?>
<label  class="form-label text-dark">Date:-<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>
<input type="text" class="form-control datetime form-control-sm onlyNumber importantFields <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('testDate',$msg)==true){ echo 'is-invalid'; } ?>"   name="testDate" maxlength="15" placeholder="Date" value="<?php echo $testDate; ?>"  />
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('testDate',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['testDate'],'','trim');?></div><?php } ?>
</div>
</div>

<div class="col-md-3">
<div class="my-2 mt-md-4 text-start">
<label  class="form-label text-dark"> Gender:-<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>
<?php 
$gender=old('gender');
if(isEmptyArray($genderTypes)>0) { foreach($genderTypes as $genderInfo) { 
$id=checkVariable($genderInfo['id'],0,'intval');
$gName=checkVariable($genderInfo['name'],'','trim');
if($id>0) {
?>
<div class="form-check form-check-inline">
  <input class="form-check-input importantFields gender" <?php if($gender==$id) { echo 'checked'; } ?> type="radio" name="gender" id="<?php echo 'gender-'.$id;?>" value="<?php echo $id;?>"  />
  <label class="form-check-label" for="<?php echo 'gender-'.$id;?>"><?php echo $gName;?></label>
</div>
<?php } }  } ?>
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('gender',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['gender'],'','trim');?></div><?php } ?>
</div>
</div>

</div>
</div>




<div class="col-md-12 mt-3 ">
<p class="fw-bold">Service Details:-</p>
</div>
<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('services',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['services'],'','trim');?></div><?php } ?>
<?php
$existsServices=old('services');
$existsServicesStatus=isEmptyArray($existsServices);
if($serviceListStatus>0)
{
if($categoryListStatus<=0)
{
	$categoryList=array(array('srNo'=>0,'name'=>'Common'));
}
else
{
	$categoryList[]=array('srNo'=>0,'name'=>'Common');
}
foreach($categoryList as $category)
{
	$categoryID=checkVariable($category['srNo'],0,'intval');
	$categoryName=checkVariable($category['name'],0,'trim');
	$categoryServices=searchValueInArray(array('data'=>$serviceList,'search'=>array('categoryID'=>$categoryID),'type'=>1));
	if(isEmptyArray($categoryServices)>0)
	{
		?>
		<div class="col-md-12 border-bottom mb-3">
		<div class="row">
		<div class="col-md-12 ">
		<p class="fw-semibold "><i class="fa-solid fa-arrows-to-dot me-2"></i><?php echo $categoryName;?></p>
		</div>
		<?php
		foreach($categoryServices as $catService)
		{
		$testID=checkVariable($catService['srNo'],0,'intval');
		$testName=checkVariable($catService['testName'],0,'trim');
		$amount=checkVariable($catService['amount'],0,'doubleval');
		$discountValue=checkVariable($catService['discountValue'],0,'doubleval');
		?>
		<div class="col-md-2 mb-2">
		<div class="form-check">
		<input class="form-check-input importantFields services" name="services[]" type="checkbox" value="<?php echo $testID;?>"  data-amount="<?php echo $amount;?>" id="<?php echo 'text-'.$testID;?>" <?php if($existsServicesStatus>0 && in_array($testID,$existsServices)==true) { echo 'checked'; } ?>  />
		<label class="form-check-label" for="<?php echo 'text-'.$testID;?>"><?php echo $testName;?><br><span class="serviceAmount" ><i class="fa-solid fa-indian-rupee-sign"></i><?php echo $amount;?></span></label>
		</div>
		</div>
		<?php
		}
		?>
		</div>
		</div>
		<?php
	}
}	
//srNo,name
//
}
?>

<div class="col-md-2 mb-2">
<p class="d-none totalAmount">Total Amount:<i class="ms-2 fa-solid fa-indian-rupee-sign"></i><b></b></p>
<button type="submit" class="btn btn-success btn-md" ><i class="fas fa-save me-2"></i>Save</button>
</div>

<?php echo form_close();?>

</div>
</div>
</div>
</div>
</div>



</div>
</div>
</section>
<script type="text/javascript">
function validateForm(parent)
{
	var response=0;
	if(parent.jquery)
	{
		parent.find('input,select').removeClass('is-invalid');
		parent.find('.invalid-feedback').remove();
		var child=parent.find(".importantFields") || 0;
		if(child.length>0)
		{
			var is_display=0;
			var is_display2=0;
			$.each(child,function(e){
				var t=$(this);
				var val=t.val() || '';
				val=$.trim(val);
				var maxlength=toNumber(t.attr("maxlength") || 0);
				var name=t.attr("name") || '';
				var placeholder=t.attr("placeholder") || '';
				if(placeholder=='')
				{
					placeholder=name;
					placeholder=placeholder.replace(/[_\W]+/g, "");
					placeholder = placeholder.replace(/([-,.€~!@#$%^&*()_+=`{}\[\]\|\\:;'<>])+/g, '');
				}
				
				if(t.is("textarea")!=true  && t.attr("type")!='email' && t.attr("type")!='hidden' && (val.length<=0 || val=='' || val==0))
				{
					response=1;
					t.addClass("is-invalid");
					if(t.is("select"))
					{
						if(t.parent().find(".invalid-feedback").length<=0)
						{
							$('<div class="invalid-feedback"> Please provide a valid '+placeholder+'</div>').insertAfter(t);
						}
						else
						{
							t.parent().find(".invalid-feedback").text('Please provide a valid '+placeholder);	
						}
						/*if ($('.select').data('select2')) {
						$(".select2").select2('destroy');
						}
						$(".select2").select2({"val": ""});*/
					}
					else
					{
						if(t.next('.invalid-feedback').length<=0)
						{
							$('<div class="invalid-feedback"> Please provide a valid '+placeholder+'</div>').insertAfter(t);
						}
						else
						{
							t.parent().find(".invalid-feedback").text('Please provide a valid '+placeholder);
						}
					}
					
				}
				else if(name=='age' && val>100)
				{
					response=1;
					t.addClass("is-invalid");
					if(t.next('.invalid-feedback').length<=0)
					{
						$('<div class="invalid-feedback">  '+placeholder+' between 0 to 100 value</div>').insertAfter(t);
					}
					else
					{
						t.parent().find(".invalid-feedback").text(placeholder+' between 0 to 100 value');
					}
				}
				else if( t.attr("type")=='checkbox')
				{
					var services=[];
					parent.find(".services:checked").each(function(){
					services.push($(this).val());
					});
				   if(services.length<=0)
				   {
					    response=1; 
						if(is_display==0)
						{	
							is_display=1;
							$.alert({
							title: 'Warning!',
							content: 'You must Service for add test',
							});
						}
						parent.find(".services").addClass("is-invalid");
						if(t.parent().next('.invalid-feedback').length<=0)
						{
							$('<div class="invalid-feedback d-block"> Please provide a valid '+placeholder+'</div>').insertAfter(t.parent());
						}
						else
						{
							t.parents().eq(1).find(".invalid-feedback").text('Please provide a valid '+placeholder);
						}
				   }
				}
				else if( t.attr("type")=='radio')
				{
				   var val=toNumber(parent.find(".gender:checked").val() || 0);
				   if(val<=0)
				   {
					  response=1; 
					 if(is_display==0)
					 {	
						is_display=1;
						$.alert({
						title: 'Warning!',
						content: 'You must gender for add test',
						});
						parent.find(".gender").addClass("is-invalid");
						if(t.parents().eq(1).next('.invalid-feedback').length<=0)
						{
							$('<div class="invalid-feedback d-block"> Please provide a valid '+placeholder+'</div>').insertAfter(t.parents().eq(1));
						}
						else
						{
							t.parents().eq(1).find(".invalid-feedback").text('Please provide a valid '+placeholder);
						}
					 }
				   }
				}
				else if(t.hasClass("validateMobile")==true)
				{
					if(val.length!=10)
					{
						response=1;
						t.addClass("is-invalid");
						if(t.next('.invalid-feedback').length<=0)
						{
							$('<div class="invalid-feedback"> Please provide a valid '+placeholder+'</div>').insertAfter(t);
						}
						else
						{
							t.parent().find(".invalid-feedback").text('Please provide a valid '+placeholder);
						}
					}
					else
					{
						t.removeClass("is-invalid");
						if(t.next('.invalid-feedback').length>0)
						{
							t.next('.invalid-feedback').remove();
						}
					}
				}
				else if(t.hasClass('length-validate-none')==false && maxlength>0 && val.length>maxlength)
				{
					response=1;
					t.addClass("is-invalid");
					if(t.next('.invalid-feedback').length<=0)
					{
						$('<div class="invalid-feedback">'+placeholder+' should be '+maxlength+' number </div>').insertAfter(t);
					}
					else
					{
						t.next('.invalid-feedback').text(placeholder+' should be '+maxlength+' number');
					}
				}
				else
				{
					t.removeClass("is-invalid");
					if(t.next('.invalid-feedback').length>0)
					{
						t.next('.invalid-feedback').remove();
					}
				}
			});
		}
		
	}
	
	return response;
}
$(document).ready(function(e){
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
	});
	$('.select2').select2();
	$(".datetime").datetimepicker({format: 'DD-MM-YYYY'});
	$('body').on("submit",'form[name="testForm"]',function(e){
		e.preventDefault();
		var t=$(this);
		var status=validateForm(t);
		if(status==0)
		{
			t[0].submit();
		}
	});
	$('body').on('change','input.services',function(e){
		var t=$(this);
		var parent=t.closest('form') || 0;
		var services=parent.find("input.services:checked") || 0;
		parent.find(".totalAmount").addClass("d-none").find("b").text('');
		if(services.length>0)
		{
			var totalAmount=0;
			services.each(function(index,obj){
				var t2=$(this);
				totalAmount+=toAmount(t2.attr("data-amount"));
			});
			parent.find(".totalAmount").removeClass("d-none").find("b").text(totalAmount);
		}
		
	});
	
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>