<?php
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use CodeIgniter\Pager\PagerRenderer;
$request=\Config\Services::request();
$prePath=AdminConfig::get('prePath'); 
helper('cookie');
echo view($prePath.'common/headerSection'); 
$statisticResult=CommonMethods::getDashboardStatistics(0);
$timing = new \Config\Timing();
$today = $timing->today;
$isDownload=0;
$backupDownload=get_cookie('backupDownload');
if(empty($backupDownload))
{
	$endTime=(60*60*(24-intval(date("H"))));
	$cookie = array('name'=> 'backupDownload','value'  => uniqid(),'expire' => $endTime);
	set_cookie($cookie);
	$isDownload=1;
}
$statisticResultStatus=isEmptyArray($statisticResult);
$dateWise=($statisticResultStatus>0) ?  checkVariable($statisticResult['testList']['dateWise'],0) : 0;
$dateWiseStatus=isEmptyArray($dateWise);
$reportInfo=($statisticResultStatus>0) ?  checkVariable($statisticResult['info'],0) : 0;
$reportInfoStatus=isEmptyArray($reportInfo);
$monthWise=($statisticResultStatus>0) ? checkVariable($statisticResult['testList']['monthWise'],0) : 0;
$monthWiseStatus=isEmptyArray($monthWise);
$patientList=($statisticResultStatus>0) ?  checkVariable($statisticResult['testList']['patients'],0) : 0;
$patientListStatus=isEmptyArray($patientList);
$servicesList=($statisticResultStatus>0) ?  checkVariable($statisticResult['testList']['services'],0) : 0; 
$servicesListStatus=isEmptyArray($servicesList);
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-self-start justify-content-start">
<div class="col-md-6 mb-2">
<p class="h4">Dashboard</p>
</div>
<div class="col-md-6 text-end mb-2">
<div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
<a type="submit" class="btn btn-success btn-sm" href="<?php echo site_url($prePath.'test/list');?>" ><i class="fas fa-list-alt me-2"></i>Form</a>
<a type="button" class="btn btn-primary" href="<?php echo site_url($prePath.'payment/list');?>"><i class="fa fa-indian-rupee-sign me-2"></i>Payment</a>
<a type="button" class="btn btn-info" href="<?php echo site_url($prePath.'report/doctor');?>"><i class="fa fa-print me-2"></i>Doctor Report</a>
</div>
</div>
</div>

