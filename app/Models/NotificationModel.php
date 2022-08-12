<?php
namespace App\Models;
use CodeIgniter\Model;
use Config\Services;
use App\Libraries\AdminConfig;
use App\Libraries\CommonMethods;
use App\Models\Admin\RegistrationModel;
//use CodeIgniter\Database\Query;
class NotificationModel extends Model
{
	public $db;
	public function __construct()
	{
		$this->db=\Config\Database::connect();
	}
	public function getList($options=false)
	{
		$response=0;
		$mediaType=checkVariable($options['mediaType'],0,'intval');
		$mediaStatus=checkVariable($options['mediaStatus'],0,'intval');
		$search=checkVariable($options['search'],'','trim');
		$dateTime=checkVariable($options['dateTime'],0);
		$dateTime=(!empty($dateTime)) ? date("Y-m-d",strtotime($dateTime)) : '';
		$offset=abs(checkVariable($options['offset'],0,'intval'));
		$limit=abs(checkVariable($options['limit'],20,'intval'));
		$type=checkVariable($options['type'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		$orderBy=checkVariable($options['orderBy'],'');
		$fetchField=checkVariable($options['fetchField'],'');
		$query=array();
		$queryStr='';
		$parameters=array();
		//SELECT `id`, `member_id`, `recipient`, `subject`, `message`, `type`, `message_for`, `m_status`, `date_created`, `reference` FROM `message` WHERE 1
		if($type==1)
		{
			$fetchField=' id,memberID,recipient,subject,message,type,messageFor,mStatus,dateTime ';
		}
		else
		{
			$fetchField='count(id) as found ';
		}
		
		if($mediaStatus==1)
		{
		array_push($query,' mStatus=? ');
		array_push($parameters,1);
		}
		elseif($mediaStatus==-1)
		{
		array_push($query,' mStatus!=? ');
		array_push($parameters,1);	
		}
		if($mediaType>0)
		{
			if($mediaType==1)
			{
			array_push($query,' type=? ');
			array_push($parameters,1);
			}
			else
			{
			array_push($query,' type=? ');
			array_push($parameters,2);	
			}
		}
		if(!empty($dateTime))
		{
			array_push($query,' date(dateTime)= date(?)');
			array_push($parameters,$dateTime);
		}
		if(!empty($search))
		{
			//recipient`, `subject`, `message`, `type`, `message_for
			array_push($query," (recipient like ? or  subject  like ?  or messageFor like ? )" );
			$search='%'.$search.'%';
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
		}
		if(count($query)>0)
		{
			$queryStr=' where '.join(' and ',$query).' ';
		}
		if($type==1)
		{
			if(empty($orderBy))
			{
				$orderBy=' order by dateTime DESC ';
			}
			$parameters[]=$offset;
			$parameters[]=$limit;
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from message  ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from message ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." from message ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." from message ";
			}
		}
		$selQuery = $this->db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			if($type==1)
			{
				$response=$selQuery->getResultArray();
			}
			else
			{
				$row = $selQuery->getRowArray();
				$response=checkVariable($row['found'],0,'intval');
			}
		}
		return $response;
	}
	public function getLoginList($options=false)
	{
		$response=0;
		$dateTime=checkVariable($options['dateTime'],0);
		$dateTime=(!empty($dateTime)) ? date("Y-m-d",strtotime($dateTime)) : 0;
		$search=checkVariable($options['search'],'','trim');
		$offset=abs(checkVariable($options['offset'],0,'intval'));
		$limit=abs(checkVariable($options['limit'],20,'intval'));
		$type=checkVariable($options['type'],0,'intval');
		$userType=checkVariable($options['userType'],0,'intval');
		$certificateType=checkVariable($options['certificateType'],0,'intval');
		$action=checkVariable($options['action'],0,'intval');
		$orderBy=checkVariable($options['orderBy'],'');
		$query=array();
		$queryStr='';
		$isRemoved=0;
		$parameters=array();
		array_push($query,' usr.isRemoved = ? ');
		array_push($parameters,$isRemoved);
		array_push($query,' ld.isRemoved = ? ');
		array_push($parameters,$isRemoved);
		//array_push($query,' ld.userType!=? ');
		//array_push($parameters,1);
		//SELECT `id`, `user_id`, `browser_id`, `login_for`, `login_type`, `user_type`, `username`, `visitor_ip`, `visitor_browser`, `status`, `created_on`, `updated_on`, `is_removed`, `reference` FROM `login_logs` WHERE 1
		if($type==1)
		{
			$fetchField="ld.userType,ld.username,ld.visitorIP,ld.visitorBrowser, ld.createdOn ";
		}
		else
		{
			$fetchField='count(ld.id) as found ';
		}
		if(!empty($search))
		{
			$search='%'.$search.'%';
			array_push($query," (ld.visitorIP LIKE ? or ld.visitorBrowser LIKE ? or usr.username LIKE ? or  usr.email LIKE ? or usr.mobile LIKE ? )" );
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
			array_push($parameters,$search);
		}
		if(!empty($dateTime))
		{
			array_push($query,' date(ld.createdOn) = date(?) ');
			array_push($parameters,$dateTime);
		}
		if(!empty($userType))
		{
			array_push($query,' ld.userType = ? ');
			array_push($parameters,$userType);
		}
		if(count($query)>0)
		{
			$queryStr=' where '.join(' and ',$query).' ';
		}
		if($type==1)
		{
			$parameters[]=$offset;
			$parameters[]=$limit;
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." FROM login_logs ld left join users usr on ld.userID=usr.userID ".$queryStr.$orderBy."limit ?,?";
			}
			else
			{
				$queryText="SELECT  ".$fetchField." FROM login_logs ld left join users usr on ld.userID=usr.userID ".$orderBy."limit ?,?";
			}
		}
		else
		{
			if(!empty(trim($queryStr)))
			{
				$queryText="SELECT  ".$fetchField." FROM login_logs ld left join users usr on ld.userID=usr.userID ".$queryStr;
			}
			else
			{
				$queryText="SELECT  ".$fetchField." FROM login_logs ld left join users usr on ld.userID=usr.userID ";
			}
		}
		$selQuery = $this->db->query($queryText,$parameters);
		if($selQuery->getNumRows()>0)
		{
			if($type==1)
			{
			  $response=$selQuery->getResultArray();
			}
			else
			{
				$row = $selQuery->getRow();
				if(isset($row->found) && intval($row->found)>0)
				{
					$response=intval($row->found);
				}
			}
		}
		return $response;
		
	}
	public function triggerMultipleProcess($options=false)
	{
		$my=checkVariable($options['my'],0);
		$queryText="SELECT count(srNo) as found,mediaType FROM member_notification_details where sendStatus=? and isRemoved=? ";
		$parameters=array(0,0);
		$remain=0;
		$sendMediaType=0;
		$query = $this->db->query($queryText,$parameters);
		if($query->getNumRows()>0)
		{
			$row = $query->getRowArray();
			$mediaType=checkVariable($row['mediaType'],0,'intval');
			$found=checkVariable($row['found'],0,'intval');
			if($found>0)
			{
				$remain=$found;
				if($found==1)
				{
					$sendMediaType=$mediaType;
				}
			}
		}
		if($remain>0)
		{
			$max=50;
			$max=($remain<$max) ? $remain : $max;
			$notifications=range(1,$max);
			$key='2970918e4a3bfa02fefc957526a408d549c10435';
			$url='https://www.register.cme2022.com/index.php/send/notification/process/3a6feccdeefce6bed4b2780f0ddaaecf82f18acc/1';
			//$url='http://localhost/goacon2022/index.php/send/notification/process/3a6feccdeefce6bed4b2780f0ddaaecf82f18acc/1';
			$header="-H 'Content-type: application/json' -H 'Authorization: key=3a6feccdeefce6bed4b2780f0ddaaecf82f18acc'";
			$data = array("key" =>$key,'mediaType'=>2); 
			foreach($notifications as $p)
			{
				if($sendMediaType>0)
				{
				$mediaType=$sendMediaType;	
				}
				else
				{
				$mediaType=(($p%2)==0) ? 2 : 1;
				}
				$mediaType=2;
				$data['mediaType']=$mediaType;
				$command = "curl $header -X 'POST' -d '".json_encode($data)."' --url '".$url."' > /dev/null 2>&1 &";
				exec($command);
				sleep(1);
			}
		}
	}
	public function processBulkMessage($options=false)
	{
		$sendMediaType=checkVariable($options['mediaType'],2,'intval');
		$this->db->transStart();
		$queryText="SELECT srNo,notificationID,memberID,mediaType,catelog,subject,message,sendTo,descriptions FROM member_notification_details where sendStatus=? and isRemoved=? and mediaType=? order by createdOn ASC limit 1";
		$parameters=array(0,0,$sendMediaType);
		sleep(1);
		$query = $this->db->query($queryText,$parameters);
		if($query->getNumRows()>0)
		{
			 $row=$query->getRowArray();
			 $srNo=checkVariable($row['srNo'],0,'intval');
			 $catelog=checkVariable($row['catelog'],0,'intval');
			 $mediaType=checkVariable($row['mediaType'],0,'intval');
			 $notificationID=checkVariable($row['notificationID'],'','trim');
			 $sendTo=checkVariable($row['sendTo'],'','trim');
			 $memberID=checkVariable($row['memberID'],'','trim');
			 $subject=checkVariable($row['subject'],'','trim');
			 $message=checkVariable($row['message'],'','trim');
			 $descriptions=checkVariable($row['descriptions'],'','trim');
			 if(!empty($sendTo) && !empty($mediaType) && !empty($message) && !empty($srNo))
			 {
				 $status=2;
				 $timing = new \Config\Timing();
				 $dateTime = $timing->dateTime;
				 $messageFor='notification';
				 if(!empty($descriptions))
				 {
					 $descriptions=json_decode($descriptions,true);
				 }
				 $reference=null;
				 $template=(isEmptyArray($descriptions)>0) ? checkVariable($descriptions['template'],0,'intval') : 0;
				 $messageFor=(isEmptyArray($descriptions)>0) ?  checkVariable($descriptions['messageFor'],'','trim') : 'notification';
				 $result=(isEmptyArray($descriptions)>0) ? checkVariable($descriptions['result'],0) : 0;
				 $certificateType=(isEmptyArray($descriptions)>0) ? checkVariable($descriptions['certificateType'],0) : 0; 
				 $validate=(isEmptyArray($descriptions)>0) ? checkVariable($descriptions['validate'],0) : 0;
				 $validateType=(isEmptyArray($validate)>0) ? checkVariable($validate['type'],0) : 0;
				 $where=array('notificationID'=>$notificationID,'srNo'=>$srNo,'isRemoved'=>0,'mediaType'=>$mediaType,'sendTo'=>$sendTo,'memberID'=>$memberID);
				 $data=array('updatedOn'=>$dateTime,'sendStatus'=>$status);
				 $builder=$this->db->table('member_notification_details');
				 $builder->where($where);
				 $builder->limit(1);
				 $builder->update($data);
				 if($mediaType==1)//sms
				 {
					$status=CommonMethods::sendSMS(array('mobile_no'=>$sendTo,'message'=>$message,'templateID'=>'5899'));
					$data =array('memberID'=>$memberID,'recipient'=>$sendTo,'message'=>$message,'type'=>1, 'messageFor'=>'notification','mStatus'=>$status,'dateTime'=>$dateTime);
					$builder = $this->db->table('message');
					$builder->set($data);
					$builder->insert();
				 }
				 else
				 {
					if($validateType==1)
					{	
						$registrationModel=new registrationModel();
						$result=$registrationModel->getMemberDetails(array('id'=>$memberID,'type'=>1));
						if(isEmptyArray($result)>0)
						{
							$firstName=checkVariable($result['personal']['firstName'],'','trim');
							$lastName=checkVariable($result['personal']['lastName'],'','trim');
							$memberName=ucwords($firstName.' '.$lastName);
							if($catelog==2)
							{
								$certificateTypes=CommonMethods::getCertificateType(array('type'=>0));
								$certificateInfo=searchValueInArray(array('isSingle'=>1,'type'=>1,'data'=>$certificateTypes,'search'=>array('id'=>$certificateType)));
								if(isEmptyArray($certificateInfo)>0)
								{
									$registerName=checkVariable($certificateInfo['name'],'','trim');
									$certificateUrl=checkVariable($certificateInfo['certificateUrl'],'','trim');
									$attachmentFile=$registerName.'-'.strtoupper($memberName).'.pdf';
									$attachmentName=$registerName.'-Certificate-2021';
									$htmlData='';
									//$htmlData=$this->receiptData->getCertificate(array('output'=>0,'fontSize'=>48,'xPosition'=>0,'yPosition'=>-39,'certificateName'=>$memberName,'certificateUrl'=>$certificateUrl,'registerName'=>$registerName));
									//$htmlData=CommonMethods::getCertificate(array('type'=>0,'output'=>0,'payID'=>$payID,'srNo'=>$payNo));
									$para=array('sendTo'=>$sendTo,'subject'=>$subject,'message'=>$message,'template'=>0,'attachment'=>$htmlData,'isAttachmentString'=>1,'attachmentFile'=>$attachmentFile,'attachmentName'=>$attachmentName);
									$status=CommonMethods::sendMail($para);
									$data =array('memberID'=>$memberID,'recipient'=>$sendTo,'subject'=>$subject,'message'=>$message,'type'=>2, 'messageFor'=>'notification_certificate','mStatus'=>$status,'dateTime'=>$dateTime);
									$builder = $this->db->table('message');
									$builder->set($data);
									$builder->insert();
								}
							}
						}
					}
					else
					{
						$para=array('sendTo'=>$sendTo,'subject'=>$subject,'message'=>$message,'template'=>$template,'result'=>$result);
						$status=CommonMethods::sendMail($para);
						$data =array('memberID'=>$memberID,'recipient'=>$sendTo,'subject'=>$subject,'message'=>$message,'type'=>2, 'messageFor'=>$messageFor,'mStatus'=>$status,'dateTime'=>$dateTime);
						$builder = $this->db->table('message');
						$builder->set($data);
						$builder->insert();
					}
				 }
				 $status=($status==1) ? 1 : -1;
				 $data=array('updatedOn'=>$dateTime,'sendStatus'=>$status);
				 $builder=$this->db->table('member_notification_details');
				 $builder->where($where);
				 $builder->limit(1);
				 $builder->update($data);
			 }
		}
		$this->db->transComplete();
	}
}