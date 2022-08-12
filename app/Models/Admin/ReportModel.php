<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
//use CodeIgniter\Database\Query;
use App\Models\Admin\CategoryModel;
use App\Models\Admin\ServiceModel;
use App\Models\Admin\DoctorModel;
class ReportModel extends Model
{
	public $db;
	public function __construct()
	{
		$this->db=\Config\Database::connect();
	} 
	public function getList($options)
	{
		$response=0;
		$type=checkVariable($options['type'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		$offset=abs(checkVariable($options['offset'],0,'intval'));
		$limit=abs(checkVariable($options['limit'],20,'intval'));
		$orderBy=checkVariable($options['orderBy'],' order by tl.labDate DESC ');
		$processType=checkVariable($options['processType'],0,'intval');
		$fetchField=checkVariable($options['fetchField'],' * ');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$year=checkVariable($options['year'],0,'intval');
		$month=checkVariable($options['month'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$fromDate=checkVariable($options['fromDate'],'');
		$toDate=checkVariable($options['toDate'],'');
		$doctorID=checkVariable($options['doctorID'],-1,'intval');
		$fromDate=(!empty($fromDate)) ? date("Y-m-d",strtotime($fromDate)) : '';
		$toDate=(!empty($toDate)) ? date("Y-m-d",strtotime($toDate)) : '';
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$groupBy='';
		$parameters=array();
		if($processType==2)
		{
			$fetchField=" count(td.srNo) totalServices ,SUM(COALESCE(td.amount,0)) totalAmount,SUM(COALESCE(td.discountValue,0)) totalDiscount,tl.doctorID ";
			$groupBy=" group by tl.doctorID ";
		}
		elseif($processType==3)
		{
			$fetchField=" td.testName,count(td.srNo) totalServices ,SUM(COALESCE(td.amount,0)) totalAmount,SUM(COALESCE(td.discountValue,0)) totalDiscount ";
			$groupBy=" group by td.serviceID ";
		}
		if($type==1)
		{
			if(empty($fetchField))
			{
			 $fetchField=" td.serviceID,td.testName,td.amount,td.discountValue,tl.doctorID,tl.patientName,tl.labDate ";
			}
		}
		else
		{
			$fetchField=" count(td.srNo) as found ";
		}
		array_push($query,' td.isRemoved=? ');
		array_push($parameters,$isRemoved);	
		array_push($query,' tl.isRemoved=? ');
		array_push($parameters,$isRemoved);	
		if(!empty($search))
		{
			array_push($query," ( tl.patientName like ? or tl.mobileNo like ? or  td.testName like ? ) ");
			$search='%'.$search.'%';
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
		}
		if($year>0)
		{
			array_push($query,' year(tl.labDate)=? ');
			array_push($parameters,$year);
		}
		if($month>0)
		{
			array_push($query,' month(tl.labDate)=? ');
			array_push($parameters,$month);
		}
		if(!empty($fromDate) && !empty($toDate))
		{
			array_push($query,' date(tl.labDate) between date(?) and date(?) ');
			array_push($parameters,$fromDate);
			array_push($parameters,$toDate);
		}
		elseif(!empty($fromDate))
		{
			array_push($query,' date(tl.labDate)=date(?) ');
			array_push($parameters,$fromDate);
		}
		elseif(!empty($toDate))
		{
			array_push($query,' date(tl.labDate)=date(?)');
			array_push($parameters,$toDate);
		}
		if($doctorID>0)
		{
			array_push($query,' tl.doctorID=? ');
			array_push($parameters,$doctorID);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		if($type==1)
		{
			if(empty($orderBy))
			{
				$orderBy=' order by tl.labDate DESC ';
			}
			$parameters[]=$offset;
			$parameters[]=$limit;
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." FROM test_details td left join test_list tl on td.testID=tl.testID   ".$queryStr." ".$groupBy." ".$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." FROM test_details td left join test_list tl on td.testID=tl.testID    ".$groupBy." ".$orderBy." limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." FROM test_details td left join test_list tl on td.testID=tl.testID  ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." FROM test_details td left join test_list tl on td.testID=tl.testID   ";
			}
		}
		$selQuery = $this->db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			if($type==1)
			{
				if($action==1)
				{
					$response=($isMultiple==1) ? $selQuery->getResultArray() : $selQuery->getRowArray();
				}
				else
				{
					$response= 1;
				}
			}
			else
			{
				$row = $selQuery->getRowArray();
				$response=checkVariable($row['found'],0,'intval');
			}
		}
		return $response;
	}
	
}
?>