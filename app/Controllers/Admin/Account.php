<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Models\CategoryModel;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
class Account extends BaseController
{
	public function __construct()
	{
	}
    public function index()
    {
		$data=array();
		$accountModel = new accountModel();
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Dashboard');	
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		return view($prePath.'account/dashboard',$data, ['saveData' => true]);
    }
	public function logout()
	{
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		login(array('sesName'=>$sesName,'category'=>-1,'type'=>$sesType));
		return redirect()->to(site_url('/'.$prePath.'login')); 
	}
	public function backupDownload()
	{
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		AdminConfig::set('title','Download Backup');
		$data['user']=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType)); 
		CommonMethods::backupDownload(0);
	}
	public function login()
	{
		$sesName=AdminConfig::get('sesName');
		$sesType=AdminConfig::get('sesType');
		$prePath=AdminConfig::get('prePath');
		$data=array();
		$sesType=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType,'isReturn'=>0));
		if($sesType>0)
		{
			return redirect()->to($prePath.'dashboard');
		}
		else
		{
		$data['title']='User Login';
		return view($prePath.'account/login',$data);
		}
	}
	public function validateInput($options=false)
	{
		$response=0;
		$type=(isEmptyArray($options)>0) ? checkVariable($options['type'],0,'intval') : 0;
		$validation =  \Config\Services::validation();
		if($type==1)
		{
		$rules =array(
        'username'    => array(
            'rules'  => 'required',#|valid_email
            'errors' => array(
                'valid_email' => 'Please check the Email field. It does not appear to be valid.',
            ),
        ),
		'password' => array(
            'rules'  => 'required',
            'errors' => array(
                'required' => 'Please check the password.It does not appear to be valid.',
            ),
        ),
		);
		}
		if (!$this->validate($rules)) 
		{	
		$response=$this->validator->getErrors();	
		}
		else
		{
		$response=1;
		}		
		return $response;
	}
	public function userValidate()
	{
		$validationStatus=$this->validateInput(array('type'=>1));
		$session = \Config\Services::session(); 
		if(isEmptyArray($validationStatus)<=0)
		{
			$sesName=AdminConfig::get('sesName');
			$sesType=AdminConfig::get('sesType');
			$sesLoginFor=AdminConfig::get('sesLoginFor');
		    $prePath=AdminConfig::get('prePath');
			$post=$this->request->getPostGet();	
			$post['loginFor']=$sesLoginFor;
			$post['userType']=$sesType;
			$post['sesName']=$sesName;
			//$accountModel = new \App\Models\Account();
			$accountModel = new accountModel();
			$result=$accountModel->validateUser($post);
			$status=(isEmptyArray($result)>0) ? checkVariable($result['status'],0,'intval') : 0;
			if($status==1)
			{
				$session = \Config\Services::session();
				$redirect=$prePath.'dashboard';
                if($session->has('prevURL'))
				{
					$redirect=$session->get('prevURL');
				}
				return redirect()->to($redirect);
			}
			else
			{
				//$session->setFlashdata('postError',$result);
				return redirect()->back()->withInput()->with('errors', $result);
			}
		}
		else
		{
			//$session->setFlashdata('postError',array('status'=>-11,'msg'=>$validationStatus));
			return redirect()->back()->withInput()->with('errors',array('status'=>-11,'msg'=>$validationStatus));
		}	
	}
}
