<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Libraries\CommonMethods;
class WebsiteDetails extends BaseConfig
{				
    public $projectName,$projectIcon;
	public function __construct()
	{
		$this->projectName='Nidan Imaging Center ';
		$this->projectIcon='fa-solid fa-microscope';
	}
}
