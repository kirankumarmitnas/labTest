<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
//use CodeIgniter\Database\Query;
use App\Models\Admin\CategoryModel;
use App\Models\Admin\ServiceModel;
use App\Models\Admin\DoctorModel;
class PaymentModel extends Model
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
		$fetchField=checkVariable($options['fetchField'],'','trim');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$year=checkVariable($options['year'],0,'intval');
		$month=checkVariable($options['month'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$fieldSeparator=checkVariable($options['fieldSeparator'],'||','trim');
		$fromDate=checkVariable($options['fromDate'],'');
		$toDate=checkVariable($options['toDate'],'');
		$doctorID=checkVariable($options['doctorID'],-1,'intval');
		$fromDate=(!empty($fromDate)) ? date("Y-m-d",strtotime($fromDate)) : '';
		$toDate=(!empty($toDate)) ? date("Y-m-d",strtotime($toDate)) : '';
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$groupBy=' group by tl.testID ';
		$parameters=array();
		if($type==1)
		{
			if(empty($fetchField))
			{
			 $fetchField=" GROUP_CONCAT(td.testName ORDER BY td.testName ASC SEPARATOR '".$fieldSeparator."' ) services,COALESCE(sum(td.amount),0) totalAmount,tl.labDate,tl.patientName,tl.doctorID ";
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
		if($year>0 || $month>0)
		{
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
		}
		else
		{
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
				$queryText="SELECT  ".$fetchField." FROM test_list tl inner join test_details td on tl.testID=td.testID   ".$queryStr." ".$groupBy." ".$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." FROM test_list tl inner join test_details td on tl.testID=td.testID    ".$groupBy." ".$orderBy." limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="select count(*) found from ( SELECT tl.srNo  FROM test_list tl inner join test_details td on tl.testID=td.testID  ".$queryStr.$groupBy." ) details ";
			}
			else
			{
				$queryText="select count(*) found from ( SELECT tl.srNo FROM test_list tl inner join test_details td on tl.testID=td.testID ".$groupBy." ) details ";
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