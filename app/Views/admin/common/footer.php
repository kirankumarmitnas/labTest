<?php
$websiteDetails = new \Config\WebsiteDetails();
$projectName=checkVariable($websiteDetails->projectName,'','trim');
?>
<div class="clearfix"></div>
<footer class="container-fluid footer ">
<div class="row p-3 theme-bg align-self-start justify-content-start">
<div class="col-md-6 col-5 text-start text-white"><a href="#" class="text-white text-decoration-none"><?php echo $projectName;?><span class="fw-bold"><?php echo date("Y");?></span></a></div>
<div class="col-md-6 col-7 text-end text-white">Powered by&nbsp;<a href="http://nectron.in/" class="text-white text-decoration-none fw-bold" target="_blank">Nectron Technology</a></div>
</div>
</footer>