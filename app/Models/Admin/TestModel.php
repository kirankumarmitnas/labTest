<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
//use CodeIgniter\Database\Query;
use App\Models\Admin\CategoryModel;
use App\Models\Admin\ServiceModel;
use App\Models\Admin\DoctorModel;
class TestModel extends Model
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
		$orderBy=checkVariable($options['orderBy'],' order by labDate DESC ');
		$fetchField=checkVariable($options['fetchField'],' * ');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$isNotInID=checkVariable($options['isNotInID'],0,'intval');
		$lStatus=checkVariable($options['lStatus'],-1,'intval');
		$year=checkVariable($options['year'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$fromDate=checkVariable($options['fromDate'],'');
		$toDate=checkVariable($options['toDate'],'');
		$doctorID=checkVariable($options['doctorID'],-1,'intval');
		$fromDate=(!empty($fromDate)) ? date("Y-m-d",strtotime($fromDate)) : '';
		$toDate=(!empty($toDate)) ? date("Y-m-d",strtotime($toDate)) : '';
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$parameters=array();
		if($type==1)
		{
			
			if(empty($fetchField))
			{
			 $fetchField=" srNo,testID,doctorID,labDate,patientName,mobileNo,gender,age ";
			}
		}
		else
		{
			$fetchField=" count(srNo) as found ";
		}
		array_push($query,' isRemoved=? ');
		array_push($parameters,$isRemoved);	
		if($lStatus!=-1)
		{
		array_push($query,' lStatus=? ');
		array_push($parameters,$lStatus);
		}
		if(!empty($search))
		{
			
			array_push($query," ( patientName like ? or mobileNo like ? or testID in (select testID from test_details where isRemoved=? and testName like ? ) ) ");
			$search='%'.$search.'%';
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$isRemoved);	
			array_push($parameters,$search);
		}
		if($year>0)
		{
			array_push($query,' year(labDate)=? ');
			array_push($parameters,$year);
		}
		else
		{
			if(!empty($fromDate) && !empty($toDate))
			{
				array_push($query,' date(labDate) between date(?) and date(?) ');
				array_push($parameters,$fromDate);
				array_push($parameters,$toDate);
			}
			elseif(!empty($fromDate))
			{
				array_push($query,' date(labDate)=date(?) ');
				array_push($parameters,$fromDate);
			}
			elseif(!empty($toDate))
			{
				array_push($query,' date(labDate)=date(?)');
				array_push($parameters,$toDate);
			}
		}
		
		if($doctorID>0)
		{
			array_push($query,' doctorID=? ');
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
				$orderBy=' order by labDate DESC ';
			}
			
			
			$parameters[]=$offset;
			$parameters[]=$limit;
			
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from test_list  ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from test_list   ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from test_list  ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from test_list   ";
			}
		}
		//SELECT `srNo`, `testID`, `doctorID`, `labDate`, `patientName`, `mobileNo`, `gender`, `age`, `lStatus`, `reference`, `createdOn`, `updatedOn`, `isRemoved` FROM `test_list` WHERE 1
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
	public function getTestDetails($options=false)
	{
		$response=0;
		$type=checkVariable($options['type'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$patientName=checkVariable($options['patientName'],0,'trim');
		$mobileNo=checkVariable($options['mobileNo'],0,'trim');
		$testDate=checkVariable($options['testDate'],0,'trim');
		$testDate=(!empty($testDate)) ? date("Y-m-d",strtotime($testDate)) : '';
		$age=checkVariable($options['age'],0,'intval');
		$gender=checkVariable($options['gender'],0,'intval');
		$doctorID=checkVariable($options['doctorID'],-1,'intval');
		$testID=checkVariable($options['testID'],0,'intval');
		$isNotInID=checkVariable($options['isNotInID'],0,'intval');
		$lStatus=checkVariable($options['lStatus'],-1,'intval');
		$offset=abs(checkVariable($options['offset'],0,'intval'));
		$limit=abs(checkVariable($options['limit'],20,'intval'));
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$parameters=array();
		if($type==1)
		{
			if(empty($fetchField))
			{
				$fetchField=" srNo,testID,doctorID,labDate,patientName,mobileNo,gender,age ";
			}
		}
		else
		{
			$fetchField=" count(srNo) found";
		}
		array_push($query,' isRemoved=? ');
		array_push($parameters,$isRemoved);	
		//array_push($query,' cd.isRemoved=? ');
		//array_push($parameters,$isRemoved);	
		if($lStatus!=-1)
		{
		array_push($query,' lStatus=? ');
		array_push($parameters,$lStatus);
		}
		if(!empty($testDate))
		{
			array_push($query," date(labDate) = date(?) ");
			array_push($parameters,$testDate);
		}
		if(!empty($age))
		{
			array_push($query,' age=? ');
			array_push($parameters,$age);
		}
		if(!empty($gender))
		{
			array_push($query,' gender=? ');
			array_push($parameters,$gender);
		}
		if(!empty($mobileNo))
		{
			array_push($query,' mobileNo=? ');
			array_push($parameters,$mobileNo);
		}
		if(!empty($patientName))
		{
			array_push($query,' patientName=? ');
			array_push($parameters,$patientName);
		}
		if($testID>0)
		{
			if($type==1)
			{
				if($isNotInID==1)
				{
				array_push($query,' srNo!=? ');
				}
				else
				{
				array_push($query,' srNo=? ');	
				}
			}
			else
			{
				if($isNotInID==1)
				{
				array_push($query,' testID!=? ');
				}
				else
				{
				array_push($query,' testID=? ');	
				}
			}
			array_push($parameters,$testID);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		if($type==1)
		{
			if(empty($orderBy))
			{
				$orderBy=' order by labDate DESC ';
			}
			$parameters[]=$offset;
			$parameters[]=$limit;
			
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT ".$fetchField." from test_list ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT ".$fetchField." from test_list  ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT ".$fetchField." from test_list  ".$queryStr;
			}
			else
			{
				$queryText="SELECT ".$fetchField." from test_list  ";
			}
		}
		$selQuery = $this->db->query($queryText,$parameters);
		//echo $this->db->getLastQuery();
		if($selQuery->getNumRows()>0)
		{
			if($type==1)
			{
				if($action==1)
				{
					if($isMultiple==1)
					{
						$response=array();
						$rows=$selQuery->getResultArray();
						foreach($rows as $row)
						{
							$testID=checkVariable($row['testID'],'','trim');
							if(!empty($testID))
							{
								$queryText="select srNo,testID,serviceID,testName,amount,discountValue from test_details where isRemoved=? and testID=? order by tOrder ASC";
								$parameters=array($isRemoved,$testID);
								$selQuery = $this->db->query($queryText,$parameters);
								if($selQuery->getNumRows()>0)
								{
									$row['testList']=$selQuery->getResultArray();
								}
							}
							$response[]=$row;
						}
					}
					else
					{
						$row=$selQuery->getRowArray();
						$testID=checkVariable($row['testID'],'','trim');
						if(!empty($testID))
						{
							$queryText="select srNo,testID,serviceID,testName,amount,discountValue from test_details where isRemoved=? and testID=? order by tOrder ASC";
							$parameters=array($isRemoved,$testID);
							$selQuery = $this->db->query($queryText,$parameters);
							if($selQuery->getNumRows()>0)
							{
								$row['testList']=$selQuery->getResultArray();
							}
						}
						$response=$row;
					}
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
	public function saveDetails($options=false)
	{
		$response=0;
		$sesType=checkVariable($options['sesType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
	    $patientName=checkVariable($options['patientName'],0,'trim');
		$mobileNo=checkVariable($options['mobileNo'],0,'trim');
		$testDate=checkVariable($options['testDate'],0,'trim');
		$testDate=(!empty($testDate)) ? date("Y-m-d",strtotime($testDate)) : date("Y-m-d");
		$doctor=checkVariable($options['doctor'],0,'intval');
		$age=checkVariable($options['age'],0,'intval');
		$gender=checkVariable($options['gender'],0,'intval');
		$serviceList=checkVariable($options['services'],0);
		$servicesStatus=isEmptyArray($serviceList);
		$serviceModel = new ServiceModel();
		$doctorModel = new DoctorModel();
		$doctorStatus=$doctorModel->getList(array('type'=>0,'doctorID'=>$doctor));
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($doctorStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid doctor');
		}
		elseif($servicesStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'services are mandatory for test|'.$servicesStatus);
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$cStatus=1;
			$this->db->transStart();
			$builder = $this->db->table('test_list');
			$testID=uniqid('L');
			$data=array('testID'=>$testID,'patientName'=>$patientName,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID,'lStatus'=>1,'mobileNo'=>$mobileNo,'gender'=>$gender,'age'=>$age,'doctorID'=>$doctor,'labDate'=>$testDate);
			$builder->set($data);
			$builder->insert();
			$testSrNo=$this->db->insertID();
			if($testSrNo>0)
			{
				if($servicesStatus>0)
				{
					$serviceList=array_diff($serviceList,array(null,0,'',' '));
					$servicesStatus=isEmptyArray($serviceList);
					if($servicesStatus>0)
					{
						$builder = $this->db->table('test_details');
						foreach($serviceList as $serviceID)
						{
							$serviceInfo=$serviceModel->getList(array('type'=>1,'serviceID'=>$serviceID,'action'=>1));
							if(isEmptyArray($serviceInfo)>0)
							{
								$amount=checkVariable($serviceInfo['amount'],0,'doubleval');
								$testName=checkVariable($serviceInfo['testName'],0,'trim');
								$discountValue=checkVariable($serviceInfo['discountValue'],0,'doubleval');
								$data=array('testID'=>$testID,'serviceID'=>$serviceID,'tStatus'=>1,'discountValue'=>$discountValue,'testName'=>$testName,'amount'=>$amount,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID);
								$builder->set($data);
								$builder->insert();
							}
						}
					}					
				}
			}
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'test details are saved successfully');
			}
			else
			{
				$response=array('status'=>144,'msg'=>'internal error occur');
			}
		}
		return $response;
	}
	public function updateDetails($options=false)
	{
		$response=0;
		$sesType=checkVariable($options['sesType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
		$patientName=checkVariable($options['patientName'],0,'trim');
		$mobileNo=checkVariable($options['mobileNo'],0,'trim');
		$testDate=checkVariable($options['testDate'],0,'trim');
		$testDate=(!empty($testDate)) ? date("Y-m-d",strtotime($testDate)) : date("Y-m-d");
		$doctor=checkVariable($options['doctor'],0,'intval');
		$testSrNo=checkVariable($options['testID'],0,'trim');
		$age=checkVariable($options['age'],0,'intval');
		$gender=checkVariable($options['gender'],0,'intval');
		$serviceList=checkVariable($options['services'],0);
		$servicesStatus=isEmptyArray($serviceList);
		$serviceModel = new ServiceModel();
		$doctorModel = new DoctorModel();
		$doctorStatus=$doctorModel->getList(array('type'=>0,'doctorID'=>$doctor));
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$testDetails=$this->getTestDetails(array('type'=>1,'testID'=>$testSrNo,'action'=>1));
		$testDetailsStatus=isEmptyArray($testDetails);
		$testID=($testDetailsStatus>0) ? checkVariable($testDetails['testID'],'','trim') : '';
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($doctorStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid doctor');
		}
		elseif($testDetailsStatus<=0 || empty($testID))
		{
			$response=array('status'=>-3,'msg'=>'invalid test id');
		}
		elseif($servicesStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'services are mandatory for test|'.$servicesStatus);
		}
		else
		{
			
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('test_list');
			$where=array('testID'=>$testID,'isRemoved'=>$isRemoved,'srNo'=>$testSrNo);
			$data=array('patientName'=>$patientName,'updatedOn'=>$dateTime,'reference'=>$userID,'mobileNo'=>$mobileNo,'gender'=>$gender,'age'=>$age,'doctorID'=>$doctor,'labDate'=>$testDate);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
			$updated=$this->db->affectedRows();
			$builder = $this->db->table('test_details');
			if($servicesStatus>0)
			{
				$testList=($testDetailsStatus>0) ? checkVariable($testDetails['testList'],0) : 0;
				$testListStatus=isEmptyArray($testList);
				$testList2=getArrayKeyValues(array('fields'=>array('serviceID'),'data'=>$testList));
				$testListStatus2=isEmptyArray($testList2);
				if($testListStatus2>0)
				{
					$addData=array_diff($serviceList,$testList2);
					if(isEmptyArray($addData)>0)
					{
						foreach($addData as $aData)
						{
							$serviceInfo=$serviceModel->getList(array('type'=>1,'serviceID'=>$aData,'action'=>1));
							if(isEmptyArray($serviceInfo)>0)
							{
								$amount=checkVariable($serviceInfo['amount'],0,'doubleval');
								$testName=checkVariable($serviceInfo['testName'],0,'trim');
								$discountValue=checkVariable($serviceInfo['discountValue'],0,'doubleval');
								$data=array('testID'=>$testID,'serviceID'=>$aData,'tStatus'=>1,'discountValue'=>$discountValue,'testName'=>$testName,'amount'=>$amount,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID);
								$builder->set($data);
								$builder->insert();
							}
						}
					}
					$removeData=array_diff($testList2,$serviceList);
					if(isEmptyArray($removeData)>0 && $testListStatus>0)
					{
						foreach($removeData as $rData)
						{
							$info=searchValueInArray(array('data'=>$testList,'search'=>array('serviceID'=>$rData),'type'=>1,'isSingle'=>1));
							if(isEmptyArray($info)>0)
							{
								$srNo=checkVariable($info['srNo'],0,'intval');
								$where=array('srNo'=>$srNo,'serviceID'=>$rData,'isRemoved'=>$isRemoved);
								$data=array('isRemoved'=>1,'tStatus'=>0,'updatedOn'=>$dateTime,'reference'=>$userID);
								$builder->where($where);
								$builder->limit(1);
								$builder->update($data);
							}
						}
					}
				}
				else
				{
					foreach($serviceList as $serviceID)
					{
						$serviceInfo=$serviceModel->getList(array('type'=>1,'serviceID'=>$serviceID,'action'=>1));
						if(isEmptyArray($serviceInfo)>0)
						{
							$amount=checkVariable($serviceInfo['amount'],0,'doubleval');
							$testName=checkVariable($serviceInfo['testName'],0,'trim');
							$discountValue=checkVariable($serviceInfo['discountValue'],0,'doubleval');
							$data=array('testID'=>$testID,'serviceID'=>$serviceID,'tStatus'=>1,'discountValue'=>$discountValue,'testName'=>$testName,'amount'=>$amount,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID);
							$builder->set($data);
							$builder->insert();
						}
					}
				}
			}
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'test details are updated successfully');
			}
			else
			{
				$response=array('status'=>144,'msg'=>'internal error occur');
			}
		}
		return $response;
	}
	public function removeDetails($options=false)
	{
		$response=0;
		$sesType=checkVariable($options['sesType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
		$testSrNo=checkVariable($options['testID'],0,'trim');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$testDetails=$this->getTestDetails(array('type'=>1,'testID'=>$testSrNo,'action'=>1));
		$testDetailsStatus=isEmptyArray($testDetails);
		$testID=($testDetailsStatus>0) ? checkVariable($testDetails['testID'],'','trim') : '';
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif(empty($testID))
		{
			$response=array('status'=>-2,'msg'=>'invalid test id');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('test_list');
			$where=array('testID'=>$testID,'isRemoved'=>0,'srNo'=>$testSrNo);
			$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'isRemoved'=>1);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
			$updated=$this->db->affectedRows();
			if($updated>0)
			{
			$builder = $this->db->table('test_details');
			$where=array('testID'=>$testID,'isRemoved'=>$isRemoved);
			$data=array('isRemoved'=>1,'updatedOn'=>$dateTime,'reference'=>$userID);
			$builder->where($where);
			$builder->update($data);
			}
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'test details are removed successfully');
			}
			else
			{
				$response=array('status'=>144,'msg'=>'internal error occur');
			}
		}
		return $response;
	}
	
	
}
?>