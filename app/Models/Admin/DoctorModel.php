<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
//use CodeIgniter\Database\Query;
class DoctorModel extends Model
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
		$orderBy=checkVariable($options['orderBy'],' order by doctorName ASC ');
		$fetchField=checkVariable($options['fetchField'],' * ');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$doctorName=checkVariable($options['doctorName'],'','trim');
		$designation=checkVariable($options['designation'],'','trim');
		$hospitalName=checkVariable($options['hospitalName'],'','trim');
		$mobileNo=checkVariable($options['mobileNo'],'','trim');
		$doctorID=checkVariable($options['doctorID'],0,'intval');
		$isNotInID=checkVariable($options['isNotInID'],0,'intval');
		$doctorStatus=checkVariable($options['doctorStatus'],-1,'intval');
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$parameters=array();
		if($type==1)
		{
			if(empty($fetchField))
			{
			 $fetchField=" srNo,doctorName,designation,hospitalName,mobileNo,doctorStatus,reference,createdOn,updatedOn ";
			}
		}
		else
		{
			$fetchField=" count(srNo) as found ";
		}
		array_push($query,' isRemoved=? ');
		array_push($parameters,$isRemoved);	
		if($doctorStatus!=-1)
		{
		array_push($query,' doctorStatus=? ');
		array_push($parameters,$doctorStatus);
		}
		if($doctorID>0)
		{
			if($isNotInID==1)
			{
			array_push($query,' srNo!=? ');
			}
			else
			{
			array_push($query,' srNo=? ');	
			}
			array_push($parameters,$doctorID);
		}
		
		if(!empty($search))
		{
			array_push($query," ( doctorName like ? or designation like ? or hospitalName like ? or mobileNo like ?  ) ");
			$search='%'.$search.'%';
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
		}
		if(!empty($doctorName))
		{
			array_push($query,' doctorName=? ');
			array_push($parameters,$doctorName);
		}
		if(!empty($designation))
		{
			array_push($query,' designation=? ');
			array_push($parameters,$designation);
		}
		if(!empty($hospitalName))
		{
			array_push($query,' hospitalName=? ');
			array_push($parameters,$hospitalName);
		}
		if(!empty($mobileNo))
		{
			array_push($query,' mobileNo=? ');
			array_push($parameters,$mobileNo);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		if($type==1)
		{
			if(empty($orderBy))
			{
				$orderBy=' order by createdOn DESC ';
			}
			
			
			$parameters[]=$offset;
			$parameters[]=$limit;
			
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from doctor_list  ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from doctor_list   ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from doctor_list  ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from doctor_list   ";
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
	public function saveDetails($options=false)
	{
		$response=0;
		$sesType=checkVariable($options['sesType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
		$doctorName=checkVariable($options['doctorName'],'','trim');
		$designation=checkVariable($options['designation'],'','trim');
		$hospitalName=checkVariable($options['hospitalName'],'','trim');
		$mobileNo=checkVariable($options['mobileNo'],'','trim');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'doctorName'=>$doctorName,'designation'=>$designation,'hospitalName'=>$hospitalName,'mobileNo'=>$mobileNo,'doctorStatus'=>-1));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus>0)
		{
			$response=array('status'=>-2,'msg'=>$doctorName.' with  are already exists');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$doctorStatus=1;
			$this->db->transStart();
			$builder = $this->db->table('doctor_list');
			$data=array('doctorName'=>$doctorName,'designation'=>$designation,'hospitalName'=>$hospitalName,'mobileNo'=>$mobileNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID,'doctorStatus'=>$doctorStatus);
			$builder->set($data);
			$builder->insert();
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'doctor details are saved successfully');
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
		$doctorName=checkVariable($options['doctorName'],'','trim');
		$designation=checkVariable($options['designation'],'','trim');
		$hospitalName=checkVariable($options['hospitalName'],'','trim');
		$mobileNo=checkVariable($options['mobileNo'],'','trim');
		$doctorID=checkVariable($options['doctorID'],0,'intval');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'doctorName'=>$doctorName,'designation'=>$designation,'hospitalName'=>$hospitalName,'mobileNo'=>$mobileNo,'doctorID'=>$doctorID,'isNotInID'=>1));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus>0)
		{
			$response=array('status'=>-2,'msg'=>$doctorName.' with  are already exists');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('doctor_list');
			$where=array('srNo'=>$doctorID,'isRemoved'=>$isRemoved);
			$data=array('doctorName'=>$doctorName,'designation'=>$designation,'hospitalName'=>$hospitalName,'mobileNo'=>$mobileNo,'updatedOn'=>$dateTime,'reference'=>$userID);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'doctor details are updated successfully');
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
		$doctorID=checkVariable($options['doctorID'],0,'intval');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'doctorID'=>$doctorID));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid doctor id');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('doctor_list');
			$where=array('srNo'=>$doctorID,'isRemoved'=>0);
			$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'isRemoved'=>1);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'doctor details are removed successfully');
			}
			else
			{
				$response=array('status'=>144,'msg'=>'internal error occur');
			}
		}
		return $response;
	}
	public function statusDetails($options=false)
	{
		$response=0;
		$sesType=checkVariable($options['sesType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
		$doctorID=checkVariable($options['doctorID'],0,'intval');
		$doctorStatus=checkVariable($options['doctorStatus'],0,'intval');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'doctorID'=>$doctorID));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid doctor id');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('doctor_list');
			$where=array('srNo'=>$doctorID,'isRemoved'=>$isRemoved);
			$builder->where($where);
			$builder->limit(1);
			$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'doctorStatus'=>$doctorStatus);
			$builder->update($data);
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'Doctor status are changed successfully');
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