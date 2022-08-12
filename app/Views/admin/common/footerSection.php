<?php 
use App\Libraries\AdminConfig;
use CodeIgniter\Pager\PagerRenderer;
$prePath=AdminConfig::get('prePath'); 
echo view($prePath.'common/footer');
echo view($prePath.'common/footerLink');
?>
