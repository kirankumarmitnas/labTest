<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\NotificationModel;
class Notification extends BaseController
{
	public function __construct()
	{
	}
    public function emailList()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Email List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>1,'mediaType'=>2,'baseUrl'=>$prePath.'notification/email/list'));
		return view($prePath.'notification/emailList',$data, ['saveData' => true]);
	}
	public function smsList()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','SMS List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>2,'mediaType'=>1,'baseUrl'=>$prePath.'notification/sms/list'));
		return view($prePath.'notification/smsList',$data, ['saveData' => true]);
	}
	public function loginLog()
	{
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Login Historys');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>3,'baseUrl'=>$prePath.'user/login/historys'));
		return view($prePath.'account/loginList',$data, ['saveData' => true]);
	}
	public function getPaginationConfig($options)
	{
		$response=array();
		$totalRecords=0;
		$limitPerPage=0;
		$prePath=AdminConfig::get('prePath');
		$baseUrl=$prePath.'notification/email/list';
		$type=1;
		if(isEmptyArray($options)>0)
		{
			$totalRecords=abs(checkVariable($options['totalRecords'],0,'intval'));
			$limitPerPage=abs(checkVariable($options['limitPerPage'],0,'intval'));
			$baseUrl=checkVariable($options['baseUrl'],$prePath.'notification/email/list','');
			$type=checkVariable($options['type'],0,'intval');
		}
		$urlStrings=array();
		if(in_array($type,array(1,2))==true)
		{
			$search=$this->request->getGet('search');
			$dateTime=$this->request->getGet('dateTime');
			$mediaStatus=$this->request->getGet('mediaStatus');
			if(!empty($dateTime))
			{
				$urlStrings[]='dateTime='.$dateTime;
			}
			if(!empty($mediaStatus))
			{
				$urlStrings[]='mediaStatus='.$mediaStatus;
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
		elseif($type==3)
		{
			$dateTime=$this->request->getGet('dateTime');
			$search=$this->request->getGet('search');
			if(!empty($search))
			{
				$urlStrings[]='search='.$search;
			}
			if(!empty($dateTime))
			{
				$urlStrings[]='dateTime='.$dateTime;
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
		$baseUrl=checkVariable($options['baseUrl'], $prePath.'notification/email/list','trim');
		$parameters=array();
		if(in_array($type,array(1,2))==true)
		{
			$search=$this->request->getGet('search');
			$dateTime=$this->request->getGet('dateTime');
			$mediaStatus=$this->request->getGet('mediaStatus');
			$parameters['mediaStatus']=$mediaStatus;
			$parameters['dateTime']=$dateTime;
			$parameters['search']=$search;
			$parameters['mediaType']=$mediaType;
		}
		elseif($type==3)
		{
			$search=$this->request->getGet('search');
			$dateTime=$this->request->getGet('dateTime');
			$parameters['dateTime']=$dateTime;
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
		$notificationModel = new notificationModel();
		if($type==3)
		{
		$result['totalRecords']=$totalRecords=$notificationModel->getLoginList($parameters);
		}
		else
		{
		$result['totalRecords']=$totalRecords=$notificationModel->getList($parameters);
		}
		if($totalRecords>0)
		{
		//$limitPerPage =$totalRecords;	
		$parameters['offset']=$dbOffset;
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$limitPerPage;
		$parameters['type']=1;
		if($type==3)
		{
		$result['loginList']=$notificationModel->getLoginList($parameters);
		}
		else
		{
		$result['mediaList']=$notificationModel->getList($parameters);	
		}
		$result['pagination']=$this->getPaginationConfig(array('totalRecords'=>$totalRecords,'limitPerPage'=>$limitPerPage,'baseUrl'=>$baseUrl,'type'=>$type));
		$result["listIndex"] = $offset;
		}
		return $result;
	}

}