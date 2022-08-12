<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
//use CodeIgniter\Database\Query;
use App\Models\Admin\CategoryModel;
class ServiceModel extends Model
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
		$orderBy=checkVariable($options['orderBy'],' order by testName ASC ');
		$fetchField=checkVariable($options['fetchField'],' * ');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$testName=checkVariable($options['testName'],'','trim');
		$amount=checkVariable($options['amount'],0,'doubleval');
		$discountType=checkVariable($options['discountType'],0,'intval');
		$discountValue=checkVariable($options['discountValue'],0,'doubleval');
		$categoryID=checkVariable($options['categoryID'],-1,'intval');
		$serviceID=checkVariable($options['serviceID'],0,'intval');
		$isNotInID=checkVariable($options['isNotInID'],0,'intval');
		$tStatus=checkVariable($options['tStatus'],-1,'intval');
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$parameters=array();
		if($type==1)
		{
			if(empty($fetchField))
			{
			 $fetchField=" srNo,testName,categoryID, amount, discountType,discountValue, reference,createdOn,updatedOn ";
			}
		}
		else
		{
			$fetchField=" count(srNo) as found ";
		}
		array_push($query,' isRemoved=? ');
		array_push($parameters,$isRemoved);	
		if($tStatus!=-1)
		{
		array_push($query,' tStatus=? ');
		array_push($parameters,$tStatus);
		}
		if($serviceID>0)
		{
			if($isNotInID==1)
			{
			array_push($query,' srNo!=? ');
			}
			else
			{
			array_push($query,' srNo=? ');	
			}
			array_push($parameters,$serviceID);
		}
		if(!empty($search))
		{
			array_push($query," testName like ?  ");
			$search='%'.$search.'%';
			array_push($parameters,$search);
		}
		if(!empty($testName))
		{
			array_push($query,' testName=? ');
			array_push($parameters,$testName);
		}
		if($categoryID!=-1)
		{
			array_push($query,' categoryID=? ');
			array_push($parameters,$categoryID);
		}
		if($amount>0)
		{
			array_push($query,' amount=? ');
			array_push($parameters,$amount);
		}
		if($discountType>0)
		{
			array_push($query,' discountType=? ');
			array_push($parameters,$discountType);
		}
		if($discountValue>0)
		{
			array_push($query,' discountValue=? ');
			array_push($parameters,$discountValue);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		if($type==1)
		{
			if(empty($orderBy))
			{
				$orderBy=' order by testName DESC ';
			}
			
			
			$parameters[]=$offset;
			$parameters[]=$limit;
			
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from service_list  ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from service_list   ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from service_list  ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from service_list   ";
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
		$testName=checkVariable($options['testName'],0,'trim');
		$categoryID=checkVariable($options['categoryName'],0,'intval');
		$discountValue=checkVariable($options['discountValue'],0,'doubleval');
		$amount=checkVariable($options['amount'],0,'doubleval');
		$categoryModel = new CategoryModel();
		$categoryStatus=$categoryModel->getList(array('type'=>0,'categoryID'=>$categoryID));
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'testName'=>$testName,'categoryID'=>$categoryID));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus>0)
		{
			$response=array('status'=>-2,'msg'=>$testName.' with  are already exists');
		}
		elseif($categoryID>0 && $categoryStatus<=0)
		{
			$response=array('status'=>-3,'msg'=>' invalid category');
		}
		elseif($discountValue>$amount)
		{
			$response=array('status'=>-4,'msg'=>' discount amount must be less than amount1');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$cStatus=1;
			$this->db->transStart();
			$builder = $this->db->table('service_list');
			$data=array('testName'=>$testName,'categoryID'=>$categoryID,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID,'tStatus'=>1,'amount'=>$amount,'discountType'=>1,'discountValue'=>$discountValue);
			$builder->set($data);
			$builder->insert();
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
		$serviceID=checkVariable($options['serviceID'],0,'intval');
		$testName=checkVariable($options['testName'],0,'trim');
		$categoryID=checkVariable($options['categoryName'],0,'intval');
		$discountValue=checkVariable($options['discountValue'],0,'doubleval');
		$amount=checkVariable($options['amount'],0,'doubleval');
		$categoryModel = new CategoryModel();
		$categoryStatus=$categoryModel->getList(array('type'=>0,'categoryID'=>$categoryID));
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'serviceID'=>$serviceID,'testName'=>$testName,'categoryID'=>$categoryID,'isNotInID'=>1));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus>0)
		{
			$response=array('status'=>-2,'msg'=>$testName.' with  are already exists');
		}
		elseif($categoryID>0 && $categoryStatus<=0)
		{
			$response=array('status'=>-3,'msg'=>' invalid category');
		}
		elseif($discountValue>$amount)
		{
			$response=array('status'=>-4,'msg'=>' Commission Amount must be less than amount');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('service_list');
			$where=array('srNo'=>$serviceID,'isRemoved'=>$isRemoved);
			$data=array('testName'=>$testName,'categoryID'=>$categoryID,'amount'=>$amount,'discountValue'=>$discountValue,'updatedOn'=>$dateTime,'reference'=>$userID);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
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
		$serviceID=checkVariable($options['serviceID'],0,'intval');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'serviceID'=>$serviceID));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid service id');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('service_list');
			$where=array('srNo'=>$serviceID,'isRemoved'=>0);
			$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'isRemoved'=>1);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
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