<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
use App\Models\Admin\CategoryModel;
$categoryModel = new CategoryModel();
$parameters=array('fetchField'=>" srNo,name ",'action'=>1,'orderBy'=>' order by name ASC ','isMultiple'=>1,'limit'=>10,'type'=>1);
$categoryList=$categoryModel->getList($parameters);
$categoryListStatus=isEmptyArray($categoryList);
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
<p class="pageTitle fs-5">Service List</p>
</div>
<div class="col-md-6 col-4 mb-3 text-end">
<button type="button" data-bs-toggle2="offcanvas" data-bs-target2="#canvasModel" aria-controls2="canvasModel" class="btn btn-outline-success btn-sm" name="addServiceBtn"><i class="fas fa-plus me-2"></i>New Service</button>
</div>
<div class="col-md-12 p-0 mb-3">
<div class="card">
<div class="card-body">
<div class="row">
<div class="col-md-12">
<?php echo form_open_multipart(site_url($prePath.'service/list'),array('name'=>'searchForm','class'=>'form row justify-content-start','method'=>'GET'));?>
<div class="col-md-3 mb-2">
<label  class="form-label text-dark">Category</label>
<?php
$category=checkVariable($get['category'],0,'intval');
?>
<select class="form-select form-select-sm" name="category">
<option <?php if($category<=0) { ?> selected <?php } ?> value="0">Category</option>
<?php if($categoryListStatus>0) { foreach($categoryList as $cInfo) {
$name=checkVariable($cInfo['name'],'','trim');
$srNo=checkVariable($cInfo['srNo'],0,'intval');
if($srNo>0){
?>
<option <?php if($srNo==$category) { ?> selected <?php } ?> value="<?php echo $srNo;?>"><?php echo $name;?></option>
<?php } } }  ?>
</select>
</div>
<div class="col-md-4 mb-2">
<label  class="form-label text-dark"> Search</label>
<div class="input-group mb-3">
<input type="text" class="form-control form-control-sm"   name="search" maxlength="100" placeholder="Search by the test name" value="<?php echo checkVariable($get['search']);?>" />
</div>
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
	<th scope="col" style="width:10%;">#</th>
	<th scope="col" style="width:25%;">Category Name</th>
	<th scope="col" style="width:30%;">Test Name</th>
	<th scope="col" style="width:15%;">Amount</th>
	<th scope="col" style="width:15%;">Commission Amount</th>
	<th scope="col" style="width:5%;">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$serviceList=checkVariable($result['serviceList'],0);
	if(isEmptyArray($serviceList)>0)
	{
		$i = 1;
		if(isset($result['listIndex']) && intval($result['listIndex'])>0)
		{
		$i=$result['listIndex'];
		}
		foreach($serviceList as $service)
		{
		$srNo=checkVariable($service['srNo'],0,'intval');
		$testName=checkVariable($service['testName'],0,'trim');
		$categoryID=checkVariable($service['categoryID'],0,'intval');
		$amount=checkVariable($service['amount'],0,'doubleval');
		$discountValue=checkVariable($service['discountValue'],0,'doubleval');
		$categoryStatus=checkVariable($service['cStatus'],0,'intval');
		$updatedOn=checkVariable($service['updatedOn'],0);
		$categoryName='Other';
		if($categoryListStatus>0)
		{
			$info=searchValueInArray(array('data'=>$categoryList,'search'=>array('srNo'=>$categoryID),'type'=>1,'isSingle'=>1));
			if(isEmptyArray($info)>0){ 
			$categoryName=checkVariable($info['name'],'','trim');
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
		<td><span class="categoryName badge bg-primary" data-value="<?php echo $categoryID;?>"><?php echo $categoryName;?></td>
		<td><span class="testName"><?php echo $testName;?></td>
		<td><span class="amount" data-value="<?php echo $amount;?>"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo convertIntoIndianRupesh($amount);?></td>
		<td><span class="discountValue" data-value="<?php echo $discountValue;?>"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo convertIntoIndianRupesh($discountValue);?></td>
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
	$("body").on("click","button.categoryStatusBtn",function(e){
	var t=$(this);
	var categoryStatus=(t.find(".active").length>0) ? 0 : 1;
	var categoryID=toNumber(t.attr('data-value') || 0)
	if(categoryID>0)
	{
		$.confirm({	
		title: 'Confirm!',	
		content: 'Are you sure?',
		buttons: {
		cancel: function () {},
		yes: {
		text: 'Yes', // With spaces and symbols
		action: function () {
		var formdata={'categoryID':categoryID,'categoryStatus':categoryStatus};
		$.ajax({url: "<?php echo site_url($prePath.'/service/category/status');?>",type: "POST",data:formdata,cache:false,
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
				if(categoryStatus==1)
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
	$('button[name="addServiceBtn"]').on('click',function(e){
		var t=$(this);
		var input='<div class="row">'+
		'<form action="#" name="saveServiceForm" class="col-md-12" method="POST" enctype="multipart/form-data" accept-charset="utf-8">'+
		'<div class="mb-3">'+
		'<label class="form-label">Test Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm" name="testName" maxlength="200" placeholder="Test Name " required />'+
		'</div>'+
		'<div class="mb-3">'+
		'<label class="form-label">Category Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<select class="form-select form-select-sm" name="categoryName">'+
		'<option selected value="">select Category Name</option>'+
		'</select>'+
		'</div>'+
		'<div class="mb-3">'+
		'<label class="form-label">Amount<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm onlyNumber" data-type="1" name="amount" maxlength="13" placeholder="Amount" required />'+
		'</div>'+
		'<div class="mb-3">'+
		'<label class="form-label">Commission Amount<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm onlyNumber" data-type="1" maxlength="13" name="discountValue" maxlength="200" placeholder="Commission Amount " required />'+
		'</div>'+
		'<div class="mb-3 text-start">'+
		'<button type="submit" class="btn btn-sm btn-success">'+
		'<span><i class="fas fa-save me-2"></i>Save</span>'+
		'<span class="d-none"><i class="fa-solid fa-circle-notch animate__rotateIn animate__animated animate__infinite  animate__faster me-2"></i>Processing</span>'+
		'</button>'+
		'</div>'+
		'</form>'+
		'<form action="#" name="saveCategoryForm" class="col-md-12 d-none" method="POST" enctype="multipart/form-data" accept-charset="utf-8">'+
		'<div class="mb-3">'+
		'<label class="form-label">Category Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
		'<input type="text" class="form-control form-control-sm" name="categoryName" maxlength="200" placeholder="Category Name " required />'+
		'</div>'+
		'<div class="mb-3 text-start">'+
		'<button type="submit" class="btn btn-sm btn-success">'+
		'<span><i class="fas fa-save me-2"></i>Save</span>'+
		'<span class="d-none"><i class="fa-solid fa-circle-notch animate__rotateIn animate__animated animate__infinite  animate__faster me-2"></i>Processing</span>'+
		'</button>'+
		'<button type="button" class="btn btn-sm btn-danger ms-2" name="backFormBtn"><i class="fa-solid fa-arrow-left"></i> Back</button>'+
		'</div>'+
		'</form>'+
		'</div>';
		if(input!='')
		{
			input=$.parseHTML(input);
			input=$(input);
			var categoryList='';
			var category=$('form[name="searchForm"] select[name="category"]').find('option').not(':first') || 0;
			//console.log($('form[name="searchForm"] select[name="category"] option:gt(0)').html());
			if(category.length>0)
			{
			categoryList = category.wrapAll('<select>').parent().html(); 
			}
			//console.log(x);
			//console.log($('<select>').append(category.clone()).html());
			/*category.each(function(i,obj){
				var t1=$(this);
				var option=t1.prop('outerHTML') || '';
				if(option.length>0)
				{
					categoryList+=option;
				}
			});*/
			$('form[name="searchForm"] select[name="category"] option').not(':first')
			categoryList+='<option value="-1">Add New Category</option>';
			input.find('select[name="categoryName"]').append(categoryList);
		}
		dialogModel.find(".modal-title").text('New Service Details');
		dialogModel.find(".modal-body .container-fluid").html(input);
		dialogModel.find(".modal-footer").addClass('d-none');
		dialogBox.show();
	});
	$("body").on("click",'form button[name="backFormBtn"]',function(e){
		var t=$(this);
		var parent=t.closest('form') || 0;
		parent.addClass('d-none');
		parent.prev('form').removeClass('d-none').find('select[name="categoryName"]').val('');
	});
	$('body').on('change','#dialogBox form select[name="categoryName"]',function(e){
		var t=$(this);
		var type=toNumber(t.val() || 0);
		if(type==-1)
		{
		var parent=t.closest('form') || 0;
		parent.addClass('d-none');
		parent.next('form').removeClass('d-none');
		}
		
	});
	$("body").on("submit",'form[name="saveCategoryForm"]',function(e){
		e.preventDefault();
		var t=$(this);
		var submitBtn=t.find('button[type="submit"]');
		$.ajax({
		url: "<?php echo site_url($prePath.'/service/category/add');?>",
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
			var id=0;
			if(data!='' && data.length>1)
			{
				var result=$.parseJSON(data);
				if($.isEmptyObject(result)==false)
				{
					status=isset(result.status) ? toNumber(result.status) : 0;
					msg=isset(result.status) ? result.msg : '';
					id=isset(result.id) ? toNumber(result.id) : 0;
				}
			}
			if(status==1 && id>0)
			{
				var categoryName=t.find('input[name="categoryName"]').val() || '';
				t.addClass('d-none');
				var option='<option value="'+id+'">'+categoryName+'</option>';
				t.prev('form').removeClass('d-none');
				var parent2=t.prev('form').find('select[name="categoryName"]') || 0;
				parent2.find("option").last().before(option);
				parent2.val(id);
				$('form[name="searchForm"] select[name="category"]').append(option);
			}
			else if($.inArray(status,[-1,-2,-3,-4])!== -1)
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
	$('body').on('click','button[name="editBtn"]',function(e){
		var t=$(this);
		var parent=t.closest('tr') || 0;
		var serviceID=toNumber(t.attr("data-value") || 0);
		var testName=parent.find('.testName').text() || '';
		var discountValue=toAmount(parent.find('.discountValue').attr("data-value") || 0);
		var amount=toAmount(parent.find('.amount').attr("data-value") || 0);
		var categoryName=toNumber(parent.find('.categoryName').attr("data-value") || 0); 
		if(serviceID>0)
		{
			var input='<div class="row">'+
			'<form action="#" name="updateServiceForm" class="col-md-12" method="POST" enctype="multipart/form-data" accept-charset="utf-8">'+
			'<input type="hidden" name="serviceID"   />'+
			'<div class="mb-3">'+
			'<label class="form-label">Test Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm" name="testName" maxlength="200" placeholder="test name " required />'+
			'</div>'+
			'<div class="mb-3">'+
			'<label class="form-label">Category Name<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<select class="form-select form-select-sm" name="categoryName">'+
			'<option selected value="">select Category Name</option>'+
			'</select>'+
			'</div>'+
			'<div class="mb-3">'+
			'<label class="form-label">Amount<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm onlyNumber" data-type="1" name="amount" maxlength="13" placeholder="Amount" required />'+
			'</div>'+
			'<div class="mb-3">'+
			'<label class="form-label">Commission Amount<i class="ms-2 fa-solid fa-asterisk requiredLable"></i></label>'+
			'<input type="text" class="form-control form-control-sm onlyNumber" data-type="1" maxlength="13" name="discountValue" maxlength="200" placeholder="Commission Amount " required />'+
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
				input.find('input[name="testName"]').val(testName);
				console.log(categoryName);
				input.find('input[name="serviceID"]').val(serviceID);
				input.find('input[name="amount"]').val(amount);
				input.find('input[name="discountValue"]').val(discountValue);
				var category=$('form[name="searchForm"] select[name="category"]').find('option').not(':first') || 0;
				if(category.length>0)
				{
				categoryList = category.wrapAll('<select>').parent().html(); 
				}
				categoryList+='<option value="-1">Add New Category</option>';
				input.find('select[name="categoryName"]').append(categoryList);
				input.find('select[name="categoryName"]').val(categoryName);
			}
			dialogModel.find(".modal-title").text('Edit Category Details');
			dialogModel.find(".modal-body .container-fluid").html(input);
			dialogModel.find(".modal-footer").addClass('d-none');
			dialogBox.show();
		}
	});
	$("body").on("submit",'form[name="saveServiceForm"]',function(e){
		e.preventDefault();
		var t=$(this);
		var submitBtn=t.find('button[type="submit"]');
		$.ajax({
		url: "<?php echo site_url($prePath.'/service/add');?>",
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
			if($.trim(data).length>1)
			{
				var result=$.parseJSON(data);
				if($.isEmptyObject(result)==false)
				{
					status=isset(result.status) ? toNumber(result.status) : 0;
					msg=isset(result.msg) ? result.msg : '';
				}
			}
			if(status==1)
			{
				window.location.reload();
			}
			else if($.inArray(status,[-1,-2,-3,-4])!== -1)
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
	$("body").on("submit",'form[name="updateServiceForm"]',function(e){
		e.preventDefault();
		var t=$(this);
		var submitBtn=t.find('button[type="submit"]');
		$.ajax({
		url: "<?php echo site_url($prePath.'/service/update');?>",
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
					msg=isset(result.msg) ? result.msg : '';
				}
			}
			if(status==1)
			{
				window.location.reload();
			}
			else if($.inArray(status,[-1,-2,-3,-4])!== -1)
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
		var serviceID=toNumber(t.attr('data-value') || 0);
		if(serviceID>0)
		{
		$.confirm({	
		title: 'Confirm!',	
		content: 'Are you sure?',
		buttons: {
		cancel: function () {},
		yes: {
		text: 'Yes', // With spaces and symbols
		action: function () {
		var formdata={'serviceID':serviceID};
		$.ajax({url: "<?php echo site_url($prePath.'/service/remove');?>",type: "POST",data:formdata,cache:false,
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