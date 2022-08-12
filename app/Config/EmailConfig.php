<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;
use App\Libraries\CommonMethods;
class EmailConfig extends BaseConfig
{
							
    public $config,$nConfig,$sendBy,$replayTo,$senderName,$processForward,$mailerConfig;
	public function __construct()
	{
		$this->sendBy= 'info@cme2022.com';
		$this->replayTo='gss2022vapi@gmail.com';
		$this->senderName= 'GSS-2022';
		$this->processForward='ci.shamsher@gmail.com';
		$this->config=array(
		'protocol'=>'sendmail',
		'userAgent'=>"Global Surgeons Summit",
		'mailpath'=>'/usr/sbin/sendmail',
		'charset' =>"utf-8",
		'mailType'=>"html",
		'wordWrap'=>TRUE,
		'priority'=>1,
		'newline'=>"\r\n",
		'validate'=>TRUE
		);
		$this->nConfig=array(
		"protocol"=>"smtp",
		"SMTPHost"=>"mail.cme2022.com",
		"SMTPUser"=>"info@cme2022.com",
		"SMTPPort"=>"465",
		"SMTPPass"=>"sammy@#@!",
		"charset" =>"utf-8",
		"mailType"=>"html",
		"wordWrap"=>TRUE,
		"priority"=>1,
		"newline"=>"\r\n",
		"validate"=>FALSE,
		);
		$this->mailerConfig=array(
		'host'=>'mail.cme2022.com',
		'username'=>'info@cme2022.com',
		'password'=>'sammy@#@!',
		'port'=>465,
		);
		
	}
}
