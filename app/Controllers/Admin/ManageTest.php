<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\TestModel;
class ManageTest extends BaseController
{
	public function __construct()
	{
	}
	public function addView()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Add Test Details');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		return view($prePath.'test/add',$data, ['saveData' => true]);
	}
	public function updateView($testID=0)
    {
		$response=0;
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		if($testID>0)
		{
			$data=array();
			$testModel = new TestModel();
			$testDetails=$testModel->getTestDetails(array('type'=>1,'testID'=>$testID,'action'=>1));
			if(isEmptyArray($testDetails)>0)
			{
				$response=1;	
				$data['testDetails']=$testDetails;
				AdminConfig::set('title','Update Test Details');
				$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
				return view($prePath.'test/update',$data, ['saveData' => true]);
			}
		}
		if($response<=0)
		{
			$redirect=site_url($prePath.'/test/list');
			return redirect()->to($redirect);
		}
	}
	public function details()
	{
		$response=0;
		if ($this->request->isAJAX()) 
		{
			$validationStatus=$this->validateInput(array('type'=>3));
			$response=array();
			if(isEmptyArray($validationStatus)<=0)
			{
				$post=$this->request->getPostGet();	
				$testID=checkVariable($post['testID'],0,'intval');
				$isSevices=checkVariable($post['isSevices'],0,'intval');
				$testModel = new TestModel();
				$testDetails=$testModel->getTestDetails(array('type'=>1,'testID'=>$testID,'action'=>1));
				if(isEmptyArray($testDetails)>0)
				{
					if($isSevices==1)
					{
					    $servicesList=checkVariable($testDetails['testList'],0);
						$servicesListStatus=isEmptyArray($servicesList);
						if($servicesListStatus>0)
						{
							$servicesList=getArrayKeyValues(array('fields'=>array('testName','amount','discountValue'),'data'=>$servicesList));
							$servicesListStatus=isEmptyArray($servicesList);
							if($servicesListStatus>0)
							{
								$response=$servicesList;
							}
						}
					}
					else
					{
						$response=$testDetails;	
					}
				}
			}
		}
		return  json_encode($response,JSON_HEX_APOS);
	}
	public function addData()
	{
		$post=$this->request->getPostGet();
		$validationStatus=$this->validateInput(array('type'=>1));
		$session = \Config\Services::session(); 
		if(isEmptyArray($validationStatus)<=0)
		{
			$post=$this->request->getPostGet();
			$sesName=AdminConfig::get('sesName');
			$sesType=AdminConfig::get('sesType');
			$prePath=AdminConfig::get('prePath');
			$post['userType']=$sesType;
			$post['sesName']=$sesName;			
			$testModel= new TestModel();
			$result=$testModel->saveDetails($post);
			$status=(isEmptyArray($result)>0) ? checkVariable($result['status'],0,'intval') : 0;
			if($status==1)
			{
				$redirect=site_url($prePath.'/test/list');
				return redirect()->to($redirect);
			}
			else
			{
				return redirect()->back()->withInput()->with('errors', $result);
			}
		}
		else
		{
			return redirect()->back()->withInput()->with('errors',array('status'=>-11,'msg'=>$validationStatus));
		}
	}
	public function updateData($testID=0)
	{
		$response=0;
		$prePath=AdminConfig::get('prePath');
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$redirect=site_url($prePath.'/test/list');
		if($testID>0)
		{
			$testModel = new TestModel();
			$testStatus=$testModel->getTestDetails(array('type'=>1,'testID'=>$testID));
			if($testStatus>0)
			{
				$validationStatus=$this->validateInput(array('type'=>2));
				$response=array();
				if(isEmptyArray($validationStatus)<=0)
				{
					$post=$this->request->getPostGet();	
					$post['userType']=$sesType;
					$post['sesName']=$sesName;
					$post['testID']=$testID;
					$testModel = new TestModel();
					$result=$testModel->updateDetails($post);
					$status=(isEmptyArray($result)>0) ? checkVariable($result['status'],0,'intval') : 0;
					if($status==1)
					{
						return redirect()->to($redirect);
					}
					else
					{
						return redirect()->back()->withInput()->with('errors', $result);
					}
				}
				else
				{
					return redirect()->back()->withInput()->with('errors',array('status'=>-11,'msg'=>$validationStatus));
				}	
				
			}
			else
			{
				return redirect()->to($redirect);
			}
		}
		else
		{
			return redirect()->to($redirect);
		}
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
				$testModel = new TestModel();
				$response=$testModel->removeDetails($post);
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
			'doctor'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Doctor ID  are mandatory field',
				),
			),
			'patientName'    => array(
				'rules'  => 'required|trim|max_length[200]',#|valid_email
				'errors' => array(
					'max_length' => 'Patient Name are allow  maximum  200  charcters',
				),
			),
			'mobileNo' => array(
				'rules'  => 'required|trim|max_length[15]',
				'errors' => array(
					'max_length' => 'Mobile No maximum 15 digits long',
				),
			),
			'age'    => array(
				'rules'  => 'required|max_length[3]|less_than_equal_to[100]|greater_than_equal_to[1]',#|valid_email
				'errors' => array(
					'max_length' => 'age are between 0 to 100',
					'less_than_equal_to' => 'age are between 0 to 100',
					'greater_than_equal_to' => 'age are between 0 to 100',
				),
			),
			'gender'    => array(
				'rules'  => 'required|integer|in_list[0,1,2,3]',#|valid_email
				'errors' => array(
					'in_list' => 'Gender is male,female or other ',
				),
			),
			'services'    => array(
				'rules'  => 'required',#|valid_email
				'errors' => array(
					'required' => 'Services are mandatory Fields ',
				),
			),
			'testDate'    => array(
				'rules'  => 'required|trim|valid_date[d-m-Y]',#|valid_email
				'errors' => array(
					'valid_date' => 'invalid Date format ',
				),
			),
			
			'testID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Test ID  are mandatory field',
				),
			),
			);
		}
		elseif($type==3)
		{
			$rules =array(
			'testID'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Test ID  are mandatory field',
				),
			),
			);
		}
		else
		{
			$rules =array(
			'doctor'    => array(
				'rules'  => 'required|integer',#|valid_email
				'errors' => array(
					'required' => 'Doctor ID  are mandatory field',
				),
			),
			'patientName'    => array(
				'rules'  => 'required|trim|max_length[200]',#|valid_email
				'errors' => array(
					'max_length' => 'Patient Name are allow  maximum  200  charcters',
				),
			),
			'mobileNo' => array(
				'rules'  => 'required|trim|max_length[15]',
				'errors' => array(
					'max_length' => 'Mobile No maximum 15 digits long',
				),
			),
			'age'    => array(
				'rules'  => 'required|max_length[3]',#|valid_email
				'errors' => array(
					'max_length' => 'age are between 0 to 100',
				),
			),
			'gender'    => array(
				'rules'  => 'required|integer|in_list[0,1,2,3]',#|valid_email
				'errors' => array(
					'max_length' => 'Gender is male,female or other ',
				),
			),
			
			'testDate'    => array(
				'rules'  => 'required|trim|valid_date[d-m-Y]',#|valid_email
				'errors' => array(
					'valid_date' => 'invalid Date format ',
				),
			),
			'services'    => array(
				'rules'  => 'required',#|valid_email
				'errors' => array(
					'max_length' => 'Gender is male,female or other ',
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
}