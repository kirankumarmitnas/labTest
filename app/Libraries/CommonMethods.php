<?php
namespace App\Libraries;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
use CodeIgniter\Database\Query;
use Mpdf\Mpdf;
use App\Models\AccountModel;
use App\Models\Admin\RegistrationModel;
use CodeIgniter\HTTP\Response;
class  CommonMethods
{
	public static $db;
    public static function getUserPermission($options=false)
    {
		$response=0;
		$response=array(
		array('userType'=>2,'catelog'=>1,'isAll'=>1,'urlType'=>array(1)),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>1),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>2),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>3),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>4),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>5),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>7),
		array('userType'=>2,'catelog'=>2,'process'=>-1,'id'=>8),
		array('userType'=>2,'catelog'=>2,'process'=>1,'id'=>6,'child'=>array(16,17,18)),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>1),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>2),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>3),
	    array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>4),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>5),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>7),
		array('userType'=>2,'catelog'=>2,'process'=>-1,'id'=>8),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>6),
		array('userType'=>3,'catelog'=>2,'process'=>1,'id'=>95,'child'=>array(16,17,18)),
		);
		return $response;
	}
	public static function getOptionDisplayTypes($options=false)
	{
		$response=0;
		$response=array(
		array('id'=>0,'name'=>'Columns wise'),
		array('id'=>1,'name'=>'Row wise'),
		);
		return $response;
	}
	public static function getGenderWiseTypes($options=false)
	{
		$response=0;
		$response=array(
		array('id'=>1,'name'=>'Male'),
		array('id'=>2,'name'=>'Female'),
		array('id'=>3,'name'=>'Other'),
		array('id'=>0,'name'=>'None'),
		);
		return $response;
	}
	public static function getReportOptions($options=false)
	{
		$response=array();
		$db=\Config\Database::connect();
		$isRemoved=0;
		$minYear=intval(date("Y"));
		$maxYear=intval(date("Y"));
		$queryText="SELECT min(year(labDate)) minYear,max(year(labDate)) maxYear FROM test_list where isRemoved=?";
		$parameters=array($isRemoved);
		$selQuery = $db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			$row=$selQuery->getRowArray();
			$minYear=checkVariable($row['minYear'],$minYear,'intval');
			$maxYear=checkVariable($row['maxYear'],$maxYear,'intval');
		}
		$response['yearList']=range($minYear,$maxYear);
		$monthList=array();
		for($month = 1 ; $month <= 12; $month++)
		{
		$monthName=date("F",mktime(0,0,0,$month,1,date("Y")));
		$response['monthList'][]=array('name'=>$monthName,'id'=>$month);
		}
		return $response;
	}
	public static function getContryList($options=false)
	{
		$response=0;
		$db=\Config\Database::connect();
		$type=checkVariable($options['type'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		$countryID=checkVariable($options['countryID'],0,'intval');
		$para=array();
		$queryText = "select countryID,name from country order by name ASC";	
		if($countryID>0)
		{
		$queryText = "select countryID,name from country where countryID=? order by name ASC";
		array_push($para,$countryID);
		}
		if(!empty($queryText))
		{
			$query = $db->query($queryText,$para);
			if($query->getNumRows()>0)
			{
				$response=$query->getResultArray();
			}
		}
		return $response;
	}
	
	public static function getDashboardStatistics($options)
	{
		$response=array();
		$isRemoved=0;
		$registerStatus=1;
		$payStatus=2;
		$db=\Config\Database::connect();
		$timing = new \Config\Timing();
        $today = $timing->today;
		$currentMonth=intval($timing->currentMonth);
		$currentDay=intval($timing->currentDay);
		$currentYear=intval($timing->currentYear);
		$queryText="SELECT count(srNo) found ,DAY(labDate) day FROM test_list where lStatus=? and isRemoved=? and year(labDate)=? and month(labDate)=? group by day(labDate) order by day ASC ";
		$parameters=array(1,$isRemoved,$currentYear,$currentMonth);
		$selQuery = $db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			$rows=$selQuery->getResultArray();
			$response['testList']['dateWise']=$rows;
		}
		$queryText="SELECT count(srNo) found,month(labDate) month FROM test_list where lStatus=? and isRemoved=? and year(labDate)=? group by month(labDate) order by month ASC ";
		$parameters=array(1,$isRemoved,$currentYear);
		$selQuery = $db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			$rows=$selQuery->getResultArray();
			$response['testList']['monthWise']=$rows;
		}
		$limit=5;
		$queryText="SELECT tl.testID,tl.doctorID,tl.labDate,tl.patientName,dl.doctorName FROM test_list tl left join doctor_list dl on tl.doctorID=dl.srNo WHERE tl.lStatus=? and tl.isRemoved=? order by date(tl.labDate) DESC limit ? ";
		$parameters=array(1,$isRemoved,$limit);
		$selQuery = $db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			$result=$selQuery->getResultArray();
			if(isEmptyArray($result)>0)
			{
				foreach($result as $row)
				{
					$testID=checkVariable($row['testID'],'','trim');
					if(!empty($testID))
					{
						$queryText2="SELECT testName,amount,discountValue FROM test_details WHERE isRemoved=? and tStatus=? and testID=? order by tOrder ASC ";
						$parameters2=array($isRemoved,1,$testID);
						$selQuery2 = $db->query($queryText2,$parameters2);
						if($selQuery2->getNumRows()>0)
						{
							$row['services']=$selQuery2->getResultArray();
						}
					}
					$response['testList']['patients'][]=$row;
				}
			}
		}
		$queryText="SELECT td.testName,td.amount,td.discountValue,tl.labDate,dl.doctorName FROM test_details td inner join test_list tl on td.testID=tl.testID left join doctor_list dl on tl.doctorID=dl.srNo WHERE td.isRemoved=? and td.tStatus=? order by tl.labDate DESC,td.tOrder ASC limit ? ";
		$parameters=array($isRemoved,1,$limit);
		$selQuery = $db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			$response['testList']['services']=$selQuery->getResultArray();
		}
		/*$fromDate=date("Y-m-d",strtotime('-7 days',$today));
		$toDate=date("Y-m-d",$today);
		$queryText="select count(srNo) found,date(createdOn) createdOn from member_registration where registerStatus=? and isRemoved=? and date(createdOn) BETWEEN date(?) and date(?) GROUP by date(createdOn) ORDER by date(createdOn) DESC ";
		$parameters=array($registerStatus,$isRemoved,$fromDate,$toDate);
		$selQuery = $db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			$rows=$selQuery->getResultArray();
			$response['registration']['datewise']=$rows;
		}*/
		
		$accountModel=new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$userPermissions=$accountModel->getPermissionList(array('userID'=>$userID,'type'=>1));
		if(isEmptyArray($userPermissions)>0)
		{
			$userPermissions=searchValueInArray(array('type'=>1,'data'=>$userPermissions,'search'=>array('is_parent'=>1)));
			$response['userPermissions']=getArrayKeyValues(array('data'=>$userPermissions,'fields'=>array('menu_id')));
		}
		return $response;
	}
	public static function getZoneList($options=false)
	{
		$response=0;
		$db=\Config\Database::connect();
		$type=checkVariable($options['type'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		$status=checkVariable($options['status'],-1,'intval');
		$countryID=checkVariable($options['countryID'],0,'intval');
		$stateID=checkVariable($options['stateID'],0,'intval');
		$fetchFields=checkVariable($options['fetchFields'],' ');
		$orderBy=checkVariable($options['orderBy'],' ');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$parameters=array();
		if(!empty($stateID))
		{
		array_push($query,' zoneID=? ');
		array_push($parameters,$stateID);
		}
		if($status!=-1)
		{
		array_push($query,' status=? ');
		array_push($parameters,$status);
		}
		if(!empty($countryID))
		{
		array_push($query,' countryID=? ');
		array_push($parameters,$countryID);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		$fetchFields=(empty(trim($fetchFields))) ?  ' * ' : $fetchFields;
		$orderBy=(empty($orderBy)) ?  ' order by zoneID ASC ' : $orderBy;
		$query = $db->query("select ".$fetchFields." from zone ".$queryStr.$orderBy,$parameters);
		if($query->getNumRows()>0)
		{
			$totalRows=$query->getNumRows();
			if($action==1)
			{
				$rows=0;
				$rows=($isMultiple==1) ? $query->getResultArray() : $query->getRowArray();
				$response=$rows;
			}
			else
			{
				$response= 1;
			}
		}
		return $response;
	
	}
    public static function convertToArray($row) {
		return array_values(json_decode(json_encode($row), true));
	}
	public static function backupDownload($options=false) {
		
		$table_arr = array();
		$content = array();
		set_time_limit(0);
		$db=\Config\Database::connect();
		$sel_query = "SHOW TABLES";
		$sel_query = $db->query($sel_query);
		if($sel_query->getNumRows()>0)
		{
		foreach($sel_query->getResult() as $row) {
			$row =self::convertToArray($row);
			$table_arr[] = $row[0];
		}
		foreach($table_arr as $table) {
			$sel_query1 = "SHOW CREATE TABLE ".$table;
			$sel_query1 = $db->query($sel_query1);
			$row1 = $sel_query1->getRow();
			$row1 = self::convertToArray($row1);
			$bundle = $row1[1].";\n\n";
			
			$i = 0;
			$sel_query = "SELECT * from ".$table;
			$sel_query = $db->query($sel_query);
			if($sel_query->getNumRows()>0) :
				$bundle .= "INSERT INTO ".$table." VALUES";
				foreach($sel_query->getResult() as $row) {
					$row = self::convertToArray($row);
					$column_c = $sel_query->getFieldCount();
					$rows_c = $sel_query->getNumRows();
					$bundle .= "\n(";
					for($j = 0;$j<$column_c; $j++) {
						$comma = ", ";
						if($j == ($column_c-1)) :
							$comma = "";
						endif;
						$bundle .= '"'.addslashes($row[$j]).'"'.$comma;
					}
					if($i == $rows_c-1) :
						$bundle .= ");\n\n\n\n";
					else :
						$bundle .= "),";
					endif;       
				$i++;}
			endif;
			$content[] = $bundle;
		}
		$websiteDetails = new \Config\WebsiteDetails();
		$projectName=checkVariable($websiteDetails->projectName,'','trim');
		$content = implode("", $content);
		if(ob_get_length() > 0) {  ob_clean();}
        $fileName=$projectName."-(".date('d-m-Y')."-".date('h-i-s-A').")".time().".sql"; 
		$responseObj = service('response');
		$responseObj->setHeader('Content-Type', 'application/octet-stream');
		$responseObj->setHeader('Content-Transfer-Encoding', 'Binary');
		$responseObj->setHeader('Content-Disposition','attachment;filename="'.$fileName.'"');
		echo $content; 
		}
	}
    

}