<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\DoctorModel;
class Doctor extends BaseController
{
	public function __construct()
	{
	}
    public function listDetails()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Doctor List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>1,'mediaType'=>2,'baseUrl'=>$prePath.'doctor/list'));
		return view($prePath.'doctor/listDetails',$data, ['saveData' => true]);
	}
	public function add()
	{
		$response=0;
		//$request = \Config\Services::request();
		if ($this->request->isAJAX()) 
		{
			$validationStatus=$this->validateInput(array('type'=>1));
			$response=array();
			if(isEmptyArray($validationStatus)<=0)
			{
				$post=$this->request->getPostGet();	
				$sesName=AdminConfig::get('sesName');
				$sesType=AdminConfig::get('sesType');
				$post['userType']=$sesType;
				$post['sesName']=$sesName;
				$doctorModel = new DoctorModel();
				$response=$doctorModel->saveDetails($post);
			}
			else
			{
				$response=array('status'=>-11,'msg'=>$validationStatus);
			}
		}
		//return $this->response->setJSON($response);
		return  json_encode($response,JSON_HEX_APOS);
	}
	public function update()
	{
		$response=0;
		//$request = \Config\Services::request();
		if ($this->request->isAJAX()) 
		{
			$validationStatus=$this->validateInput(array('type'=>2));
			$response=array();
			if(isEmptyArray($validationStatus)<=0)
			{
				$post=$this->request->getPostGet();	
				$sesName=AdminConfig::get('sesName');
				$sesType=AdminConfig::get('sesType');
				$post['userType']=$sesType;
				$post['sesName']=$sesName;
				$doctorModel = new DoctorModel();
				$response=$doctorModel->updateDetails($post);
			}
			else
			{
				$response=array('status'=>-11,'msg'=>$validationStatus);
			}
		}
		return  json_encode($response,JSON_HEX_APOS);
	}
	public function status()
	{
		$response=0;
		//$request = \Config\Services::request();
		if ($this->request->isAJAX()) 
		{
			$validationStatus=$this->validateInput(array('type'=>3));
			$response=array();
			if(isEmptyArray($validationStatus)<=0)
			{
				$post=$this->request->getPostGet();	
				$sesName=AdminConfig::get('sesName');
				$sesType=AdminConfig::get('sesType');
				$post['userType']=$sesType;
				$post['sesName']=$sesName;
				$doctorModel = new DoctorModel();
				$response=$doctorModel->statusDetails($post);
			}
			else
			{
				$response=array('status'=>-11,'msg'=>$validationStatus);
			}
		}
		return  json_encode($response,JSON_HEX_APOS);
	}
	public function remove()
	{
		$response=0;
		//$request = \Config\Services::request();
		if ($this->request->isAJAX()) 
		{
			$validationStatus=$this->validateInput(array('type'=>4));
			$response=array();
			if(isEmptyArray($validationStatus)<=0)
			{
				$post=$this->request->getPostGet();	
				$sesName=AdminConfig::get('sesName');
				$sesType=AdminConfig::get('sesType');
				$post['userType']=$sesType;
				$post['sesName']=$sesName;
				$doctorModel = new DoctorModel();
				$response=$doctorModel->removeDetails($post);
			}
			else
			{
				$response=array('status'=>-11,'msg'=>$validationStatus);
			}
		}
		return  json_encode($response,JSON_HEX_APOS);
	}
	public function validateInput($options=false)
	{
		$response=0;
		$type=(isEmptyArray($options)>0) ? checkVariable($options['type'],0,'intval') : 0;
		$validation =  \Config\Services::validation();
		if($type==2)
		{
			$rules =array(
			'doctorName'    => array(
				'rules'  => 'required|max_length[200]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			'designation'    => array(
				'rules'  => 'required|max_length[50]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			'hospitalName'    => array(
				'rules'  => 'required|max_length[100]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			'mobileNo' => array(
				'rules'  => 'trim|max_length[15]',
				'errors' => array(
					'max_length' => 'Mobile No maximum 15 digits long',
				),
			),
			'doctorID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Doctor ID  are mandatory field',
				),
			),
			);
		}
		elseif($type==3)
		{
			$rules =array(
			'doctorID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Doctor ID  are mandatory field',
				),
			),
			'doctorStatus'    => array(
				'rules'  => 'required|integer|in_list[1,0]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			);
		}
		
		elseif($type==4)
		{
			$rules =array(
			'doctorID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Doctor ID  are mandatory field',
				),
			),
			);
		}
		elseif($type==5)
		{
			$rules =array(
			'search'    => array(
				'rules'  => 'required|max_length[50]',#|valid_email
				'errors' => array(
					'required' => 'search text are mandatory field',
				),
			),
			);
		}
		else
		{
			$rules =array(
			'doctorName'    => array(
				'rules'  => 'required|max_length[200]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			'designation'    => array(
				'rules'  => 'required|max_length[50]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			'hospitalName'    => array(
				'rules'  => 'required|max_length[100]',#|valid_email
				'errors' => array(
					'max_length' => 'Name are allow  maximum  200  charcters',
				),
			),
			'mobileNo' => array(
				'rules'  => 'trim|max_length[15]',
				'errors' => array(
					'max_length' => 'Mobile No maximum 15 digits long',
				),
			),
			);
		}
		if (!$this->validate($rules)) 
		{
			//$response=$validation->getErrors();	
			$response=$this->validator->getErrors();	
		}
		else
		{
			$response=1;
		}		
		return $response;
	}
	public function getPaginationConfig($options)
	{
		$response=array();
		$totalRecords=0;
		$limitPerPage=0;
		$prePath=AdminConfig::get('prePath');
		$baseUrl=$prePath.'doctor/list';
		$type=1;
		if(isEmptyArray($options)>0)
		{
			$totalRecords=abs(checkVariable($options['totalRecords'],0,'intval'));
			$limitPerPage=abs(checkVariable($options['limitPerPage'],0,'intval'));
			$baseUrl=checkVariable($options['baseUrl'],$prePath.'doctor/list','');
			$type=checkVariable($options['type'],0,'intval');
		}
		$urlStrings=array();
		if($type==3)
		{
			$search=$this->request->getGet('search');
			$doctorStatus=$this->request->getGet('doctorStatus');
			if(!empty($doctorStatus))
			{
				$urlStrings[]='doctorStatus='.$doctorStatus;
			}
			if(!empty($search))
			{
				$urlStrings[]='search='.$search;
			}
			if(count($urlStrings)>0)
			{
				$baseUrl.='?'.join('&',$urlStrings);
			}
		}
		//helper(['template']);
		$pager=service('pager');
		$response['pager']=$pager;
		//$pager = \Config\Services::pager(); 
		$pager->setPath('index.php/'.$baseUrl);
		$offset=($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 0;
		$offset=abs(ceil(intval($offset)));
		$offset=($offset<=1) ? 1 : $offset;
		$response['pagerHTML']=$pager->makeLinks($offset, $limitPerPage, $totalRecords, 'main_pagger');
		return $response;
	}
	public function getData($options)
	{
		$result=array();
		$prePath=AdminConfig::get('prePath');
		$type=checkVariable($options['type'],0,'intval');
		$baseUrl=checkVariable($options['baseUrl'], $prePath.'doctor/list','trim');
		$parameters=array();
		if($type==1)
		{
			$search=$this->request->getGet('search');
			$doctorStatus=$this->request->getGet('doctorStatus');
			$parameters['doctorStatus']=$doctorStatus;
			$parameters['search']=$search;
		}
		$parameters['type']=0;
		$limitPerPage = 50;
		$offset=($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 0;
		$offset=abs(ceil(intval($offset)));
		$dbOffset=0;
		if($offset<=1)
		{
			$offset=0;
			$dbOffset=0;
		}
		else
		{
		$dbOffset=$limitPerPage*($offset-1);	
		$offset=$limitPerPage+($offset-1);
		}
		$totalRecords=0;
		$doctorModel = new DoctorModel();
		$result['totalRecords']=$totalRecords=$doctorModel->getList($parameters);
		if($totalRecords>0)
		{
		//$limitPerPage =$totalRecords;	
		$parameters['offset']=$dbOffset;
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$limitPerPage;
		$parameters['type']=1;
		$result['doctorList']=$doctorModel->getList($parameters);
		$result['pagination']=$this->getPaginationConfig(array('totalRecords'=>$totalRecords,'limitPerPage'=>$limitPerPage,'baseUrl'=>$baseUrl,'type'=>$type));
		$result["listIndex"] = $offset;
		}
		return $result;
	}

}