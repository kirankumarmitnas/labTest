<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
//use CodeIgniter\Database\Query;
class CategoryModel extends Model
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
		$orderBy=checkVariable($options['orderBy'],' order by name ASC ');
		$fetchField=checkVariable($options['fetchField'],' * ');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$categoryName=checkVariable($options['categoryName'],'','trim');
		$categoryID=checkVariable($options['categoryID'],0,'intval');
		$isNotInID=checkVariable($options['isNotInID'],0,'intval');
		$cStatus=checkVariable($options['cStatus'],-1,'intval');
		$isRemoved=0;
		$query=array();
		$queryStr='';
		$parameters=array();
		if($type==1)
		{
			if(empty($fetchField))
			{
			 $fetchField=" srNo,name,createdOn,updatedOn,( select count(srNo) from service_list where isRemoved=1 ) totalServices ";
			}
		}
		else
		{
			$fetchField=" count(srNo) as found ";
		}
		array_push($query,' isRemoved=? ');
		array_push($parameters,$isRemoved);	
		if($cStatus!=-1)
		{
		array_push($query,' cStatus=? ');
		array_push($parameters,$cStatus);
		}
		if($categoryID>0)
		{
			if($isNotInID==1)
			{
			array_push($query,' srNo!=? ');
			}
			else
			{
			array_push($query,' srNo=? ');	
			}
			array_push($parameters,$categoryID);
		}
		if(!empty($search))
		{
			array_push($query," name like ?  ");
			$search='%'.$search.'%';
			array_push($parameters,$search);
		}
		if(!empty($categoryName))
		{
			array_push($query,' name=? ');
			array_push($parameters,$categoryName);
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
				$queryText="SELECT  ".$fetchField." from service_category  ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from service_category   ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from service_category  ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from service_category   ";
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
		$categoryName=checkVariable($options['categoryName'],0,'trim');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'categoryName'=>$categoryName,'cStatus'=>-1));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus>0)
		{
			$response=array('status'=>-2,'msg'=>$categoryName.' with  are already exists');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$cStatus=1;
			$this->db->transStart();
			$builder = $this->db->table('service_category');
			$data=array('name'=>$categoryName,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'reference'=>$userID,'cStatus'=>1);
			$builder->set($data);
			$builder->insert();
			$categoryID=$this->db->insertID();
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'id'=>$categoryID,'msg'=>'category details are saved successfully');
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
		$categoryName=checkVariable($options['categoryName'],0,'trim');
		$categoryID=checkVariable($options['categoryID'],0,'trim');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'categoryName'=>$categoryName,'categoryID'=>$categoryID,'isNotInID'=>1));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus>0)
		{
			$response=array('status'=>-2,'msg'=>$categoryName.' with  are already exists');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('service_category');
			$where=array('srNo'=>$categoryID,'isRemoved'=>$isRemoved);
			$data=array('name'=>$categoryName,'updatedOn'=>$dateTime,'reference'=>$userID);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'category details are updated successfully');
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
		$categoryID=checkVariable($options['categoryID'],'','trim');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'categoryID'=>$categoryID));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid category id');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('service_category');
			$where=array('srNo'=>$categoryID,'isRemoved'=>0);
			$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'isRemoved'=>1);
			$builder->where($where);
			$builder->limit(1);
			$builder->update($data);
			$total=$this->db->affectedRows();
			if($total>0 && $categoryID>0)
			{
				$builder = $this->db->table('service_list');
				$where=array('categoryID'=>$categoryID,'isRemoved'=>0);
				$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'categoryID'=>0);
				$builder->where($where);
				$builder->update($data);
			}
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'category details are removed successfully');
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
		$categoryID=checkVariable($options['categoryID'],'','trim');
		$status=checkVariable($options['status'],0,'intval');
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
		$existsStatus=$this->getList(array('type'=>0,'categoryID'=>$categoryID));
		if(empty($userID))
		{
			$response=array('status'=>-1,'msg'=>'invalid user id');
		}
		elseif($existsStatus<=0)
		{
			$response=array('status'=>-2,'msg'=>'invalid category ID');
		}
		else
		{
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			$isRemoved=0;
			$this->db->transStart();
			$builder = $this->db->table('service_category');
			$where=array('srNo'=>$categoryID,'isRemoved'=>$isRemoved);
			$builder->where($where);
			$builder->limit(1);
			$data=array('updatedOn'=>$dateTime,'reference'=>$userID,'cStatus'=>$status);
			$builder->update($data);
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=array('status'=>1,'msg'=>'Category status are changed successfully');
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