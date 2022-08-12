<?php
namespace App\Libraries;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
use CodeIgniter\Database\Query;
use Mpdf\Mpdf;
use App\Models\AccountModel;
use App\Models\Admin\ReportModel;
use App\Models\Admin\DoctorModel;
use CodeIgniter\HTTP\Response;
use Config\Database;
use App\Libraries\CommonMethods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 	
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Admin\FacultyModel;
set_time_limit(0);
ini_set('max_execution_time', 0);
class  ExportFile
{
	public static function testReportPdf($options=false)
	{
		$db=Database::connect();
		$request=Services::request();
		$reportModel=new ReportModel();
		$prePath=AdminConfig::get('prePath');
		$get=$request->getGetPost();
		$doctor=checkVariable($get['doctor'],0,'intval');
		$year=checkVariable($get['year'],0,'intval');
		$month=checkVariable($get['month'],0,'intval');
		$search=checkVariable($get['search'],'','trim');
		$processType=checkVariable($get['processType'],0,'intval');
        $parameters=array('type'=>0);
		$userSelectedFields=checkVariable($get['fieldNames'],0);
		$websiteDetails = new \Config\WebsiteDetails();
		$projectName=checkVariable($websiteDetails->projectName,'','trim');
		/*if(!isset($userSelectedFields) || isEmptyArray($userSelectedFields)<=0)
		{
			$userSelectedFields=array(0,1,2,3,4,5);
		}*/
		$fileTitle=' Doctorwise Report ';
		if(in_array($processType,array(2,3))==true)
		{
			if($year>0)
			{
				$parameters['year']=$year;
			}
			if($month>0)
			{
				$parameters['month']=$month;
			}
			$parameters['processType']=$processType;
			$parameters['search']=$search;
			if($processType==2)
			{
				$fileTitle=' Monthly Report ';
				$fieldList=array(
				array('name'=>'Sr No','field'=>'key','width'=>10,'position'=>1),
				array('name'=>'Doctor Name','field'=>'doctorName','width'=>40,'position'=>2),
				array('name'=>'Total Service','field'=>'totalServices','width'=>20,'position'=>3),
				array('name'=>'Amount','field'=>'amount','width'=>20,'position'=>4),
				array('name'=>'Commission','field'=>'commission','width'=>30,'position'=>5),
				array('name'=>'Earning','field'=>'earning','width'=>30,'position'=>6),
				);
				$userSelectedFields=array(0,1,2,3,4,5);
			}
			else
			{
				$fileTitle=' Servicewise Report ';
				$fieldList=array(
				array('name'=>'Sr No','field'=>'key','width'=>10,'position'=>1),
				array('name'=>'Service Name','field'=>'testName','width'=>40,'position'=>2),
				array('name'=>'Total Count	','field'=>'totalServices','width'=>20,'position'=>3),
				array('name'=>'Amount','field'=>'amount','width'=>20,'position'=>4),
				array('name'=>'Total Commission','field'=>'commission','width'=>20,'position'=>5),
				array('name'=>'Total Earning','field'=>'earning','width'=>30,'position'=>6),
				);
				$userSelectedFields=array(0,1,2,3,4,5);
			}
		}
		else
		{
			if($doctor>0)
			{
				$parameters['doctorID']=$doctor;
			}
			if($year>0)
			{
				$parameters['year']=$doctor;
			}
			if($month>0)
			{
				$parameters['month']=$month;
			}
			$parameters['search']=$search;
			$fieldList=array(
			array('name'=>'Sr No','field'=>'key','width'=>10,'position'=>1),
			array('name'=>'Date','field'=>'labDate','width'=>20,'position'=>2),
			array('name'=>'Patient Name','field'=>'patientName','width'=>40,'position'=>3),
			array('name'=>'Service Name','field'=>'testName','width'=>30,'position'=>4),
			array('name'=>'Commission','field'=>'commission','width'=>30,'position'=>5),
			);
			$userSelectedFields=array(0,1,2,3,4);
		}
		$reportModel = new ReportModel();
		$totalRecords=$reportModel->getList($parameters);
		if($totalRecords>0)
		{
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$totalRecords;
		$parameters['type']=1;
		$result=$reportModel->getList($parameters);		
		$maximum=0;
		if(isEmptyArray($result)>0)
		{
		$doctorList=0;
		$doctorModel = new DoctorModel();
		$para=array('type'=>0);
		$totalDoctor=$doctorModel->getList($para);
		if($totalDoctor>0)
		{
		$para=array('fetchField'=>" srNo,doctorName,designation",'action'=>1,'orderBy'=>' order by doctorName ASC ','isMultiple'=>1,'limit'=>$totalDoctor,'type'=>1);
		$doctorList=$doctorModel->getList($para);
		}
		$doctorListStatus=isEmptyArray($doctorList);
		$showFields=array();
		$totalFields=count($fieldList);
		if(isEmptyArray($userSelectedFields)>0)
		{
		$totalFields=count($fieldList);
		$userSelectedFields=array_unique($userSelectedFields);
		sort($userSelectedFields);
		foreach($userSelectedFields as $key=>$val)
		{
		if($val<$totalFields)
		{
		$showFields[]=$val;
		}
		}
		}
		else
		{
		if($totalFields>0)
		{
		$showFields=range(0,($totalFields-1));
		}
		}
		$idList=getColumnName(array('totalFields'=>$totalFields));
		if(isEmptyArray($showFields)>0)
		{
		foreach($showFields as $key=>$val)
		{
		$fieldInfos=array();
		if(isset($fieldList[$val]) && isEmptyArray($fieldList[$val])>0)
		{
		$fieldInfos=$fieldList[$val];	
		if(isset($idList[$key]) && !empty($idList[$key]))
		{
		$fieldInfos['id']=$idList[$key];
		}
		}
		$showFields[$key]=$fieldInfos;
		}
		}
		if(isEmptyArray($showFields)>0)
		{
		$data='
		<html>
		<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
		<style><style>
		*
		{
		-webkit-print-color-adjust: exact;
		color-adjust: exact;
		}
		.fa-smile {position: relative;}
		.fa-smile:before {content: "\f111";color: #f1c40f;}
		.fa-smile:after {left: 0;position: absolute;content: "\f118";}
		.myheader{ margin-bottom:3px; }
		.header{margin-bottom:2px;}
		/*.text-underline {text-decoration: underline;}*/
		p {	margin: 0pt;}
		table.items {border: 0.1mm solid #000000;}
		td { vertical-align: top; }
		.items td {border-left: 0.1mm solid #000000;border-right: 0.1mm solid #000000;}
		table thead tr th { text-align: center;border: 0.1mm solid #000000;font-variant: small-caps;}
		.items td.blanktotal {

		border: 0.1mm solid #000000;/*border: 1mm none #000000;border-top: 0.1mm solid #000000;border-right: 0.1mm solid #000000;*/
		}
		.items td.totals {text-align: right;border: 0.1mm solid #000000;}
		.items td.cost {text-align: "." center;}
		body {font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;}
		.hidden-item{display:none;}
		th{font-size:12px;padding: 2mm 0 !important,margin: 0 !important;border-left:none;border-right:none;}
		.myborder td,.myborder th {border-bottom: 1px solid #000;border-top: 1px solid #000;vertical-align: middle;padding:5px;}
		.hide{display:none !important; }
		.border-top{border-top: 1px solid #000 !important;}
		.border-left{border-left: 1px solid #000 !important;}
		.border-right{border-right: 1px solid #000 !important;}
		.border-bottom{border-bottom: 1px solid #000 !important;}
		.no-border-top{border-top: none !important;}
		.no-border-left{border-left: none !important;}
		.no-border-right{border-right: none !important;}
		.no-border-bottom{border-bottom: none !important;}
		.noborder {border: none !important;}
		tbody tr td,p {font-size: 12px;}
		tbody tr td {margin: 0 !important; padding: 1mm 5mm !important;}
		table thead tr th{vertical-align: middle;}
		/*.header td, .footer td{border:none;}*/
		.text-right {text-align: right;}
		.text-center {text-align: center;}
		.text-left{text-align: left;}
		table {padding-top:2mm;width:100%;clear:both;border-collapse: collapse;}
		.right{float:right;}
		table td,table th,h5,h4 {font-family:helvetica;font-size: 10px !important;margin: 0 !important;clear:both;}
		h3 {font-size: 12px !important; margin: 0 !important;}
		.barcodecell { text-align: left;vertical-align: middle;padding: 0;}
		p{margin:0;}
		.text-font{font-size:9pt;}
		table { border-collapse: collapse;}
		.signature-font{font-size:12pt;}
		.certificate-font{font-size:17pt;font-weight:500;}
		.certificate-font2{ font-size:15pt;font-weight:500;}
		.certificate-font3{ font-size:17pt;font-weight:bold;}
		.amount-font { font-size:12pt; }
		certificate-font4{ font-size:15pt;font-weight:bold;}
		.certificate-font5{ font-size:11pt;font-weight:bold;}
		.font-serif{ font-family: serif; }
		table tbody tr td { padding:8px;}
		.margin-auto { margin:0 auto; }
		.orange-font{color:#dd9b09;}
		.green-font{color:#1b8e0b;}
		.pink-font{color:#ef3075;}
		.blue-font{color:#6b63ae;}
		.light-blue-font{color:#53c5d0;}
		.red-font{color:#df382c;}
		.border-top { border-top:0.5mm solid #df9b09;}
		.border-bottom { border-bottom:0.5mm solid #df9b09;}
		</style>
		</head>
		<body>';
		$data.='<!--mpdf
		<htmlpageheader name="myheader" class="myheader" style="display:none">
		<table border="0"><tbody><tr><td style="width:100%;" class="text-center"><h2>'.$projectName.'</h2></td></tr><tr><td style="width:100%;" class="text-center"><p style="font-size:13pt;">'.$fileTitle.'</p></td></tr><tr><td style="width:100%;" class="text-right"><p>Date: <b>'.date("d-m-Y h:i A").'</b></p></td></tr></tbody></table>
		</htmlpageheader>
		<htmlpagefooter name="myfooter" >
		<div style=" font-size: 9pt; text-align: center; padding-top: 3mm; ">
		Page {PAGENO} of {nb}
		</div>
		</htmlpagefooter>
		<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
		<sethtmlpagefooter name="myfooter" value="on" />
		mpdf-->';
		$data.='<table border="1">	
		<thead><tr>';	
		foreach($showFields as $obj)
		{
		$name=isset($obj['name']) ? trim($obj['name']) : '';	
		if(!empty($name))
		{
		$data.='<th  style="text-transform: capitalize ! important;">'.$name.'</th>';	
		}
		}
		$i=0;
		$data.='<tr></thead>';	
		$data.='<tbody>';
		$totalEarning=0;
		$totalAmount=0;
		$totalCommission=0;	
		foreach($result as $key=>$row)
		{
		$data.='<tr>';	
		$i++;
		$key=$key+1;
		if($processType==1)
		{
		$testName=checkVariable($row['testName'],0,'trim');
		$patientName=checkVariable($row['patientName'],0,'trim');
		$amount=checkVariable($row['amount'],0,'doubleval');
		$commission=checkVariable($row['discountValue'],0,'doubleval');
		//$doctorID=checkVariable($test['doctorID'],0,'intval');
		$labDate=checkVariable($row['labDate'],0);
		$labDate=(!empty($labDate)) ? date("d-m-Y",strtotime($labDate)) : '';
		$doctorName='';
		$totalCommission+=$commission;
		}
		elseif($processType==2)
		{
			$totalServices=checkVariable($row['totalServices'],0,'intval');
			$amount=checkVariable($row['totalAmount'],0,'doubleval');
			$commission=checkVariable($row['totalDiscount'],0,'doubleval');
			$doctorID=checkVariable($row['doctorID'],0,'intval');
			$earning=($amount>0) ? ($amount-$commission) : 0;
			$doctorName='';
			$totalEarning+=$earning;
			$totalAmount+=$amount;
			$totalCommission+=$commission;
			if($doctorListStatus>0)
			{
				$info=searchValueInArray(array('data'=>$doctorList,'search'=>array('srNo'=>$doctorID),'type'=>1,'isSingle'=>1));
				if(isEmptyArray($info)>0){ 
				$doctorName=checkVariable($info['doctorName'],'','trim');
				}
			}
		}
		elseif($processType==3)
		{
			$totalServices=checkVariable($row['totalServices'],0,'intval');
			$amount=checkVariable($row['totalAmount'],0,'doubleval');
			$commission=checkVariable($row['totalDiscount'],0,'doubleval');
			$testName=checkVariable($row['testName'],0,'trim');
			$earning=($amount>0) ? ($amount-$commission) : 0;
			$totalEarning+=$earning;
			$totalAmount+=$amount;
			$totalCommission+=$commission;
			
		}
		foreach($showFields as $obj)
		{
		$id=isset($obj['id']) ? trim($obj['id']) : '';
		$fieldName=isset($obj['field']) ? trim($obj['field']) : '';

		if(isset($$fieldName))
		{
		$data.='<td>'.$$fieldName.'</td>';
		}
		}
		$data.='</tr>';
		}
		if($processType==1)
		{
		$data.='<tr><td class="text-right" colspan="'.(isEmptyArray($showFields)-2).'"><p> Total Commission:</p></td><td colspan="2"><p><b>&#8377; '.convertIntoIndianRupesh($totalCommission).'</b></p></td></tr>';
		}
		elseif(in_array($processType,array(2,3))==true)
		{
			$data.='<tr><td class="text-right" colspan="'.(isEmptyArray($showFields)-2).'"><p> Total Earning:</p></td><td colspan="2"><p><b>&#8377; '.convertIntoIndianRupesh($totalEarning).'</b></p></td></tr>';
		}
		
		$data.='</tbody>
		</table>
		</body>
		</html>
		';
		}
		if(!empty($data))
		{
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [190, 236]]);//
		$mpdf->SetWatermarkText('List', 0.1);
		$mpdf->AddPage('P',// L - landscape, P - portrait 
		'', '', '', '',
		10, // margin_left
		10, // margin right
		33, // margin top
		10, // margin bottom
		5, // margin header
		5 ); // margin footer
		$fileName=$projectName.' '.$fileTitle;
		$mpdf->SetTitle($fileName);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML(utf8_encode($data));
		//if(ob_get_length() > 0) {  ob_clean();}
		if (ob_get_contents()) ob_end_clean();
		$fileName=$fileName.'-'.date("Y-m-d h:i A");
		$responseObj = service('response');
		$responseObj->setHeader('Content-Type', 'application/pdf');
		$mpdf->Output($fileName,'i');			
		}
		}
		}
	}
	public static function testReportExcel($options=false)
	{
		$db=Database::connect();
		$request=Services::request();
		$reportModel=new ReportModel();
		$prePath=AdminConfig::get('prePath');
		$get=$request->getGetPost();
		$doctor=checkVariable($get['doctor'],0,'intval');
		$year=checkVariable($get['year'],0,'intval');
		$month=checkVariable($get['month'],0,'intval');
		$search=checkVariable($get['search'],'','trim');
		$processType=checkVariable($get['processType'],0,'intval');
        $parameters=array('type'=>0);
		$userSelectedFields=checkVariable($get['fieldNames'],0);
		$websiteDetails = new \Config\WebsiteDetails();
		$projectName=checkVariable($websiteDetails->projectName,'','trim');
		/*if(!isset($userSelectedFields) || isEmptyArray($userSelectedFields)<=0)
		{
			$userSelectedFields=array(0,1,2,3,4,5);
		}*/
		$fileTitle=' Doctorwise Report ';
		if(in_array($processType,array(2,3))==true)
		{
			if($year>0)
			{
				$parameters['year']=$year;
			}
			if($month>0)
			{
				$parameters['month']=$month;
			}
			$parameters['processType']=$processType;
			$parameters['search']=$search;
			if($processType==2)
			{
				$fileTitle=' Monthly Report ';
				$fieldList=array(
				array('name'=>'Sr No','field'=>'key','width'=>10,'position'=>1),
				array('name'=>'Doctor Name','field'=>'doctorName','width'=>40,'position'=>2),
				array('name'=>'Total Service','field'=>'totalServices','width'=>20,'position'=>3),
				array('name'=>'Amount','field'=>'amount','width'=>20,'position'=>4),
				array('name'=>'Commission','field'=>'commission','width'=>30,'position'=>5),
				array('name'=>'Earning','field'=>'earning','width'=>30,'position'=>6),
				);
				$userSelectedFields=array(0,1,2,3,4,5);
			}
			else
			{
				$fileTitle=' Servicewise Report ';
				$fieldList=array(
				array('name'=>'Sr No','field'=>'key','width'=>10,'position'=>1),
				array('name'=>'Service Name','field'=>'testName','width'=>40,'position'=>2),
				array('name'=>'Total Count	','field'=>'totalServices','width'=>20,'position'=>3),
				array('name'=>'Amount','field'=>'amount','width'=>20,'position'=>4),
				array('name'=>'Total Commission','field'=>'commission','width'=>20,'position'=>5),
				array('name'=>'Total Earning','field'=>'earning','width'=>30,'position'=>6),
				);
				$userSelectedFields=array(0,1,2,3,4,5);
			}
		}
		else
		{
			if($doctor>0)
			{
				$parameters['doctorID']=$doctor;
			}
			if($year>0)
			{
				$parameters['year']=$doctor;
			}
			if($month>0)
			{
				$parameters['month']=$month;
			}
			$parameters['search']=$search;
			$fieldList=array(
			array('name'=>'Sr No','field'=>'key','width'=>10,'position'=>1),
			array('name'=>'Date','field'=>'labDate','width'=>20,'position'=>2),
			array('name'=>'Patient Name','field'=>'patientName','width'=>40,'position'=>3),
			array('name'=>'Service Name','field'=>'testName','width'=>30,'position'=>4),
			array('name'=>'Commission','field'=>'commission','width'=>30,'position'=>5),
			);
			$userSelectedFields=array(0,1,2,3,4);
		}
		$reportModel = new ReportModel();
		$totalRecords=$reportModel->getList($parameters);
		if($totalRecords>0)
		{
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$totalRecords;
		$parameters['type']=1;
		$result=$reportModel->getList($parameters);		
		$maximum=0;
		if(isEmptyArray($result)>0)
		{
		$doctorList=0;
		$doctorModel = new DoctorModel();
		$para=array('type'=>0);
		$totalDoctor=$doctorModel->getList($para);
		if($totalDoctor>0)
		{
		$para=array('fetchField'=>" srNo,doctorName,designation",'action'=>1,'orderBy'=>' order by doctorName ASC ','isMultiple'=>1,'limit'=>$totalDoctor,'type'=>1);
		$doctorList=$doctorModel->getList($para);
		}
		$doctorListStatus=isEmptyArray($doctorList);
		$maximum=0;
		$showFields=array();
		$isAccompayningField=0;
		$userSelectedFields=checkVariable($post['fieldNames'],0);
		if(!isset($userSelectedFields) || isEmptyArray($userSelectedFields)<=0)
		{
		$userSelectedFields=array(0,1,2,3,4,5,6,7,10,19);
		}
	
		$totalFields=count($fieldList);
		if(isEmptyArray($userSelectedFields)>0)
		{
		$totalFields=count($fieldList);
		$userSelectedFields=array_unique($userSelectedFields);
		sort($userSelectedFields);
		foreach($userSelectedFields as $key=>$val)
		{
		if($val<$totalFields)
		{
		$showFields[]=$val;
		}
		}
		}
		else
		{
		if($totalFields>0)
		{
		$showFields=range(0,($totalFields-1));
		}
		}

		$idList=getColumnName(array('totalFields'=>$totalFields));
		if(isEmptyArray($showFields)>0)
		{
		foreach($showFields as $key=>$val)
		{
		$fieldInfos=array();
		if(isset($fieldList[$val]) && isEmptyArray($fieldList[$val])>0)
		{
		$fieldInfos=$fieldList[$val];	
		if(isset($idList[$key]) && !empty($idList[$key]))
		{
		$fieldInfos['id']=$idList[$key];
		}
		}
		$showFields[$key]=$fieldInfos;
		}
		}
		if(isEmptyArray($showFields)>0)
		{		
		$i=0;
		$spreadsheet = new Spreadsheet();
		$fileName=$projectName." ".$fileTitle;
		$spreadsheet->getProperties()
		->setCreator($projectName)
		->setLastModifiedBy($projectName)
		->setTitle("Office 2007 XLSX ".$fileName)
		->setSubject("Office 2007 XLSX ".$fileName)
		->setDescription($fileName)
		->setKeywords("office 2007 openxml php")
		->setCategory($fileName);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle($fileTitle);
		foreach($showFields as $obj)
		{
		$id=isset($obj['id']) ? trim($obj['id']) : '';
		$width=isset($obj['width']) ? trim($obj['width']) : 20;
		if(!empty($id))
		{
		$sheet->getColumnDimension($id)->setWidth($width);
		}
		}
		$i++;
		$spreadsheet->getDefaultStyle()->getFont()->setSize(20);
		$totalColumns=isEmptyArray($showFields);
		if($totalColumns>1)
		{
		$sheet->mergeCells($showFields[0]['id'].$i.':'.$showFields[count($showFields)-1]['id'].$i);
		}
		$sheet->getCell('A'.$i)->setValue($projectName);
		$styleArray = ['alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		],];
		$sheet->getStyle('A'.$i)->applyFromArray($styleArray);
		$i++;
		$spreadsheet->getDefaultStyle()->getFont()->setSize(15);
		if($totalColumns>1)
		{
		$sheet->mergeCells($showFields[0]['id'].$i.':'.$showFields[count($showFields)-1]['id'].$i);
		}
		$sheet->getCell('A'.$i)->setValue($fileTitle);
		$styleArray = ['alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		],];
		$sheet->getStyle('A'.$i)->applyFromArray($styleArray);
		$i++;
		$spreadsheet->getDefaultStyle()->getFont()->setSize(11);
		if($totalColumns>1)
		{
		$sheet->mergeCells($showFields[0]['id'].$i.':'.$showFields[count($showFields)-1]['id'].$i);
		}
		$sheet->getCell('A'.$i)->setValue('Date:'.date("d-M,Y h:i A"));
		$styleArray = ['alignment' => [
		'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		],];
		$sheet->getStyle('A'.$i)->applyFromArray($styleArray);
		$i++;
		//echo '<table border="1">';
		//echo '<thead>';
		//echo '<tr>';
		foreach($showFields as $obj)
		{
		$id=isset($obj['id']) ? trim($obj['id']) : '';
		$name=isset($obj['name']) ? trim($obj['name']) : '';	
		if(!empty($id) && !empty($name))
		{
		$sheet->setCellValue($id.$i, $name);
		//echo '<th>',$name,'</th>';
		}
		}
		//echo '</tr>';
		//echo '</thead>';
		//echo '<tbody>';
		$totalEarning=0;
		$totalAmount=0;
		$totalCommission=0;
		foreach($result as $key=>$row)
		{
		$i++;
		$key=$key+1;
		if($processType==1)
		{
		$testName=checkVariable($row['testName'],0,'trim');
		$patientName=checkVariable($row['patientName'],0,'trim');
		$amount=checkVariable($row['amount'],0,'doubleval');
		$commission=checkVariable($row['discountValue'],0,'doubleval');
		//$doctorID=checkVariable($test['doctorID'],0,'intval');
		$labDate=checkVariable($row['labDate'],0);
		$labDate=(!empty($labDate)) ? date("d-m-Y",strtotime($labDate)) : '';
		$doctorName='';
		$totalCommission+=$commission;
		}
		elseif($processType==2)
		{
			$totalServices=checkVariable($row['totalServices'],0,'intval');
			$amount=checkVariable($row['totalAmount'],0,'doubleval');
			$commission=checkVariable($row['totalDiscount'],0,'doubleval');
			$doctorID=checkVariable($row['doctorID'],0,'intval');
			$earning=($amount>0) ? ($amount-$commission) : 0;
			$doctorName='';
			$totalEarning+=$earning;
			$totalAmount+=$amount;
			$totalCommission+=$commission;
			if($doctorListStatus>0)
			{
				$info=searchValueInArray(array('data'=>$doctorList,'search'=>array('srNo'=>$doctorID),'type'=>1,'isSingle'=>1));
				if(isEmptyArray($info)>0){ 
				$doctorName=checkVariable($info['doctorName'],'','trim');
				}
			}
		}
		elseif($processType==3)
		{
			$totalServices=checkVariable($row['totalServices'],0,'intval');
			$amount=checkVariable($row['totalAmount'],0,'doubleval');
			$commission=checkVariable($row['totalDiscount'],0,'doubleval');
			$testName=checkVariable($row['testName'],0,'trim');
			$earning=($amount>0) ? ($amount-$commission) : 0;
			$totalEarning+=$earning;
			$totalAmount+=$amount;
			$totalCommission+=$commission;
			
		}
		
		foreach($showFields as $obj)
		{
		$id=isset($obj['id']) ? trim($obj['id']) : '';
		$fieldName=isset($obj['field']) ? trim($obj['field']) : '';
		if(!empty($id) && !empty($name) && isset($$fieldName))
		{
		$sheet->setCellValue($id.$i,  $$fieldName);
		//echo '<td>',$$fieldName,'</td>'; 
		}
		}
		//echo '</tr>';
		}
		//echo '</tbody>';
		//echo '</table>';
		$i++;
		if($processType==1)
		{
			if($totalColumns>1)
			{
			$sheet->mergeCells($showFields[0]['id'].$i.':'.$showFields[count($showFields)-1]['id'].$i);
			}
			$totalCommission='Total Commission:'.$totalCommission;
			$sheet->getCell('A'.$i)->setValue($totalCommission);
			$styleArray = [
			'font' => [
			'bold' => false,
			'size'=>12
			],
			'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
			],];
			$sheet->getStyle('A'.$i)->applyFromArray($styleArray);
			if($totalColumns<=1)
			{
			$totalCommission='Total Commission:'.$totalCommission;
			}
			$sheet->getCell($showFields[count($showFields)-1]['id'].$i)->setValue($totalCommission);
		}
		elseif(in_array($processType,array(2,3))==true)
		{
			if($totalColumns>1)
			{
			$sheet->mergeCells($showFields[0]['id'].$i.':'.$showFields[count($showFields)-1]['id'].$i);
			}
			$totalEarning='Total Earning:'.$totalEarning;
			$sheet->getCell('A'.$i)->setValue($totalEarning);
			$styleArray = [
			'font' => [
			'bold' => false,
			'size'=>12
			],
			'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
			],];
			$sheet->getStyle('A'.$i)->applyFromArray($styleArray);
			if($totalColumns<=1)
			{
			$totalEarning='Total Earning:'.$totalEarning;
			}
			$sheet->getCell($showFields[count($showFields)-1]['id'].$i)->setValue($totalEarning);
		}
		//if(ob_get_length() > 0) {  ob_clean();}
		if (ob_get_contents()) ob_end_clean();
		$filename=$fileName.'-'.date("Y-m-d h:i A");
		//flush();
		$responseObj = service('response');
		$responseObj->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$responseObj->setHeader('Content-Disposition','attachment;filename="'.$filename.'.xlsx');
		$responseObj->setHeader('Cache-Control', 'max-age=0');
		$responseObj->setHeader('Cache-Control', 'max-age=1');
		$responseObj->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$responseObj->setHeader('Last-Modified', gmdate('D, d M Y H:i:s').' GMT');
		$responseObj->setHeader('Cache-Control', 'cache, must-revalidate');
		$responseObj->setHeader('Pragma', 'public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		}
		}
		}
	}
	
}