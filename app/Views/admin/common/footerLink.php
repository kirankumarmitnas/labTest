<?php  $disableDataTable=checkVariable($disableDataTable,0);?>
<!-- Bootstrap core JavaScript-->
<script src="<?php echo base_url('assets/packages/bootstrap-5.2.0-beta1/js/bootstrap.bundle.min.js');?>"></script>
<script src="<?php echo base_url('assets/packages/bootstrap-5.2.0-beta1/js/popper.min.js');?>" ></script>
<!-- Core plugin JavaScript-->
<script src="<?php echo base_url('assets/packages/jquery-easing/jquery.easing.min.js');?>"></script>
<!-- Custom scripts for all pages-->
<script src="<?php echo base_url('assets/js/moment-with-locales.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-datetimepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/packages/form-validator/jquery.form-validator.js');?>"></script>
<script src="<?php echo base_url('assets/js/jquery.numeric.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/packages/jquery-confirm/jquery-confirm.min.js'); ?>"></script>
<?php if($disableDataTable==0){ ?>
<script type="text/javascript" src="<?php echo base_url('assets/packages/datatable/datatables.min.js');?>"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url('assets/packages/select2/js/select2.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/custom.min.js?v='.time());?>"></script>
</body>
</html>
