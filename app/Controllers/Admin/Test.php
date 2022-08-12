<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\TestModel;
class Test extends BaseController
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
		AdminConfig::set('title','Test List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>1,'mediaType'=>2,'baseUrl'=>$prePath.'test/list'));
		return view($prePath.'test/listDetails',$data, ['saveData' => true]);
	}
	public function getPaginationConfig($options)
	{
		$response=array();
		$totalRecords=0;
		$limitPerPage=0;
		$prePath=AdminConfig::get('prePath');
		$baseUrl=$prePath.'test/list';
		$type=1;
		if(isEmptyArray($options)>0)
		{
			$totalRecords=abs(checkVariable($options['totalRecords'],0,'intval'));
			$limitPerPage=abs(checkVariable($options['limitPerPage'],0,'intval'));
			$baseUrl=checkVariable($options['baseUrl'],$prePath.'test/list','');
			$type=checkVariable($options['type'],0,'intval');
		}
		$urlStrings=array();
		if($type==3)
		{
			$search=$this->request->getGet('search');
			$fromDate=$this->request->getGet('fromDate');
			$toDate=$this->request->getGet('toDate');
			$doctor=$this->request->getGet('doctor');
			$year=intval($this->request->getGet('year'));
			$sortBy=intval($this->request->getGet('sortBy'));
			if(in_array($sortBy,array(1,2))==true)
			{
				if($sortBy==1 && $year>0)
				{
					if(!empty($year))
					{
						$urlStrings[]='year='.$year;
					}
				}
				else
				{
					if(!empty($fromDate))
					{
						$urlStrings[]='fromDate='.$fromDate;
					}
					if(!empty($toDate))
					{
						$urlStrings[]='toDate='.$toDate;
					}
				}
			}
			if(!empty($search))
			{
				$urlStrings[]='search='.$search;
			}
			if(intval($doctor)>0)
			{
				$urlStrings[]='doctor='.$doctor;
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
		$baseUrl=checkVariable($options['baseUrl'], $prePath.'test/list','trim');
		$parameters=array();
		if($type==1)
		{
			$search=$this->request->getGet('search');
			$fromDate=$this->request->getGet('fromDate');
			$toDate=$this->request->getGet('toDate');
			$year=intval($this->request->getGet('year'));
			$doctor=intval($this->request->getGet('doctor'));
			$sortBy=intval($this->request->getGet('sortBy'));
			if(in_array($sortBy,array(1,2))==true)
			{
				if($sortBy==1 && $year>0)
				{
					$parameters['year']=$year;
				}
				else
				{
					$parameters['fromDate']=$fromDate;
					$parameters['toDate']=$toDate;
				}
			}
			if($doctor>0)
			{
				$parameters['doctorID']=$doctor;
			}
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
		$testModel = new TestModel();
		$result['totalRecords']=$totalRecords=$testModel->getList($parameters);
		if($totalRecords>0)
		{
		//$limitPerPage =$totalRecords;	
		$parameters['offset']=$dbOffset;
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$limitPerPage;
		$parameters['type']=1;
		$result['testList']=$testModel->getList($parameters);
		$result['pagination']=$this->getPaginationConfig(array('totalRecords'=>$totalRecords,'limitPerPage'=>$limitPerPage,'baseUrl'=>$baseUrl,'type'=>$type));
		$result["listIndex"] = $offset;
		}
		return $result;
	}

}