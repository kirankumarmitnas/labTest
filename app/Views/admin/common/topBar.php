<?php 
use App\Libraries\AdminConfig;
$user=checkVariable($user,0); 
$prePath=AdminConfig::get('prePath');
$username=(isEmptyArray($user)>0) ? checkVariable($user['username'],'','trim') : '';
$websiteDetails = new \Config\WebsiteDetails();
$projectName=checkVariable($websiteDetails->projectName,'','trim');
$projectIcon=checkVariable($websiteDetails->projectIcon,'','trim');
?>
<nav class="navbar navbar-expand-md fixed-top bg-white">
<div class="container-fluid">
<span class="navbar-text me-3">
<button class="btn theme-text" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
<i class="fas fa-bars"></i>
</button>
</span>
<a class="navbar-brand theme-text" href="<?php echo site_url('admin/dashboard');?>"> <i class="<i class="<?php echo $projectIcon;?> me-2"></i> <?php echo $projectName;?> </a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
<i class="fas fa-cog animate__rotateIn animate__slower animate__animated animate__infinite"></i>
</button>

<div class="collapse navbar-collapse" id="navbarCollapse">
<ul class="navbar-nav ms-auto mb-2 mb-md-0">
<li class="nav-item dropdown">
<a class="nav-link theme-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-regular fa-circle-user animate__bounceIn animate__animated animate__infinite  animate__slower"></i> Hi , <?php echo $username;?>
</a>
<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
<li><a class="dropdown-item" href="#"><i class="fa-regular fa-user me-1"></i>Profile</a></li> 
<li><hr class="dropdown-divider"></li>
<li><a class="dropdown-item" href="<?php echo site_url($prePath.'logout');?>"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Log Out</a></li>
</ul>
</li>
</ul>
</div>
</div>
</nav>