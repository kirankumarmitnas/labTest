<?php
namespace App\Libraries;
class AdminConfig 
{
    public static $commonPath='admin/common/',$title='Nidan Imaging Center',$sesName='admin',$sesLoginFor=1,$sesType=1,$panelType=1,$prePath='admin/',$permissions;
	public static function set($field,$value)
	{
		if(isset(self::$$field))
		{
			self::$$field=$value;
		}
	}
	public static function get($field)
	{
		if(isset(self::$$field))
		{
			return self::$$field;
		}
	}
}
