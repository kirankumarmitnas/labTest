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
$monthWise=($statisticResultStatus>0) ? checkVariable($statisticResult['testList']['monthWise'],0) : 0;
$monthWiseStatus=isEmptyArray($monthWise);
?>
<section class="content">
<div class="container-fluid">
<div class="row p-3 align-self-start justify-content-start">
<div class="col-md-12 mb-2">
<p class="h4">Dashboard</p>
</div>
</div>

<div class="row p-3 align-self-start justify-content-start">
<div class="col-md-12">
<div class="card">
<div class="card-body">
<div class="row">
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
<?php
if($statisticResultStatus>0 )//&& $userPermissionsStatus>0 && $menuID>0 && in_array(2,$userPermissions)==true
{
	$patientList=checkVariable($statisticResult['testList']['patients'],0);
	if(isEmptyArray($patientList)>0)
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
<th>#</th>
<th>Date</th>
<th >Patient Name</th>
<th>Services Name</th>
<th >Reffered By</th>
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
<?php }  } ?>
<?php
if($statisticResultStatus>0 )//&& $userPermissionsStatus>0 && $menuID>0 && in_array(2,$userPermissions)==true
{
	$patientList=checkVariable($statisticResult['testList']['services'],0);
	if(isEmptyArray($patientList)>0)
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
<th>#</th>
<th>Date</th>
<th >Services Name</th>
<th >Reffered By</th>
<th>Amount</th>
</tr>
</thead>
<tbody>
<?php
foreach($patientList as $key=>$row)
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
	<td><i class="fa-solid fa-indian-rupee-sign"></i><?php echo $amount;?></td>
	</tr>
	<?php
}
?>

</tbody>
</table>
</div>
</div>
<?php }  } ?>



</div>
</div>
</div>
</div>
</div>

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
	let config=0;
	let config2=0;
	var chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(231,233,237)'
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
	text: 'Daily Report'
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
	text: 'Monthly Report',
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