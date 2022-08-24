<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\PaymentModel;
use App\Libraries\ExportFile;
class Payment extends BaseController
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
		AdminConfig::set('title','Payment List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>1,'baseUrl'=>$prePath.'payment/list'));
		return view($prePath.'payment/listDetails',$data, ['saveData' => true]);
	} 
	public function exportFile()
	{
		$search=$this->request->getGet('search');
		$exportType=intval($this->request->getGet('exportType'));
		if(in_array($exportType,array(1,2))==true )
		{
			if($exportType==2)
			{
				ExportFile::paymentListPdf(0);
			}
			else
			{
				ExportFile::paymentListExcel(0);
			}
		}
	}
	public function getPaginationConfig($options)
	{
		$response=array();
		$totalRecords=0;
		$limitPerPage=0;
		$prePath=AdminConfig::get('prePath');
		$baseUrl=$prePath.'payment/list';
		$type=1;
		if(isEmptyArray($options)>0)
		{
			$totalRecords=abs(checkVariable($options['totalRecords'],0,'intval'));
			$limitPerPage=abs(checkVariable($options['limitPerPage'],0,'intval'));
			$baseUrl=checkVariable($options['baseUrl'],$prePath.'payment/list','');
			$type=checkVariable($options['type'],0,'intval');
		}
		$urlStrings=array();
		if($type==1)
		{
			$search=$this->request->getGet('search');
			$year=intval($this->request->getGet('year'));
			$month=intval($this->request->getGet('month'));
			$fromDate=$this->request->getGet('fromDate');
			$toDate=$this->request->getGet('toDate');
			$doctor=intval($this->request->getGet('doctor'));
			$sortBy=intval($this->request->getGet('sortBy'));
			if(in_array($sortBy,array(1,2))==true)
			{
				if($sortBy==1)
				{
					if($year>0)
					{
						$urlStrings[]='year='.$year;
					}
					if($month>0)
					{
						$urlStrings[]='month='.$month;
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
			if($doctor>0)
			{
				$urlStrings[]='doctor='.$doctor;
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
		$baseUrl=checkVariable($options['baseUrl'], $prePath.'test/list','trim');
		$parameters=array();
		if($type==1)
		{
			$search=$this->request->getGet('search');
			$fromDate=$this->request->getGet('fromDate');
			$toDate=$this->request->getGet('toDate');
			$sortBy=intval($this->request->getGet('sortBy'));
			$year=intval($this->request->getGet('year'));
			$month=intval($this->request->getGet('month'));
			$doctor=intval($this->request->getGet('doctor'));
			if(in_array($sortBy,array(1,2))==true)
			{
				if($sortBy==1)
				{
					if($year>0)
					{
						$parameters['year']=$year;
					}
					if($month>0)
					{
						$parameters['month']=$month;
					}
				}
				else
				{
					if(!empty($fromDate))
					{
						$parameters['fromDate']=$fromDate;
					}
					if(!empty($toDate))
					{
						$parameters['toDate']=$toDate;
					}
				}
			}
			$parameters['doctorID']=$doctor;
			$parameters['search']=$search;
		}
		$parameters['type']=0;
		$limitPerPage = 50;
		$offset=($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 0;
		$offset=abs(ceil(intval($offset)));
		$dbOffset=0;
		$totalRecords=0;
		$paymentModel = new PaymentModel();
		$result['totalRecords']=$totalRecords=$paymentModel->getList($parameters);
		if($totalRecords>0)
		{
		$limitPerPage =$totalRecords;	
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
		$parameters['offset']=$dbOffset;
		$parameters['action']=1;
		$parameters['isMultiple']=1;
		$parameters['limit']=$limitPerPage;
		$parameters['type']=1;
		$result['paymentList']=$paymentModel->getList($parameters);	
		$result['pagination']=$this->getPaginationConfig(array('totalRecords'=>$totalRecords,'limitPerPage'=>$limitPerPage,'baseUrl'=>$baseUrl,'type'=>$type));
		$result["listIndex"] = $offset;
		}
		return $result;
	}

}