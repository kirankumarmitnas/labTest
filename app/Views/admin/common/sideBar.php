<?php
use App\Libraries\AdminConfig;
use App\Models\AccountModel;
$user=checkVariable($user,0);
$currentRoute=trim(uri_string());
$sesName=AdminConfig::get('sesName');
$sesType=AdminConfig::get('sesType');
$panelType=AdminConfig::get('panelType');
$username=(isEmptyArray($user)>0) ? checkVariable($user['username'],'','trim') : '';
if(!empty($username))
{	
$accountModel = new accountModel();
$category=$accountModel->getMenu(array('isVisible'=>1,'panelType'=>$panelType));
$myMenuList=$accountModel->getMyMenu(array('type'=>0,'sesType'=>$sesType,'sesName'=>$sesName));
}
$websiteDetails = new \Config\WebsiteDetails();
$projectName=checkVariable($websiteDetails->projectName,'','trim');
$projectIcon=checkVariable($websiteDetails->projectIcon,'','trim');
if(isset($category) && isEmptyArray($category)>0 && isset($myMenuList) && isEmptyArray($myMenuList)>0){
?>
<div class="offcanvas sidebar  offcanvas-start" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
<div class="offcanvas-header">
<h5 class="offcanvas-title theme-text" id="offcanvasExampleLabel"><i class="<?php echo $projectIcon;?> me-2"></i> <?php echo $projectName;?></h5>
<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body theme-bg">

<div class="row">
<div class="col-md-12">
<ul class="nav nav-pills flex-column mb-sm-auto mb-0 " id="menu">
<?php 
foreach($category as $key=>$cat) 
{ 
$srNo=checkVariable($cat['srNo'], 0,'intval');
$isBlank=checkVariable($cat['isBlank'], 0,'intval');
$name=checkVariable($cat['name'], '','trim');
$parentID=checkVariable($cat['parentID'], 0,'intval');
$url=checkVariable($cat['url'], '','trim');
$icon=checkVariable($cat['icon'], '','trim');
$childrens=checkVariable($cat['children'],0);
$isActive=0;
if($currentRoute==$url)
{
	$isActive=1;
}
if(in_array($srNo,$myMenuList)==true)
{ 
if(isEmptyArray($childrens)>0){ 
//echo '<pre>',print_r($children),'</pre>';
if(empty($isActive))
{
	$packageInfo=searchValueInArray(array('isSingle'=>1,'type'=>1,'data'=>$childrens,'search'=>array('url'=>$currentRoute)));
	if(isEmptyArray($packageInfo)>0)
	{
	 $isActive=1;
	}
}
?>
<li class="py-2">
<a href="<?php echo '#'.url_title(strtolower($name));?>" data-bs-toggle="collapse" class=" nav-link px-045 align-middle d-flex">
<div><i class="<?php echo $icon;?>"></i><span class="ms-2"><?php echo $name;?></span></div>
<div class="ms-auto"><i class="fas fa-caret-down"></i></div>
</a>
<ul class="collapse rounded   bg-opacity-50 nav flex-column ms-1 mt-3" id="<?php echo url_title(strtolower($name));?>" data-bs-parent="#menu">
<?php 

foreach($childrens as $key2=>$cat2) 
{ 
$srNo2=checkVariable($cat2['srNo'], 0,'intval');
$name=checkVariable($cat2['name'], 0,'trim');
$isBlank2=checkVariable($cat2['is_blank'], 0,'intval');
$parentID=checkVariable($cat2['parent_id'], 0,'intval');
$url=checkVariable($cat2['url'],'','trim');
$icon=checkVariable($cat2['icon'],'','trim');
$isActive2=0;
if($currentRoute==$url)
{
	$isActive2=1;
}
if(in_array($srNo2,$myMenuList)==true){ 
?>
<li class="<?php if($key2==2){ echo 'w-100'; } else { echo 'w-100'; }  ?>">
<a <?php  if($isBlank2==1){ ?>  target="_blank" <?php } ?> href="<?php echo site_url($url); ?>" class="nav-link ms-25"> <span class="d-none2 d-sm-inline2"><?php echo $name;?></span></a>
</li>
<?php } }  ?>
</ul>
</li>
<?php } else { ?>

<li class="nav-item py-2 ">
<a <?php  if($isBlank==1){ ?>  target="_blank" <?php } ?> href="<?php echo site_url($url); ?>" class=" nav-link align-middle px-045">
<i class="<?php echo $icon;?>"></i> <span class="ms-2 d-none2 d-sm-inline2"><?php echo $name;?></span>
</a>
</li>
<?php } } } ?>
</ul>
</div>
</div>
</div>
</div>
<?php } ?>