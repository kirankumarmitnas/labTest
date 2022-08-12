<?php 
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\AccountModel;
use App\Libraries\AdminConfig;
helper('discover');
class AuthGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
       if(url_is('admin/*') && !url_is('admin/login')) 
	   { 
			$request = \Config\Services::request();
			$session = \Config\Services::session();
			$currentRoute=uri_string();
			$requestMethod =$request->getMethod();
			if ($request->isAJAX()) {
				$session->remove('prevURL');
			}
			elseif(stripos($currentRoute,'logout')===false && stripos($requestMethod,'get')!==false)
			{
				//$router = \CodeIgniter\Config\Services::router();
				//$currentRoute = $router->getMatchedRouteOptions()['as'];
				$session->set('prevURL',$currentRoute);
			}
			
			$accountModel = new accountModel();
			//echo AdminConfig::get('sesType');
			$data['sessionDetails']=array('sesName'=>AdminConfig::$sesName,'sesType'=>AdminConfig::$sesType,'panelType'=>AdminConfig::$panelType,'commonPath'=>AdminConfig::$commonPath,'prePath'=>AdminConfig::$prePath);
			$sesType=login(array('sesName'=>AdminConfig::$sesName,'category'=>2,'type'=>AdminConfig::$sesType,'isReturn'=>0));
			if($sesType<=0)
			{
				return redirect()->to(site_url('/'.AdminConfig::$prePath.'login')); 
			}
			else
			{
				//AdminConfig::set('sesType',$sesType);	
				AdminConfig::$sesType=$sesType;
				$userData=$accountModel->isLoggedIn(array('panelType'=>AdminConfig::$panelType,'sesType'=>AdminConfig::$sesType,'sesName'=>AdminConfig::$sesName));
				$userStatus=(isEmptyArray($userData)>0) ? checkVariable($userData['status'],0,'intval') : 0;
				if($userStatus!=1)
				{
					$redirect=(isEmptyArray($userData)>0) ? checkVariable($userData['redirect'],'') : '';
					if(empty($redirect))
					{
						$redirect=site_url('/'.AdminConfig::$prePath.'login');
					}
					return redirect()->to($redirect); 
				}
				else
				{
					$permissionsInfo=$accountModel->isLoggedIn(array('panelType'=>AdminConfig::$panelType,'sesType'=>AdminConfig::$sesType,'sesName'=>AdminConfig::$sesName,'isPermission'=>1));
					if(isEmptyArray($permissionsInfo)>0)
					{
					AdminConfig::$permissions=checkVariable($permissionsInfo['permissions'],0);
					}
					$accountModel->updateUserTime(array('sesName'=>AdminConfig::$sesName,'sesType'=>AdminConfig::$sesType));
				}
			}
	   }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
		
    }
}