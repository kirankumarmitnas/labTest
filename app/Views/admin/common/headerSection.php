<?php
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
$prePath=AdminConfig::get('prePath');
echo view($prePath.'common/headerLink');
echo view($prePath.'common/topBar');
echo view($prePath.'common/sideBar');
?>
<div class="clearfix"></div>
<div class="spaceBlock d-block"></div>
<div class="modal fade" id="dialogBox" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="dialogBoxTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="dialogBoxTitle"></h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<div class="container-fluid">
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
<script type="text/javascript">
let dialogBox=0;
let dialogModel=0;
$(document).ready(function(e){
	dialogBox = new bootstrap.Modal(document.getElementById('dialogBox'));
	dialogModel=$(dialogBox._element);
	$("body").on("click",'#dialogBox button[data-bs-dismiss="modal"]',function(e){
		dialogBox.hide();
	});
});
</script>