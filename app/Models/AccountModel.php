<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
class AccountModel extends Model
{
	public function initializeUserPermissions($options)
	{
		$response=0;
		$userType=checkVariable($options['userType'],0,'intval');
		$userPermission=\App\Libraries\CommonMethods::getUserPermission(0);
		$isRemoved=0;
		$proccesdMenus=array();
		if(isEmptyArray($userPermission)>0)
		{
			$previousMenus=$this->getUrlExtraPermisions(array('userType'=>$userType,'isMultiple'=>1));
			$previousMenusStatus=isEmptyArray($previousMenus);
			$permissionInfo=searchValueInArray(array('type'=>1,'data'=>$userPermission,'search'=>array('userType'=>$userType)));
			$timing = new \Config\Timing();
			$dateTime = $timing->dateTime;
			if(isEmptyArray($permissionInfo)>0)
			{
				$this->db->transStart();
				$commonPermissionInfo=searchValueInArray(array('type'=>1,'data'=>$permissionInfo,'search'=>array('catelog'=>1),'isSingle'=>1));
				$processPermissionInfo=searchValueInArray(array('type'=>1,'data'=>$permissionInfo,'search'=>array('catelog'=>2)));
				if(isEmptyArray($commonPermissionInfo)>0)
				{
					$urlTypes=checkVariable($commonPermissionInfo['urlType'],0);
					if(isEmptyArray($urlTypes)>0)
					{
						$menuList=$this->getMenu(array('isVisible'=>-1,'panelType'=>1));
						if(isEmptyArray($menuList)>0)
						{
							$menuList=getArrayKeyValues(array('data'=>$menuList,'fields'=>array('srNo','parentID','children','urlType')));
							foreach($menuList as $menu)
							{
								$srNo=checkVariable($menu['srNo'],0,'intval');
								$parentID=checkVariable($menu['parentID'],0,'intval');
								$children=checkVariable($menu['children'],0);
								$parentUrlType=checkVariable($menu['urlType'],0,'intval');
								if(isEmptyArray($children)>0)
								{
								$children=getArrayKeyValues(array('data'=>$children,'fields'=>array('srNo','urlType')));
								}
								if($srNo>0)
								{
									$foundPermissionInfo=searchValueInArray(array('type'=>1,'data'=>$processPermissionInfo,'search'=>array('id'=>$srNo),'isSingle'=>1));
									if(isEmptyArray($foundPermissionInfo)>0)
									{
										$process=checkVariable($foundPermissionInfo['process'],0,'intval');
										$child=checkVariable($foundPermissionInfo['child'],0);
										if($process==1)
										{
											$existsStatus=0;
											//$proccesdMenus[]=$srNo;
											//array_splice($a1, -1, 0, $child);
											if($previousMenusStatus>0)
											{
												$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$srNo)));
												if(isEmptyArray($foundExistsData)>0)
												{
													$existsStatus=1;
												}
											}
											if($existsStatus==0)
											{
												$builder = $this->db->table('extra_menu_permission');
												$data=array('userType'=>$userType,'menuID'=>$srNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
												$builder->set($data);
												$builder->insert();
												
											}
											if(isEmptyArray($children)>0)
											{
												if(isEmptyArray($child)>0)
												{
													$childrenIDS=getArrayKeyValues(array('data'=>$children,'fields'=>array('srNo')));
													sort($child);
													sort($childrenIDS);
													$matched=array_intersect($childrenIDS,$child);
													if(isEmptyArray($matched)>0)
													{
														foreach($matched as $pChild)
														{
															if($pChild>0)
															{
																//$proccesdMenus[]=intval($pChild);
																$existsStatus=0;
																if($previousMenusStatus>0)
																{
																	$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$pChild)));
																	if(isEmptyArray($foundExistsData)>0)
																	{
																		$existsStatus=1;
																	}
																}
																if($existsStatus==0)
																{	
																	$builder = $this->db->table('extra_menu_permission');
																	$data=array('userType'=>$userType,'menuID'=>$pChild,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
																	$builder->set($data);
																	$builder->insert();
																	
																}
															}
														}
													}
												}
												else
												{	
													foreach($children as $foundChild)
													{
														$cSrNo=checkVariable($foundChild['srNo'],0,'intval');
														if($cSrNo>0)
														{
															//$proccesdMenus[]=intval($pChild);
															$existsStatus=0;
															if($previousMenusStatus>0)
															{
																$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$cSrNo)));
																if(isEmptyArray($foundExistsData)>0)
																{
																	$existsStatus=1;
																}
															}
															if($existsStatus==0)
															{	
																$builder = $this->db->table('extra_menu_permission');
																$data=array('userType'=>$userType,'menuID'=>$cSrNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
																$builder->set($data);
																$builder->insert();
																	
															}
														}
													}
												}
											}
										}
										elseif($process==-1)
										{
											if(isEmptyArray($children)>0)
											{
												if(isEmptyArray($child)>0)
												{
													$childrenIDS=getArrayKeyValues(array('data'=>$children,'fields'=>array('srNo')));
													sort($childrenIDS);
													sort($child);
													$matched=array_diff($childrenIDS,$child);
													if(isEmptyArray($matched)>0)
													{
														//$proccesdMenus[]=intval($srNo);
														$existsStatus=0;
														if($previousMenusStatus>0)
														{
															$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$srNo)));
															if(isEmptyArray($foundExistsData)>0)
															{
																$existsStatus=1;
															}
														}
														if($existsStatus==0)
														{
															$builder = $this->db->table('extra_menu_permission');
															$data=array('userType'=>$userType,'menuID'=>$srNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
															$builder->set($data);
															$builder->insert();	
														}
														foreach($matched as $pChild)
														{
															if($pChild>0)
															{
																//$proccesdMenus[]=intval($pChild);
																$existsStatus=0;
																if($previousMenusStatus>0)
																{
																	$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$pChild)));
																	if(isEmptyArray($foundExistsData)>0)
																	{
																		$existsStatus=1;
																	}
																}
																if($existsStatus==0)
																{		
																	$builder = $this->db->table('extra_menu_permission');
																	$data=array('userType'=>$userType,'menuID'=>$pChild,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
																	$builder->set($data);
																	$builder->insert();
															
																}
															}
														}
													}
												}
											}
										}
									}
									else
									{
										//$proccesdMenus[]=$srNo;
										$existsStatus=0;
										if($previousMenusStatus>0)
										{
											$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$srNo)));
											if(isEmptyArray($foundExistsData)>0)
											{
												$existsStatus=1;
											}
										}
										if($existsStatus==0)
										{
										$builder = $this->db->table('extra_menu_permission');
										$data=array('userType'=>$userType,'menuID'=>$srNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
										$builder->set($data);
										$builder->insert();
										}
										if(isEmptyArray($children)>0)
										{
											
											foreach($urlTypes as $urlType)
											{
												$foundChildrenInfo=searchValueInArray(array('type'=>1,'data'=>$children,'search'=>array('urlType'=>$urlType)));
												if(isEmptyArray($foundChildrenInfo)>0)
												{
													foreach($foundChildrenInfo as $foundChild)
													{
														$cSrNo=checkVariable($foundChild['srNo'],0,'intval');
														if($cSrNo>0)
														{
															//$proccesdMenus[]=intval($pChild);
															$existsStatus=0;
															if($previousMenusStatus>0)
															{
																$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$cSrNo)));
																if(isEmptyArray($foundExistsData)>0)
																{
																	$existsStatus=1;
																}
															}
															if($existsStatus==0)
															{	
																$builder = $this->db->table('extra_menu_permission');
																$data=array('userType'=>$userType,'menuID'=>$cSrNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
																$builder->set($data);
																$builder->insert();
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				else
				{
					$menuList=$this->getMenu(array('isVisible'=>-1,'panelType'=>1));
					if(isEmptyArray($menuList)>0)
					{
						$menuList=getArrayKeyValues(array('data'=>$menuList,'fields'=>array('srNo','parent_id','children','urlType')));
						foreach($menuList as $menu)
						{
							$srNo=checkVariable($menu['srNo'],0,'intval');
							$parentID=checkVariable($menu['parentID'],0,'intval');
							$children=checkVariable($menu['children'],0);
							$parentUrlType=checkVariable($menu['urlType'],0,'intval');
							if(isEmptyArray($children)>0)
							{
							$children=getArrayKeyValues(array('data'=>$children,'fields'=>array('srNo','urlType')));
							}
							if($srNo>0)
							{
								$foundPermissionInfo=searchValueInArray(array('type'=>1,'data'=>$processPermissionInfo,'search'=>array('id'=>$srNo),'isSingle'=>1));
								if(isEmptyArray($foundPermissionInfo)>0)
								{
									$process=checkVariable($foundPermissionInfo['process'],0,'intval');
									$child=checkVariable($foundPermissionInfo['child'],0);
									if($process==1)
									{
										
										//$proccesdMenus[]=$srNo;
										$existsStatus=0;
										if($previousMenusStatus>0)
										{
											$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$srNo)));
											if(isEmptyArray($foundExistsData)>0)
											{
												$existsStatus=1;
											}
										}
										if($existsStatus==0)
										{
											$builder = $this->db->table('extra_menu_permission');
											$data=array('userType'=>$userType,'menuID'=>$srNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
											$builder->set($data);
											$builder->insert();
										}
										if(isEmptyArray($children)>0)
										{
											if(isEmptyArray($child)>0)
											{
												$childrenIDS=getArrayKeyValues(array('data'=>$children,'fields'=>array('srNo')));
												sort($child);
												sort($childrenIDS);
												$matched=array_intersect($childrenIDS,$child);
												if(isEmptyArray($matched)>0)
												{
													foreach($matched as $pChild)
													{
														if($pChild>0)
														{
															//$proccesdMenus[]=intval($pChild);
															$existsStatus=0;
															if($previousMenusStatus>0)
															{
																$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$pChild)));
																if(isEmptyArray($foundExistsData)>0)
																{
																	$existsStatus=1;
																}
															}
															if($existsStatus==0)
															{		
																$builder = $this->db->table('extra_menu_permission');
																$data=array('userType'=>$userType,'menuID'=>$pChild,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
																$builder->set($data);
																$builder->insert();
															}
														}
													}
												}
											}
											else
											{	
												foreach($children as $foundChild)
												{
													$cSrNo=checkVariable($foundChild['srNo'],0,'intval');
													if($cSrNo>0)
													{
														//$proccesdMenus[]=intval($pChild);
														$existsStatus=0;
														if($previousMenusStatus>0)
														{
															$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$cSrNo)));
															if(isEmptyArray($foundExistsData)>0)
															{
																$existsStatus=1;
															}
														}
														if($existsStatus==0)
														{	
															$builder = $this->db->table('extra_menu_permission');
															$data=array('userType'=>$userType,'menuID'=>$cSrNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
															$builder->set($data);
															$builder->insert();
														}
													}
												}
											}
										}
									}
									elseif($process==-1)
									{
										if(isEmptyArray($children)>0)
										{
											if(isEmptyArray($child)>0)
											{
												$childrenIDS=getArrayKeyValues(array('data'=>$children,'fields'=>array('srNo')));
												sort($childrenIDS);
												sort($child);
												$matched=array_diff($childrenIDS,$child);
												if(isEmptyArray($matched)>0)
												{
													//$proccesdMenus[]=$srNo;
													$existsStatus=0;
													if($previousMenusStatus>0)
													{
														$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$srNo)));
														if(isEmptyArray($foundExistsData)>0)
														{
															$existsStatus=1;
														}
													}
													if($existsStatus==0)
													{
														$builder = $this->db->table('extra_menu_permission');
														$data=array('userType'=>$userType,'menuID'=>$srNo,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
														$builder->set($data);
														$builder->insert();
													}
													foreach($matched as $pChild)
													{
														if($pChild>0)
														{
															//$proccesdMenus[]=intval($pChild);
															$existsStatus=0;
															if($previousMenusStatus>0)
															{
																$foundExistsData=searchValueInArray(array('type'=>1,'data'=>$previousMenus,'search'=>array('menuID'=>$pChild)));
																if(isEmptyArray($foundExistsData)>0)
																{
																	$existsStatus=1;
																}
															}
															if($existsStatus==0)
															{	
																$builder = $this->db->table('extra_menu_permission');
																$data=array('userType'=>$userType,'menuID'=>$pChild,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved);
																$builder->set($data);
																$builder->insert();
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				$this->db->transComplete();
				//$this->db->transStatus() === true
			}
		}
		
		return $response;
	}
	public function getMenuList($options)
	{
		$response=0;
		$type=checkVariable($options['type'],0,'intval');
		$panelType=checkVariable($options['panelType'],0,'intval');
		if($type>0)
		{
			$id=checkVariable($options['id'],'','trim');
			if($type==1)
			{
				$queryText='SELECT srNo,name,parentID,mOrder,url,icon,isBlank,urlType FROM menu_name where  panelType=? and uStatus=? and url=? order by parentID limit 1';
				$query=$this->db->query($queryText,array($panelType,1,$id));
				if($query->getNumRows()>0)
				{
					$response=$query->getRowArray();
				}
			}
			elseif($type==2)
			{
				$queryText='SELECT srNo,name,parentID,mOrder,url,icon,isBlank,urlType FROM menu_name where  panelType=? and uStatus=? and parentID=? order by parentID';
				$query=$this->db->query($queryText,array($panelType,1,$id));
				if($query->getNumRows()>0)
				{
					$response=$query->getResultArray();
				}
			}
			else
			{
				$id=intval($options['id']);
				$queryText='SELECT srNo,name,parentID,mOrder,url,icon,isBlank,urlType FROM menu_name where panelType=? and uStatus=? and srNo=? order by parentID limit 1';
				$query=$this->db->query($queryText,array($panelType,1,$id));
				if($query->getNumRows()>0)
				{
					$response=$query->getRowArray();
				}
			}
		}
		else
		{
			$isVisible=isset($options['isVisible']) ? intval($options['isVisible']) : -1;
			if($isVisible==1)
			{
			$queryText='SELECT srNo,name,parentID,mOrder,url,icon,isBlank,urlType FROM menu_name where uStatus=? and isVisible=? and panelType=? order by parentID';
			$para=array(1,1,$panelType);
			}
			else
			{
			$queryText='SELECT srNo,name,parentID,mOrder,url,icon,isBlank,urlType FROM menu_name where uStatus=?   and panelType=? order by parentID';
			$para=array(1,$panelType);
			}
			$query=$this->db->query($queryText,$para);
			if($query->getNumRows()>0)
			{
				$response=$query->getResultArray();
			}
		}
		return $response;
	
	}
	public function getUrlExtraPermisions($options)
	{
		$response=0;
		$userType=checkVariable($options['userType'],0,'intval');
		$menuID=checkVariable($options['menuID'],0,'intval');
		$srNo=checkVariable($options['srNo'],0,'intval');
		$isMultiple=checkVariable($options['isMultiple'],0,'intval');
		$query=array();
		$queryStr='';
		$isRemoved=0;
		$parameters=array();
		array_push($query,' isRemoved=? ');
		array_push($parameters,$isRemoved);
		if($userType>0)
		{
			array_push($query,' userType=? ');
			array_push($parameters,$userType);
		}
		if($menuID>0)
		{
			array_push($query,' menuID=? ');
			array_push($parameters,$menuID);
		}
		if($srNo>0)
		{
			array_push($query,' srNo=? ');
			array_push($parameters,$srNo);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		if(!empty($queryStr))
		{
			$query = $this->db->query("select srNo,menuID from extra_menu_permission ".$queryStr,$parameters);	
			$rowFound=$query->getNumRows();
			if($rowFound>0)
			{
				if($isMultiple==1)
				{
					$response=$query->getResultArray();
				}
				else
				{
					$response=$query->getRowArray();
				}
			}
		}
		return $response;
	}
	public function getPermissionList($options)
	{
		$response=0;
		$type=checkVariable($options['type'],0,'intval');
		$userID=checkVariable($options['userID'],'','trim');
		$isRemoved=0;
		if($type==1)
		{
		$queryText='SELECT srNo,menuID,isParent FROM user_permissions where isRemoved=? and uStatus=? and userID=? ';
		}
		else
		{
		$queryText='SELECT menuID FROM user_permissions where isRemoved=? and uStatus=? and userID=? ';	
		}
		$query=$this->db->query($queryText,array($isRemoved,1,$userID));
		if($query->getNumRows()>0)
		{
			if($type==1)
			{
			$response=$query->getResultArray();
			}
			else
			{
				$response=array();
				foreach($query->getResultArray() as $row)
				{
					$id=checkVariable($row['menuID'],0,'intval');
					if($id>0)
					{
						$response[]=$id;
					}
				}
			}
		}
		return $response;
	
	}
	public function getMyMenu($options)
	{
		$response=0;
		$type=checkVariable($options['type'],0,'intval');
		$sesType=checkVariable($options['sesType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
		$userData=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		if(isEmptyArray($userData)>0)
		{
			$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
			if(!empty($userID))
			{
				 $response=$this->getPermissionList(array('userID'=>$userID));
			}
		}
		return $response;
	}
	public function getMenu($options)
	{
		$response=0;
		$isVisible=checkVariable($options['isVisible'],-1,'intval');
		$panelType=checkVariable($options['panelType'],-1,'intval');
		$list=$this->getMenuList(array('isVisible'=>$isVisible,'panelType'=>$panelType));
		$category1=searchMenu(array('menuIDS'=>$list,'field'=>'parentID','search'=>0));
		if(isEmptyArray($category1)>0)
		{
			array_multisort(array_map(function($element) { return $element['mOrder'];}, $category1), SORT_ASC, $category1);
			foreach($category1 as $key=>$cat)
			{
				if(isEmptyArray($cat)>0)
				{
					$srNo=checkVariable($cat['srNo'],0,'intval');
					if($srNo>0)
					{
						$children=searchMenu(array('menuIDS'=>$list,'field'=>'parentID','search'=>$srNo));
						if(isEmptyArray($children)>0)
						{
							array_multisort(array_map(function($element) { return $element['mOrder'];}, $children), SORT_ASC, $children);
							$category1[$key]['children']=$children;
						}
					}
				}
			}
			$response=$category1;
		}
		return $response;
	}
	public function updateUserTime($options)
	{
		$response=0;
		$sesName=checkVariable($options['sesName'],'','trim');
		$sesType=checkVariable($options['sesType'],0,'intval');
		$status=checkVariable($options['status'], 0,'intval');
		$userData=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$userID=(isEmptyArray($userData)>0) ? checkVariable($userData['userID'],'','trim') : '';
		$browserID=(isEmptyArray($userData)>0) ? checkVariable($userData['browserID'],'','trim') : '';
		$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType,'userID'=>$userID));
		if(!empty($browserID) && !empty($userID))
		{
			$this->db->transStart();
			$builder = $this->db->table('login_logs');
			$timing = new \Config\Timing();
		    $dateTime = $timing->dateTime;
			$where=array('userID'=>$userID,'browserID'=>$browserID);
			$builder->where($where);
			$builder->limit(1);
			$data=array('updatedOn'=>$dateTime);
			if($status==-1)
			{
				$data['status']=-1;
			}
			$builder->update($data);
			$this->db->transComplete();
			if($this->db->transStatus() === true)
			{
				$response=1;
			}
		}
		return $response;
	}
	public function saveLoginHistorys($options)
	{
		$response=0;
		$username=checkVariable($options['username'],'','trim');
		$userID=checkVariable($options['userID'],'','trim');
		$browserID=checkVariable($options['browserID'],'','trim');
		$loginFor= checkVariable($options['loginFor'],0,'intval');
		$mediaType=checkVariable($options['mediaType'],0,'intval');
		$userType=checkVariable($options['userType'],0,'intval');
		$status=checkVariable($options['status'],0,'intval');
		$timing = new \Config\Timing();
		$dateTime = $timing->dateTime;
		$device = "Desktop";
		$request=\Config\Services::request();
		$agent = $request->getUserAgent();
		if($agent->isMobile()) {
			$device = $agent->getMobile();
		}
		$visitorBrowser = $agent->getBrowser().' '.$agent->getVersion().' - '.$device;
		$this->db->transStart();
		$ip=$request->getIPAddress();
		$builder = $this->db->table('login_logs');
		$data=array('username'=>$username,'loginFor'=>$loginFor,'userID'=>$userID,'visitorIP'=>$ip,'visitorBrowser'=>$visitorBrowser,'status'=>$status,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'loginType'=>$mediaType,'browserID'=>$browserID,'userType'=>$userType);
		$builder->set($data);
		$builder->insert();
		$this->db->transComplete();
		if($this->db->transStatus() === true)
		{
			$response=1;
		}
		return $response;
	}
	public function getMyPermissions($options)
	{
		$response=0;
		$id=checkVariable($options['id'],0,'intval');
		$userID=checkVariable($options['userID'],'','trim');
		$fetchFields=checkVariable($options['fetchFields'],' ');
		$orderBy=checkVariable($options['orderBy'],' ');
		$query=array();
		$queryStr='';
		$isRemoved=0;
		$uStatus=1;
		$parameters=array();
		array_push($query,' up.uStatus=? ');
		array_push($parameters,$uStatus);
		array_push($query,' up.isRemoved=? ');
		array_push($parameters,$isRemoved);
		if(!empty($id))
		{
		array_push($query,' (mn.parentID=? or mn.srNo=? ) ');
		array_push($parameters,$id);
		array_push($parameters,$id);
		}
		if(!empty($userID))
		{
		array_push($query,' up.userID=? ');
		array_push($parameters,$userID);
		}
		if(count($query)>0)
		{
		$queryStr=' where '.join(' and ',$query).' ';
		}
		if(!empty($queryStr))
		{
			$fetchFields=(empty(trim($fetchFields))) ?  ' up.menuID,mn.urlType ' : $fetchFields;
			$orderBy=(empty($orderBy)) ?  ' order by up.menuID ASC ' : $orderBy;
			$query = $this->db->query("select ".$fetchFields." FROM user_permissions up left join menu_name mn on up.menuID=mn.srNo ".$queryStr.$orderBy,$parameters);
			if($query->getNumRows()>0)
			{
				$response = $query->getResultArray();
			}
		}
		return $response;
	}
	public function isLoggedIn($options) 
	{
		$response=0;
		$sesType=checkVariable($options['sesType'],0,'intval');
		$isPermission=checkVariable($options['isPermission'],0,'intval');
		$panelType=checkVariable($options['panelType'],0,'intval');
		$sesName=checkVariable($options['sesName'],'','trim');
		$permissions=array();
		$userData=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType));
		$redirect='';
		if(isEmptyArray($userData)>0)
		{
			$userID=idConversion(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
			if(!empty($userID))
			{
				$userData['userID']=$userID;
				$userStatus=$this->getUserInfo($userData);
				if($userStatus==1)
				{
					$url=current_url();
					$current=parse_url($url, PHP_URL_PATH);
					if(!empty($current))
					{
						$current=ltrim($current,'/');
						$url=explode('/', $current);
						$removeURLS=array('admin','index.php');
						foreach($removeURLS as $rURL)
						{
							if(in_array($rURL,$url)==true)
							{
								$url=array_slice($url, 1);	
								$current=join('/',$url);
							}
						}
						$route = \Config\Services::routes();
						$get=$route->getRoutes('get');
						$post=$route->getRoutes('post');
						$routes=array();
						if(isEmptyArray($get)>0) {
						$routes=array_merge($routes,array_keys($get));
						}
						if(isEmptyArray($post)>0) {
						$routes=array_merge($routes,array_keys($post));
						}
						if(isEmptyArray($routes)>0)
						{
						$routes=array_values(array_unique($routes));
						$removeRoute=array('BaseController(.*)','(.*)/initController');
						$routes=array_diff($routes,$removeRoute);
						}
						if(!empty($current))
						{
							$id=0;
							if(isEmptyArray($routes)>0 && in_array($current,$routes)==true)
							{
								$info=$this->getMenuList(array('type'=>1,'id'=>$current,'panelType'=>$panelType));
								$parentID=(isEmptyArray($info)>0) ? checkVariable($info['parentID'],0,'intval') : 0;
								$id=(isEmptyArray($info)>0) ? checkVariable($info['srNo'],0,'intval') : 0;
								if($parentID>0)
								{
									$info2=$this->getMyPermissions(array('id'=>$parentID,'userID'=>$userID));
									$info2=getArrayKeyValues(array('data'=>$info2,'fields'=>array('urlType')));
									$permissions=array_values(array_unique($info2));
								}
							}
							if($id<=0 && count($url)>1)
							{
								//$last_url=end($url);
								//array_pop($url);
								$url2=$url;
								//$url2=array_slice($url, -2, 2);
								//$url2= array_combine(array(-2,-1), $url2);
								if(count($url2)>0)
								{
									foreach($url2 as $key=>$url21)
									{ 
										if(is_numeric($url21)==true)
										{	
										array_splice($url, $key, 1,array('([0-9]+)'));
										}
										else if(is_numeric($url21)==false && in_array(strlen($url21),array(13,14,15,16,17,18,19,20,32,40,64,96,128))==true)
										{	
										array_splice($url, $key, 1,array('(.*)'));
										}   
									}
								}	
								$current=join('/',$url);
								if(isEmptyArray($routes)>0 && in_array($current,$routes)==true)
								{	
									$info=$this->getMenuList(array('type'=>1,'id'=>$current,'panelType'=>$panelType));
									$id=(isEmptyArray($info)>0) ? checkVariable($info['srNo'],0,'intval') : 0;
									$parentID=(isEmptyArray($info)>0) ? checkVariable($info['parentID'],0,'intval') : 0;
									if($parentID>0)
									{
									$info2=$this->getMyPermissions(array('id'=>$parentID,'userID'=>$userID));
									$info2=getArrayKeyValues(array('data'=>$info2,'fields'=>array('urlType')));
									$permissions=array_values(array_unique($info2));
									}
								}
							
							}
							if($id>0)
							{
								//$session = session();
								$session = \Config\Services::session(); 
								$session->setFlashdata('loginRedirect', 1);
								$myMenuList=$this->getMyMenu(array('type'=>0,'sesName'=>$sesName,'sesType'=>$sesType));
								if(isEmptyArray($myMenuList)>0)
								{
									if(in_array($id,$myMenuList)==true)
									{
										$response=1;
									}
									else
									{
										$firstID=isset($myMenuList[0]) ? intval($myMenuList[0]) : 0;
										$info=$this->getMenuList(array('id'=>$firstID,'panelType'=>$panelType));
										$redirect=isset($info['url']) ? trim($info['url']) : '';
										
									}
								}
							}
						}
					}
				}
			}
		}
		if($response!=1)
		{
			if(empty($redirect))
			{	
				$redirect=site_url('/'.AdminConfig::$prePath.'login');
				login(array('sesName'=>$sesName,'category'=>-1,'type'=>$sesType));
			}
			else
			{
				$redirect=site_url($redirect);
			}
			$response=array('status'=>-1,'redirect'=>$redirect);
		}
		else
		{
			if($isPermission==1 && isEmptyArray($permissions)>0) 
			{
				$response=array('status'=>1,'permissions'=>$permissions);
			}
			else
			{
				$response=array('status'=>1,'response'=>$response);
			}
			
		}
		
		return $response;
	}
	public function getUserInfo($options)
	{
		$response=0;
		$userID=checkVariable($options['userID'],'','trim');
		$userType=checkVariable($options['userType'],0,'intval');
		$type=checkVariable($options['type'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		if(!empty($userID))
		{
			$para=array('userID'=> $userID,'userStatus'=>1,'isRemoved'=>0);
			if(!empty($userType))
			{
				$para['userType']=$userType;
			}
			$builder = $this->db->table('users');
			$query = $builder->select('*')->where($para)->limit(1)->get();
			if($query->getNumRows()>0)
			{
				if($action==1)
				{
					$response = $query->getRowArray();
				}
				else
				{
					$response=1;
				}
			}
		}
		return $response;
	}
	public function refreshMenuList($options)
	{
		$response=0;
		$userID=checkVariable($options['userID'],'','trim');
		if(!empty($userID))
		{
			$userInfo=$this->getUserInfo(array('action'=>1,'userID'=>$userID,'isArray'=>1));
			$existsPermissions=$this->getMyPermissions(array('userID'=>$userID));
			$existsPermissionsStatus=isEmptyArray($existsPermissions);
			if(isEmptyArray($userInfo)>0)
			{
				$reference=checkVariable($userInfo['reference'],'','trim');
				$userType=checkVariable($userInfo['user_type'],0,'intval');
				$this->initializeUserPermissions(array('userType'=>$userType));
				sleep(1);
				$query=array();
				$queryStr='';
				$isRemoved=0;
				$parameters=array();
				array_push($query,' uStatus=? ');
				array_push($parameters,1);
				array_push($query,' panelType=? ');
				array_push($parameters,1);
				array_push($query,' isRemoved=? ');
				array_push($parameters,0);
				if(strpos($reference,'isAdmin')=== false)
				{
					array_push($query,' ( isGlobal=? or srNo in ( select menuID from extra_menu_permission where userType=? ) )');
					array_push($parameters,1);
					array_push($parameters,$userType);
				}
				if(count($query)>0)
				{
				$queryStr=' where '.join(' and ',$query).' ';
				}
				if(!empty($queryStr))
				{
					$query = $this->db->query("select srNo,parentID from menu_name ".$queryStr,$parameters);
					if($query->getNumRows()>0)
					{
						$isRemoved=0;
						$rows=$query->getResultArray();
						$proccesdMenus=array();
						$uStatus=1;
						$timing = new \Config\Timing();
						$dateTime = $timing->dateTime;
						$builder = $this->db->table('user_permissions');
						$this->db->transStart();
						foreach($rows as $row)
						{
							$menuID=checkVariable($row['srNo'],0,'intval');
							$parentID=checkVariable($row['parentID'],0,'intval');
							if($menuID>0)
							{
								$proccesdMenus[]=$menuID;
								$reference='';	
								$isExists=0;
								if($existsPermissionsStatus>0)
								{
									$commonPermissionInfo=searchValueInArray(array('type'=>1,'data'=>$existsPermissions,'search'=>array('menuID'=>$menuID),'isSingle'=>1));
									if(isEmptyArray($commonPermissionInfo)>0)
									{
										$isExists=1;
									}
								}
								if($isExists<=0)
								{
									$isParent=1;
									if($parentID>0)
									{
										$isParent=0;
									}
									$data=array('userID'=>$userID,'menuID'=>$menuID,'createdOn'=>$dateTime,'updatedOn'=>$dateTime,'isRemoved'=>$isRemoved,'uStatus'=>$uStatus,'isParent'=>$isParent);
									$builder->set($data);
									$builder->insert();
									$response++;
								}
							}
						}
						if(isEmptyArray($proccesdMenus)>0 && $existsPermissionsStatus>0)
						{
							$existsPermissions=getArrayKeyValues(array('data'=>$existsPermissions,'fields'=>array('menu_id')));
							$blockMenus=array_diff($existsPermissions,$proccesdMenus);
							if(isEmptyArray($blockMenus)>0)
							{
								foreach($blockMenus as $menuID)
								{
									$where=array('userID'=>$userID,'menuID'=>$menuID,'isRemoved'=>$isRemoved);
									$this->db->where($where);
									$data=array('updatedOn'=>$this->dateTime,'isRemoved'=>1);
									$builder->update($data);
								}
							}
						}
						$this->db->transComplete();
						//$this->db->transStatus() === true
					}
				}
			}
		}
		return $response;
	}
	public function validateUser($options=false)
	{
		$response=0;
		$username =checkVariable($options['username'],'','trim');
		$password =checkVariable($options['password'],'');
		$remember =checkVariable($options['remember'], 0,'intval');
		$loginFor =checkVariable($options['loginFor'], 0,'intval');
		$userType =checkVariable($options['userType'], 0,'intval');
		$sesName =checkVariable($options['sesName'],'userInfo','trim');
		$userID=null;
		$mediaType=0;
		$browserID=getBrowserID(0);
		if(empty($username) || empty($password))
		{
			$response=array('status'=>-4,'msg'=>'username and password are mandatory fields');
		}
		elseif(is_numeric($username)==true && strlen($username)!=10)
		{
			$response=array('status'=>-5,'msg'=>'invalid  username');
		}
		elseif(is_numeric($username)==false && filter_var($username, FILTER_VALIDATE_EMAIL)===false)
		{
			$response=array('status'=>-6,'msg'=>'invalid  username');
		}
		else
		{
			$userStatus=1;
			$fieldName='';
			$isVerified=0;
			$fetchRow=0;
			if(is_numeric($username)==true && strlen($username)==10)
			{
				$mediaType=1;
				$fieldName='mobile';
			}
			else if(is_numeric($username)==false && filter_var($username, FILTER_VALIDATE_EMAIL)!==false)
			{
				$mediaType=2;
				$fieldName='email';
			}
			else
			{
				$response=array('status'=>-1,'msg'=>'invalid  username');
			}
			if(!empty($fieldName) && !empty($mediaType))
			{
				/*$sql = "SELECT * FROM some_table WHERE id = :id: AND status = :status: AND author = :name:";
				$db->query($sql, [
				'id'     => 3,
				'status' => 'live',
				'name'   => 'Rick',
				]);
				$query   = $this->db->query('SELECT name, title, email FROM user');
				$response = $query->getResultArray();
				$results = $this->select('title, image, categories, id, excerpt')->groupStart()->like('title', $search)->orLike('excerpt', $search)->groupEnd()->where('status','live')->paginate(2);
				$total   = $this->select('title, image, categories, id, excerpt')->groupStart()->like('title', $search)->orLike('excerpt', $search)->groupEnd()->where('status','live')->countAllResults();
				//////////////
				$builder->select('*')->groupStart()->where(array($fieldName=>$username,"userStatus"=>$userStatus,'loginFor'=>$loginFor,'isRemoved'=>0,'userType'=>$userType))->groupEnd()->orderBy("createdOn","ASC");
				 
				return [
				'products'  => $this->paginate(),
				'pager'     => $this->pager,
				];
				*/
				$builder = $this->db->table('users');
				$query=$builder->select('*')->where(array($fieldName=>$username,"userStatus"=>$userStatus,'loginFor'=>$loginFor,'isRemoved'=>0,'userType'=>$userType))->orderBy("createdOn","ASC")->limit(1)->get();
				//echo $this->db->getLastQuery();
				if($query->getNumRows()>0)
				{
					$fetchRow=$query->getRowArray();
					$userID=checkVariable($fetchRow['userID'],'','trim');
					$builder2 = $this->db->table('verified_user_media');
					$catelog=1;
					$query2=$builder2->select('mStatus,srNo')->where(array('userID'=>$userID,"mType"=>$mediaType,'mValue'=>$username,'catelog'=>$catelog))->orderBy("createdOn","ASC")->limit(1)->get();
					if($query2->getNumRows()>0)
					{
						$row2=$query2->getRowArray();
						$mStatus=checkVariable($row2['mStatus'],0,'intval');
						if($mStatus==1)
						{
							$isVerified=1;
						}
						else
						{
							$isVerified=-1;
							unset($fetchRow);
						}
						
					}
					/*$session =session();
					$newdata = [
					'username'  => 'johndoe',
					'email'     => 'johndoe@some-site.com',
					'logged_in' => true,
					];
					$session->set($newdata);*/
				}
				if(isEmptyArray($fetchRow)>0)	
				{
					$userType=checkVariable($fetchRow['userType'],0,'intval');
					$userID=checkVariable($fetchRow['userID'],'','trim');
					$name=checkVariable($fetchRow['username'],'','trim');
					$userPassword=checkVariable($fetchRow['password'] ,'');
					if($userType<=0 || empty($userID) || empty($userPassword))
					{
						$response=array('status'=>-3,'msg'=>'invalid  username');
					}
					else
					{
						if(password_verify($password, $userPassword)!=true) 
						{	
							$response=array('status'=>-2,'msg'=>'invalid  password');
						}
						else
						{
							if($remember==1) 
							{
								$cookie = array(
									'name'   => 'username',
									'value'  => $username,
									'expire' => '86500'
								);
								$cookie1 = array(
									'name'   => 'password',
									'value'  => $password,
									'expire' => '86500'
								);
								set_cookie($cookie);
								set_cookie($cookie1);
								
								$cookie = array(
								'name'   => 'username',
								'value'  => $username,
								'expire' => '86500',
								'prefix' => ''
								);
								$cookie1 = array(
								'name'   => 'password',
								'value'  => $password,
								'expire' => '86500',
								'prefix' => ''
								);
							}
							else 
							{
								delete_cookie('username');
								delete_cookie('password');
							}
							$responseID=idConversion(array('type'=>1,'userID'=>$userID));
							$userInfo = array(
								'userType' => $userType,
								'userID' => $responseID,
								'mediaType' => $mediaType,
								'username' => $name,
								'mediaValue' => $username,
								'browserID'=>$browserID,
								'panelName' => $sesName.' panel',
								'redirectUrl' => site_url($sesName.'/logout'),
								'isLogin' => 1
							);
							$this->refreshMenuList(array('userID'=>$userID));
							$status=login(array('sesName'=>$sesName,'category'=>1,'data'=>$userInfo));
							if($status==1)
							{
								$response=1;
								$response=array('status'=>1,'msg'=>'success');
							}
						}
					}
				}
				else
				{
					if($mediaType==1 && $isVerified==-1)
					{
						$response=array('status'=>-7,'msg'=>'your login blocked by admin');
					}
					elseif($mediaType==2 && $isVerified==-1)
					{
						$response=array('status'=>-8,'msg'=>'your login blocked by admin');
					}
					else
					{
						$response=array('status'=>-9,'msg'=>'invalid  username or password');
					}
				}
			}
		}
		$responseStatus=(isEmptyArray($response)>0) ? checkVariable($response['status'],0,'intval') : $response;
		$this->saveLoginHistorys(array('username'=>$username,'userID'=>$userID,'loginFor'=>$loginFor,'status'=>$responseStatus,'mediaType'=>$mediaType,'browserID'=>$browserID,'userType'=>$userType));
		return $response;
	}
	
}
?>