<?php
if($statisticResultStatus>0 && ($patientListStatus>0 || $servicesListStatus>0) || ($monthWiseStatus>0 || $dateWiseStatus>0 ) || $reportInfoStatus>0 )
{
?>
<div class="row p-3 align-self-start justify-content-start">
<div class="col-md-12">
<div class="card">
<div class="card-body">
<div class="row">
<?php
if($monthWiseStatus>0 || $dateWiseStatus>0 )
{
?>
<div class="col-md-12 text-center mb-4">
<div class="btn-group  btn-group-sm" role="group" aria-label="Basic radio toggle button group">
  <input type="radio" class="btn-check chartToggle" value="1" name="chartToggle" id="btnradio1" autocomplete="off" checked />
  <label class="btn btn-outline-primary" for="btnradio1">Daily</label>
  <input type="radio" class="btn-check chartToggle" value="2" name="chartToggle" id="btnradio2" autocomplete="off">
  <label class="btn btn-outline-primary" for="btnradio2">Monthly</label>
</div>
</div>
<div class="col-md-12 mb-5 d-none">
<div id="chartContainer">
  <canvas id="dayChart"></canvas>
</div>
</div>
<div class="col-md-12 mb-5 d-none">
<div id="chartContainer">
  <canvas id="monthChart"></canvas>
</div>
</div>
<?php } ?>
<?php
$userPermissions=0;
$userPermissionsStatus=0;
$totalDoctors=0;
$totalService=0;
$totalCategory=0;
$totalTest=0;
$totalAmount=0;
$totalCommission=0;
$totalEarning=0;
$totalPatient=0;
if($reportInfoStatus>0)
{
$userPermissions=checkVariable($statisticResult['userPermissions'],0);	
$userPermissionsStatus=isEmptyArray($userPermissions);
$totalDoctors=checkVariable($reportInfo['totalDoctors'],0,'intval');
$totalService=checkVariable($reportInfo['totalService'],0,'intval');
$totalCategory=checkVariable($reportInfo['totalCategory'],0,'intval');
$totalPatient=checkVariable($reportInfo['totalPatient'],0,'intval');
$totalTest=checkVariable($reportInfo['totalTest'],0,'intval');
$totalAmount=checkVariable($reportInfo['totalAmount'],0,'doubleval');
$totalCommission=checkVariable($reportInfo['totalCommission'],0,'doubleval');
}
$totalEarning=$totalAmount-$totalCommission;
$statisticsList=array(
array('title'=>'Total Doctors','value'=>$totalDoctors,'icon'=>'fa-solid fa-user-doctor mr-2 fa-2x','background'=>'bg-gradient bg-primary','menuID'=>2),
array('title'=>'Total Patient','value'=>$totalPatient,'icon'=>'fa-solid fa-user-doctor mr-2 fa-2x','background'=>'bg-gradient bg-dark ','menuID'=>2),
array('title'=>'Total Services','value'=>$totalService,'icon'=>'fa-solid fa-microscope mr-2 fa-2x','background'=>'bg-gradient bg-success','menuID'=>2),
array('title'=>'Total Category','value'=>$totalCategory,'icon'=>'fa-solid fa-boxes-stacked mr-2 fa-2x','background'=>'bg-gradient bg-danger','menuID'=>2),
array('title'=>'Total Test','value'=>$totalTest,'icon'=>'fa-solid fa-vial-circle-check mr-2 fa-2x','background'=>'bg-gradient bg-info','menuID'=>2),
array('title'=>'Total Amount','value'=>convertIntoIndianRupesh($totalAmount),'icon'=>'fa-solid fa-indian-rupee-sign mr-2 fa-2x','background'=>'bg-gradient bg-primary','menuID'=>2),
array('title'=>'Total Commissions','value'=>convertIntoIndianRupesh($totalCommission),'icon'=>'fa-solid fa-indian-rupee-sign mr-2 fa-2x','background'=>'bg-gradient bg-danger','menuID'=>2),
array('title'=>'Total Earning','value'=>convertIntoIndianRupesh($totalEarning),'icon'=>'fa-solid fa-indian-rupee-sign mr-2 fa-2x','background'=>'bg-gradient bg-success','menuID'=>2),
);
foreach($statisticsList as $list){
	$extra=checkVariable($list['extra'],'');
	$extraValue=checkVariable($list['extraValue'],0,'intval');
	$menuID=checkVariable($list['menuID'],0,'intval');
	if($menuID>0 )//$userPermissionsStatus>0 && && in_array($menuID,$userPermissions)==true 
	{
?>

<div class="col-md-3 mb-3">
<div class="card m-2 <?php echo checkVariable($list['background'],'');?>  shadow-sm">
<div class="card-body">
<div class="d-flex">
<div class="me-auto  p-2 bd-highlight">
<p class=" text-uppercase text-white fs-12 fw-bold"><?php echo checkVariable($list['title'],'');?></p>
<p class="mb-0  text-white"><?php echo checkVariable($list['value'],'');if(!empty($extra)){ echo '('.$extra.')'; } if(!empty($extraValue)){ echo ' / <span class="text-muted">'.$extraValue.'</span>'; } ?></p>
</div>
<div class="p-2 bd-highlight"><i class="text-white <?php echo checkVariable($list['icon'],'');?>"></i></div>
</div>
</div>
</div>
</div>	
<?php } } ?>
<?php
//if($statisticResultStatus>0 )//&& $userPermissionsStatus>0 && $menuID>0 && in_array(2,$userPermissions)==true
//{	
if($patientListStatus>0)
{	
?>
<div class="col-md-12">
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th colspan="5">
<p class="text-start mb-0 h5">Current List</p>
</th>
</tr>
<tr>
<th style="width:5%;">#</th>
<th style="width:10%;">Date</th>
<th style="width:20%;">Patient Name</th>
<th style="width:45%;">Services Name</th>
<th style="width:20%;">Reffered By</th>
</tr>
</thead>
<tbody>
<?php
foreach($patientList as $key=>$row)
{
	//labDate,patientName,doctorName,services
	$patientName=checkVariable($row['patientName'],0,'trim');
	$doctorName=checkVariable($row['doctorName'],0,'trim');
	$services=checkVariable($row['services'],0);
	$serviceName='';
	if(isEmptyArray($services)>0)
	{
		$services=getArrayKeyValues(array('data'=>$services,'fields'=>array('testName')));
	}
	$labDate=checkVariable($row['labDate'],0);
	$labDate=(!empty($labDate)) ? strtotime($labDate) : 0;
	?>
	<tr>
	<td><?php echo ($key+1);?></td>
	<td><?php echo (!empty($labDate)) ? date("d/m/Y",$labDate) : '';?></td>
	<td><?php echo $patientName;?></td>
	<td>
	<?php
	if(isEmptyArray($services)>0)
		{
			foreach($services as $service)
			{
			echo '<span class="badge bg-success me-2">',print_r($service),'</span>';
			}
		}
		?>
	</td>
	<td><?php echo $doctorName;?></td>
	</tr>
	<?php
}
?>

</tbody>
</table>
</div>
</div>
<?php }   ?>
<?php
if($servicesListStatus>0)
{
?>
<div class="col-md-12">
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th colspan="5">
<p class="text-start mb-0 h5">Account List</p>
</th>
</tr>
<tr>
<th style="width:5%;">#</th>
<th style="width:10%;">Date</th>
<th style="width:40%;">Services Name</th>
<th style="width:30%;">Reffered By</th>
<th style="width:15%;">Amount</th>
</tr>
</thead>
<tbody>
<?php
foreach($servicesList as $key=>$row)
{
	//td.testName,td.amount,td.discountValue,tl.labDate,dl.doctorName
	$testName=checkVariable($row['testName'],0,'trim');
	$doctorName=checkVariable($row['doctorName'],0,'trim');
	$amount=checkVariable($row['amount'],0,'doubleval');
	$labDate=checkVariable($row['labDate'],0);
	$labDate=(!empty($labDate)) ? strtotime($labDate) : 0;
	?>
	<tr>
	<td><?php echo ($key+1);?></td>
	<td><?php echo (!empty($labDate)) ? date("d/m/Y",$labDate) : '';?></td>
	<td><?php echo $testName;?></td>
	<td><?php echo $doctorName;?></td>
	<td><i class="fa-solid fa-indian-rupee-sign me-1"></i><?php echo convertIntoIndianRupesh($amount);?></td>
	</tr>
	<?php
}
?>

</tbody>
</table>
</div>
</div>
<?php }   ?>



</div>
</div>
</div>
</div>
</div>
<?php } else { ?>
<div class="card">
  <div class="card-body">
    <blockquote class="blockquote mb-0">
	  <h5 class="card-title text-center">No data found.</h5>
    </blockquote>
  </div>
</div>
<?php } ?>
</div>
</section>
<script src="<?php echo  base_url('assets/packages/chart.js/dist/chart.js')?>"></script>
<script type="text/javascript">
let dateWise='';
let monthWise='';
<?php if($dateWiseStatus>0) { ?>dateWise='<?php echo json_encode($dateWise);?>'; <?php  } ?>
<?php if($monthWiseStatus>0) { ?>monthWise='<?php echo json_encode($monthWise);?>'; <?php  } ?>
if($.trim(dateWise).length>0)
{
	dateWise=$.parseJSON(dateWise);
}
if($.trim(monthWise).length>0)
{
	monthWise=$.parseJSON(monthWise);
}
$(document).ready(function(e){
	let dayChart=0;
	let monthChart=0;
	const cMonth=moment().format("MMMM"); 
	const cYear=moment().format("YYYY"); 
	let config=0;
	let config2=0;
	var chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	black: 'rgb(34 38 42)'
	};
    var totalColor=Object.keys(chartColors).length || 0;
	--totalColor;
	var randomScalingFactor = function() {
	return (Math.random() > 0.5 ? 1.0 : 1.0) * Math.round(Math.random() * 100);
	};
	if($.isEmptyObject(dateWise)==false)
	{
	var dayColumns=[];
	var dayRows=[];
	var colorList=[];
	var colorIndex=0;
	//var result = Object.keys(chartColors).map((key) => [chartColors[key]]); //key, chartColors[key]
	
	/*Object.keys( myObj ).forEach(function ( name, index ) {
    var value = myObj[name];
    console.log(++i);
    console.log(name); // the property name
    console.log(value); // the value of that property
    console.log(index); // the counter
	});*/
	var result=Object.values(chartColors);
	//var entries = Object.entries(person);
	$.each(dateWise,function(i,obj){
	var found=isset(obj.found) ? toNumber(obj.found) : 0;
	var day=isset(obj.day) ? obj.day : '';
	if(found>0 && $.trim(day).length>0)
	{
	dayColumns.push(day);
	dayRows.push(found);
	colorList.push(result[colorIndex]);
	if(colorIndex==totalColor)
	{
	colorIndex=0;	
	}
	else
	{
	colorIndex++;
	}
	}
	});
	let delayed;
	var data =  {
	labels: dayColumns,
	datasets: [{
	label: 'Service',
	backgroundColor: colorList,
	data: dayRows
	}]
	};
	config = {
	
	 type: 'line',
	data: data,
	options: {
	animation: {
      onComplete: () => {
        delayed = true;
      },
      delay: (context) => {
        let delay = 0;
        if (context.type === 'data' && context.mode === 'default' && !delayed) {
          delay = context.dataIndex * 300 + context.datasetIndex * 100;
        }
        return delay;
      },
    },
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true
      }
    },	
	responsive: true,
	maintainAspectRatio: false,
	elements: {
	bar: {
	borderWidth: 1,
	}
	},	
	scales: {
	y: {
	beginAtZero: true
	}
	},
	plugins: {
	legend: {
	position: 'top',
	},
	title: {
	display: true,
	text: 'Daily Report - '+cMonth+','+cYear
	},
	tooltips: {
	mode: 'index',
	intersect: false
	},
	}
	},
	};
	
	$("#dayChart").parents().eq(1).removeClass('d-none');
	dayChart = new Chart(document.getElementById("dayChart"), config);
	}
	if($.isEmptyObject(monthWise)==false)
	{
	var monthColumns=[];
	var monthRows=[];
	var colorList=[];
	var colorIndex=0;
	var result = Object.keys(chartColors).map((key) => [chartColors[key]]);
	$.each(monthWise,function(i,obj){
	var found=isset(obj.found) ? toNumber(obj.found) : 0;
	var month=isset(obj.month) ? toNumber(obj.month) : 0;
	if(found>0 && month>0)
	{
	month=moment().month(month).format("MMMM"); 	
	monthColumns.push(month);
	monthRows.push(found);
	colorList.push(result[colorIndex]);
	if(colorIndex==totalColor)
	{
	colorIndex=0;	
	}
	else
	{
	colorIndex++;
	}
	}
	});
	var data2 =  {
	labels: monthColumns,
	datasets: [{
	label: 'Service',
	backgroundColor: colorList,
	data: monthRows
	}]
	};
	config2 = {
	type: 'bar',
	data: data2,
	options: {
		
	responsive: true,
	maintainAspectRatio: false,
	elements: {
	bar: {
	borderWidth: 1,
	}
	},	
	scales: {
	y: {
	beginAtZero: true
	}
	},
	plugins: {
	legend: {
	position: 'top',
	},
	title: {
	display: true,
	text: 'Monthly Report -'+cYear,
	position: 'top',
	},
	tooltips: {
	mode: 'index',
	intersect: false
	},
	}
	},
	};
    $("#monthChart").parents().eq(1).removeClass('d-none');
	monthChart = new Chart(document.getElementById("monthChart"), config2);
	$("#monthChart").parents().eq(1).addClass('d-none');
	}
	$("body").on("change",'input.chartToggle',function(e){
		var t=$(this);
		var value=t.val() || 0;
		if(value==2)
		{
		$("#monthChart").parents().eq(1).removeClass('d-none');
		$("#dayChart").parents().eq(1).addClass('d-none');
		monthChart.destroy();
		monthChart = new Chart(document.getElementById("monthChart"), config2);
		}
		else
		{
		$("#monthChart").parents().eq(1).addClass('d-none');
		$("#dayChart").parents().eq(1).removeClass('d-none');	
		dayChart.destroy();
		dayChart = new Chart(document.getElementById("dayChart"), config);
		}
	});
   <?php if($isDownload==1) { ?>
   $('<iframe>', {
   src: '<?php echo site_url($prePath."db/backup/download");?>',
   id:  'myFrame',
   frameborder: 0,
   class : 'hide',
   scrolling: 'no'
   }).appendTo('body.authenticate');
   <?php } ?>
});
</script>
<?php echo view($prePath.'common/footerSection'); ?>