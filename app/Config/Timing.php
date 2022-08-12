<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Timing extends BaseConfig
{
    public $dateTime,$now,$today;
	public function __construct()
	{
		$this->dateTime= date("Y-m-d H:i:s");
		$this->now= strtotime("now");
		$this->today=strtotime("today");
	}
}
