<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\ReportModel;
use App\Libraries\ExportFile;
class Report extends BaseController
{
	public function __construct()
	{
	}
    public function doctorDetails()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Doctorwise Report');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>1,'baseUrl'=>$prePath.'report/doctor'));
		return view($prePath.'report/doctor',$data, ['saveData' => true]);
	} 
	public function monthlyDetails()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Monthly Report');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>2,'baseUrl'=>$prePath.'report/monthly'));
		return view($prePath.'report/monthly',$data, ['saveData' => true]);
	} 
	public function serviceDetails()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Servicewise List');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$data['result']=$this->getData(array('type'=>3,'baseUrl'=>$prePath.'report/service'));
		return view($prePath.'report/service',$data, ['saveData' => true]);
	}
	public function exportFile()
	{
		$search=$this->request->getGet('search');
		$processType=intval($this->request->getGet('processType'));
		$exportType=intval($this->request->getGet('exportType'));
		if(in_array($exportType,array(1,2))==true  && in_array($processType,array(1,2,3))==true)
		{
			if($exportType==2)
			{
				ExportFile::testReportPdf(0);
			}
			else
			{
				ExportFile::testReportExcel(0);
			}
		}
	}
	public function getPaginationConfig($options)
	{
		$response=array();
		$totalRecords=0;
		$limitPerPage=0;
		$prePath=AdminConfig::get('prePath');
		$baseUrl=$prePath.'report/doctor';
		$type=1;
		if(isEmptyArray($options)>0)
		{
			$totalRecords=abs(checkVariable($options['totalRecords'],0,'intval'));
			$limitPerPage=abs(checkVariable($options['limitPerPage'],0,'intval'));
			$baseUrl=checkVariable($options['baseUrl'],$prePath.'report/doctor','');
			$type=checkVariable($options['type'],0,'intval');
		}
		$urlStrings=array();
		if(in_array($type,array(2,3))==true)
		{
			$search=$this->request->getGet('search');
			$year=intval($this->request->getGet('year'));
			$month=intval($this->request->getGet('month'));
			if($year>0)
			{
				$urlStrings[]='year='.$year;
			}
			if($month>0)
			{
				$urlStrings[]='month='.$month;
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
		else
		{
			$search=$this->request->getGet('search');
			$doctor=intval($this->request->getGet('doctor'));
			$year=intval($this->request->getGet('year'));
			$month=intval($this->request->getGet('month'));
			if($year>0)
			{
				$urlStrings[]='year='.$year;
			}
			if($month>0)
			{
				$urlStrings[]='month='.$month;
			}
			if(!empty($search))
			{
				$urlStrings[]='search='.$search;
			}
			if($doctor>0)
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
		if(in_array($type,array(2,3))==true)
		{
			$search=$this->request->getGet('search');
			$year=intval($this->request->getGet('year'));
			$month=intval($this->request->getGet('month'));
			if($year>0)
			{
				$parameters['year']=$year;
			}
			if($month>0)
			{
				$parameters['month']=$month;
			}
			$parameters['processType']=$type;
			$parameters['search']=$search;
		}
		else
		{
			$search=$this->request->getGet('search');
			$doctor=intval($this->request->getGet('doctor'));
			$year=intval($this->request->getGet('year'));
			$month=intval($this->request->getGet('month'));
			if($doctor>0)
			{
				$parameters['doctorID']=$doctor;
			}
			if($year>0)
			{
				$parameters['year']=$year;
			}
			if($month>0)
			{
				$parameters['month']=$month;
			}
			$parameters['search']=$search;
		}
		$parameters['type']=0;
		$limitPerPage = 50;
		$offset=($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 0;
		$offset=abs(ceil(intval($offset)));
		$dbOffset=0;
		$totalRecords=0;
		$reportModel = new ReportModel();
		$result['totalRecords']=$totalRecords=$reportModel->getList($parameters);
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
		$result['reportList']=$reportModel->getList($parameters);	
		$result['pagination']=$this->getPaginationConfig(array('totalRecords'=>$totalRecords,'limitPerPage'=>$limitPerPage,'baseUrl'=>$baseUrl,'type'=>$type));
		$result["listIndex"] = $offset;
		}
		return $result;
	}

}