<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\ServiceModel;
class Service extends BaseController
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
		AdminConfig::set('title','Services List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>1,'mediaType'=>2,'baseUrl'=>$prePath.'service/list'));
		return view($prePath.'service/listDetails',$data, ['saveData' => true]);
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
				$serviceModel = new ServiceModel();
				$response=$serviceModel->saveDetails($post);
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
				$serviceModel = new ServiceModel();
				$response=$serviceModel->updateDetails($post);
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
			$validationStatus=$this->validateInput(array('type'=>3));
			$response=array();
			if(isEmptyArray($validationStatus)<=0)
			{
				$post=$this->request->getPostGet();	
				$sesName=AdminConfig::get('sesName');
				$sesType=AdminConfig::get('sesType');
				$post['userType']=$sesType;
				$post['sesName']=$sesName;
				$serviceModel = new ServiceModel();
				$response=$serviceModel->removeDetails($post);
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
			'testName'    => array(
				'rules'  => 'required|max_length[150]',#|valid_email
				'errors' => array(
					'max_length' => 'Test name are allow  maximum  200  charcters',
				),
			),
			'categoryName'    => array(
				'rules'  => 'required|integer|greater_than_equal_to[0]',#|valid_email
				'errors' => array(
					'greater_than_equal_to' => 'invalid category name ',
				),
			),
			'discountValue'    => array(
				'rules'  => 'required|greater_than[0]',#|valid_email
				'errors' => array(
					'greater_than' => 'Commission  Amount must greater than 0',
				),
			),
			'amount'    => array(
				'rules'  => 'required|greater_than[0]',#|valid_email
				'errors' => array(
					'greater_than' => 'Amount must greater than 0',
				),
			),
			'serviceID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Service ID  are mandatory field',
				),
			),
			);
		}
		elseif($type==3)
		{
			$rules =array(
			'serviceID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Service ID  are mandatory field',
				),
			),
			);
		}
		else
		{
			$rules =array(
			'testName'    => array(
				'rules'  => 'required|max_length[150]',#|valid_email
				'errors' => array(
					'max_length' => 'Test name are allow  maximum  200  charcters',
				),
			),
			'categoryName'    => array(
				'rules'  => 'required|integer|greater_than_equal_to[0]',#|valid_email
				'errors' => array(
					'greater_than_equal_to' => 'invalid category name ',
				),
			),
			'discountValue'    => array(
				'rules'  => 'required|integer|greater_than[0]',#|valid_email
				'errors' => array(
					'greater_than' => 'Commission Amount must greater than 0',
				),
			),
			'amount'    => array(
				'rules'  => 'required|integer|greater_than[0]',#|valid_email
				'errors' => array(
					'greater_than' => 'Amount must greater than 0',
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
		$baseUrl=$prePath.'service/list';
		$type=1;
		if(isEmptyArray($options)>0)
		{
			$totalRecords=abs(checkVariable($options['totalRecords'],0,'intval'));
			$limitPerPage=abs(checkVariable($options['limitPerPage'],0,'intval'));
			$baseUrl=checkVariable($options['baseUrl'],$prePath.'service/list','');
			$type=checkVariable($options['type'],0,'intval');
		}
		$urlStrings=array();
		if($type==1)
		{
			$category=$this->request->getGet('category');
			$search=$this->request->getGet('search');
			if(!empty($search))
			{
				$urlStrings[]='search='.$search;
			}
			if(intval($category)>0)
			{
				$urlStrings[]='category='.$category;
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
		$mediaType=checkVariable($options['mediaType'],0,'intval');
		$baseUrl=checkVariable($options['baseUrl'], $prePath.'service/list','trim');
		$parameters=array();
		if($type==1)
		{
			$search=$this->request->getGet('search');
			$category=$this->request->getGet('category');
			$parameters['categoryID']=$category;
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
		$serviceModel = new ServiceModel();
		$result['totalRecords']=$totalRecords=$serviceModel->getList($parameters);
		if($totalRecords>0)
		{
		//$limitPerPage =$totalRecords;	
		$parameters['offset']=$dbOffset;
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$limitPerPage;
		$parameters['type']=1;
		$result['serviceList']=$serviceModel->getList($parameters);
		$result['pagination']=$this->getPaginationConfig(array('totalRecords'=>$totalRecords,'limitPerPage'=>$limitPerPage,'baseUrl'=>$baseUrl,'type'=>$type));
		$result["listIndex"] = $offset;
		}
		return $result;
	}

}