<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Timing extends BaseConfig
{
    public $dateTime,$now,$today,$currentMonth,$currentDay,$currentYear;
	public function __construct()
	{
		$this->dateTime= date("Y-m-d H:i:s");
		$this->currentMonth= date("m");
		$this->currentDay= date("d");
		$this->currentYear= date("Y");
		$this->now= strtotime("now");
		$this->today=strtotime("today");
	}
}
