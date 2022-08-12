<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
$request=\Config\Services::request();
$prePath=AdminConfig::get('prePath'); 
echo view($prePath.'common/headerLink');
$session = \Config\Services::session(); 
$errors=$session->getFlashdata('errors');
$status=0;
$msg=0;
if(isEmptyArray($errors)>0)
{
	$status=checkVariable($errors['status'],0,'intval');
	$msg=checkVariable($errors['msg'],0);
}
$websiteDetails = new \Config\WebsiteDetails();
$projectName=checkVariable($websiteDetails->projectName,'','trim');
?>
<section class="container">
<div class="row align-items-center align-self-center justify-content-center">
<div class="col-md-4 mt-3 mt-md-5">
<?php echo form_open_multipart(site_url('admin/login'),array('name'=>'loginForm','class'=>'form','method'=>'POST'));?>
<div class="card shadow invisible rounded mt-md-5 rounded-top bg-white text-white border-5 theme-border">
  <div class="card-body">
    <p class="h2 text-center theme-text"><i class="fas2 fa-question2 far fa-user-circle fa-2x rounded-circle  px-3 py-2"></i></p>
    <h5 class="text-center theme-text card-title">Welcome to <?php echo $projectName;?></h5>
  </div>
  <?php if($status!=0 && $status!=-11 && isEmptyArray($msg)<=0){ ?>
  <div class="card-body pt-2 pb-0">
  <p class="fst-normal text-danger mb-0"><i class="fas fa-exclamation-circle me-2"></i><?php echo $msg;?></p>
   </div>
  <?php } ?>
  <div class="card-body">
	<div class="mb-1">
	<label  class="form-label text-dark"><i class="fas fa-user me-1"></i> Username</label>
	<input type="text" class="form-control <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('username',$msg)==true){ echo 'is-invalid'; } ?>" value="<?php echo old('username') ?>" required  name="username" maxlength="64" placeholder="Enter Username" />
	<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('username',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['username'],'','trim');?></div><?php } ?>
	</div>
	<div class="mb-3">
	<label  class="form-label text-dark"><i class="fas fa-lock me-1"></i>Password</label>
	<input type="password" class="form-control <?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('password',$msg)==true){ echo 'is-invalid'; } ?>"  value="<?php echo  old('password') ?>" required name="password"  maxlength="30" placeholder="Enter Password" />
	<?php if($status==-11 && isEmptyArray($msg)>0 && array_key_exists('password',$msg)==true){ ?> <div class="invalid-feedback"> <?php echo checkVariable($msg['password'],'','trim');?></div><?php } ?>
	</div>
  </div>
  <div class="card-body ">
	<div class="text-center">
	<button class="btn btn-light text-white theme-bg" type="submit"> Login <i class="fas fa-arrow-right ms-1"></i></button>
	</div>
  </div>
</div>
<?php echo form_close();?>
</div>
</div>
</section>
<script type="text/javascript">
$(document).ready(function(e){
	$("form .card").removeClass("invisible");
	//$("form .card").addClass("animate__animated animate__backInUp animate__delay-0s animate__repeat-1");
	//$("form .card .card-body").addClass("invisible");
	$('body').on('animationend webkitAnimationEnd oAnimationEnd mozAnimationEnd', 'form .card', function () {
	   //$("form .card .card-body").removeClass("invisible");
	   //$("form .card .card-body").find('input[name="username"]').parent().addClass("animate__animated animate__delay-1s animate__backInDown");
	   // $("form .card .card-body").find('input[name="password"]').parent().addClass("animate__animated animate__delay-1s animate__backInUp");
		//$("form .card .card-body").find('button[type="submit"]').parents().eq(1).addClass("animate__animated animate__delay-1s animate__fadeInUp");
		//$("form .card .card-body").find('i.fa-user-circle').parent().addClass("animate__animated animate__delay-2s animate__fadeIn");
		//$("form .card .card-body").find('.card-title').parent().addClass("animate__animated animate__delay-1s animate__fadeInDown");
	});
	$('body').on('animationend webkitAnimationEnd oAnimationEnd mozAnimationEnd', 'form .card .card-body', function () {
		
	});
});
</script>
<?php
echo view($prePath.'common/footerLink');
?>