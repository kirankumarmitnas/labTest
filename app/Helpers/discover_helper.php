<?php 
if(function_exists('idConversion')!==true)
{
function idConversion($options=false)
{
		$response=0;
		$type=checkVariable($options['type'], 0,'intval');
		$userID=checkVariable($options['userID'],'','trim');
		$sesName=checkVariable($options['sesName'],'','trim');
		$sesType=checkVariable($options['sesType'], 0,'intval');
		$encrypter = \Config\Services::encrypter();
		if(empty($userID) || strlen($userID)<=0)
		{
			//$key =bin2hex(\CodeIgniter\Encryption\Encryption::createKey(32));
			$userData=login(array('sesName'=>$sesName,'category'=>2,'type'=>$sesType)); 
			$userID=(isEmptyArray($userData)) ? checkVariable($userData['userID'],'','trim') : '';
		}
		if(!empty($userID))
		{
			try
			{
				if($type==1)
				{
					$response=$encrypter->encrypt($userID);
				}
				else
				{
					$response=$encrypter->decrypt($userID);
				}
			}
			catch(\Exception $e)
			{
				$response=0;
			}
		}
		return $response;
}
}
if(function_exists('searchMenu')!==true)
{
function searchMenu($options)
{
		$response=0;
		$menuIDS=checkVariable($options['menuIDS'],0);
		$field=checkVariable($options['field'],0);
		$search=checkVariable($options['search'],0,'trim');
		if(isEmptyArray($menuIDS)>0 && !empty($field))
		{
			$response=array();
			foreach($menuIDS as $menu)
			{
				if(isEmptyArray($menu)>0 && isset($menu[$field]) && $menu[$field]==$search)
				{
					$response[]=$menu;
				}
			}
		}
		return $response;
	}
}
if(function_exists('login')!==true)
{
function login($options)
{
	$response=0;
	$type= checkVariable($options['type'],0,'intval');
	$category=checkVariable($options['category'],0,'intval');
	$sesName=checkVariable($options['sesName'],'','trim');
	$redirect=checkVariable($options['redirect'],'','trim');
	$isReturn=checkVariable($options['isReturn'],1,'intval');
	$session = \Config\Services::session(); 
	if(!empty($sesName))
	{
		$userData= $session->has($sesName) ? $session->get($sesName) : 0;
		$userType=(isEmptyArray($userData)>0) ? checkVariable($userData['userType'],0,'intval') : 0;
		switch($category)
		{
		   case $category==1:
		   {
			   $data=isset($options['data']) ? $options['data'] : 0;
			   if(!empty($data) || (isEmptyArray($data)>0))
			   {
					$session->set($sesName, $data);
					$response=1;
			   }	
			   break;
		   }
		   case $category==2:
		   {
				if(isEmptyArray($userData)>0)
				{
					$response=($isReturn==0) ?  $userType : $userData;
				}
				break;
		   }
		   case $category==-1:
		   {
				if(isEmptyArray($userData)>0)
				{
					$session->remove($sesName);
					$response=1;
				}
				break;
		   }
		   case $category==-2:
		   {
				if(isEmptyArray($userData)>0)
				{
					$userType=checkVariable($userData['userType'],0,'intval');
					$session->remove($sesName);
					$response=1;
				}
				break;
		   }
		   default:
		   {
			   break;
		   }
		}
	}
	/*if(!empty($redirect) && ( $category<=0 ||  ($category>0  && ($response==0 || ($isReturn==1 && isEmptyArray($response)<=0)))))
	{	
		//return redirect()->to($redirect); 
	}*/
	return $response;
}
}
if(function_exists('customSession')!==true)
{
function customSession($options)
{
	$response=0;
	$category=checkVariable($options['category'],0,'intval');
	$data=checkVariable($options['data'], null);
	$sesName=checkVariable($options['sesName'],'','trim');
	if(!empty($sesName))
	{
		if(!empty($sesName))
		{
			$session = \Config\Services::session(); 
			$userData= $session->has($sesName) ? $session->get($sesName) : 0;
			switch($category)
			{
			   case $category==1:
			   {
				   $data=checkVariable($options['data'],0);
				   if(!empty($data) || (isEmptyArray($data)>0))
				   {
						$session->set($sesName, $data);
						$response=1;
				   }	
				   break;
			   }
			   case $category==2:
			   {
					if(!empty($userData) || (isEmptyArray($userData)>0))
					{
						$response=$userData;
					}
					break;
			   }
			   case $category==-1:
			   {
					$session->remove($sesName);
					$response=1;
					break;
			   }
			   default:
			   {
				   break;
			   }
			}
		}
	}
	return $response;
}
}	
if(function_exists('getColumnName')!==true)
{
 function getColumnName($options)
{
		$response=0;
		$totalFields=isset($options['totalFields']) ? $options['totalFields'] : 0;
		if($totalFields>0){
		$columns=range('A','Z');
		$totalColumn=count($columns);
		$divide=$totalFields/$totalColumn;$ids=array();$id=0;$indx=0;$b_index=0;
		for($i=0;$i<$totalFields;$i++){ if(($divide>0 && $id>=$totalColumn) || $b_index>0) { $rsl=($i%$totalColumn);if($rsl==0){$id=0;$b_index+=1;if($b_index>1){$indx+=1;}}
		$columnName2=isset($columns[$indx]) ? trim($columns[$indx]) : '';$columnName=isset($columns[$id]) ? trim($columns[$id]) : '';
		if(!empty($columnName) && !empty($columnName2)){$ids[]=$columnName2.$columnName;}}
		else{$columnName=isset($columns[$id]) ? trim($columns[$id]) : '';if(!empty($columnName)){$ids[]=$columnName;}}$id+=1;}if(is_array($ids)==true && count($ids)>0){$response=$ids;}
		}
		return $response;
}
}
if(function_exists('getBrowserID')!==true)
{
function getBrowserID($options)
{
	$response=0;
	$time=isset($_SERVER['REQUEST_TIME_FLOAT']) ? doubleval($_SERVER['REQUEST_TIME_FLOAT']) : '';
	if(!empty($time))
	{
		list($whole, $decimal) = explode('.', $time);
		$response=uniqid('BRS').sprintf("%04d",$decimal);
	}
	return $response;
}
}
if(function_exists('getDomain')!==true)
{
function getDomain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
		return $regs['domain'];
  }
  return false;
}
}
if(function_exists('makeDir')!==true)
{
function makeDir($path){
if(is_dir($path)){ }else{
if (!mkdir($path, 0755, true)){ die('Failed to create folders...'); return false;}
else{ return true;}}
}
}

if(function_exists('getKeysValue')!==true)
{
function getKeysValue($options)
{
	$response=0;
	$list=isset($options['list']) ? $options['list'] : 0;
	$type=isset($options['type']) ? $options['type'] : 0;
	$field=isset($options['field']) ? $options['field'] : '';
	$search=isset($options['search']) ? $options['search'] : '';
	if(is_array($list)==true && count($list)>0 && !empty($field) && ((is_array($search)==true && count($search)>0) || $search!=''))
	{
		if($type==1)
		{
			$response=array();
			foreach ($list as $key => $value) {
				foreach ($search as $k => $v) { 
					if (!isset($value[$k]) || $value[$k] != $v) 
					{ 
						continue 2; 
					} 
				} 
				$response[] = $value; 
			} 
			
		}
		else
		{
			$field=trim($field);
			$response=array();
			foreach($list  as $info)
			{
				$value=isset($info[$field]) ? trim($info[$field]) : '';
				if($value!='')
				{
					if(is_array($search)==true && count($search)>0)
					{
						$search=array_unique($search);
						$is_exists=0;
						foreach($search  as $s_find)
						{
							if($s_find!='' && $value==$s_find)
							{
								$is_exists=1;
							}
						}
						if($is_exists==1)
						{
						$response[]=$info;
						}
					}
					else
					{
						if($value==$search)
						{
							$response[]=$info;
						}
					}
				}
			}
		}
	}
	return $response;
}
}
if(function_exists('checkVariable')!==true)
{
function checkVariable(&$value,$default=false,$fun=false)
{
    $response=null;
	if(!$fun){ $response=isset($value) ? $value : $default;}
	else
	{
	if(function_exists($fun)==true && isEmptyArray($value)<=0){	
	if(is_object($value)==false)
	{
	$response=isset($value) ? $fun($value) : $default; 
	}
	else
	{
		$response==isset($value) ? $fun($value) : $default; 
	}
	}
	else{ $response=isset($value) ? $value : $default;	}
	}
    return $response;
}
}
if(function_exists('isEmptyArray')!==true)
{
function isEmptyArray(&$var)
{
	$response=0;
	if(isset($var) && is_array($var)==true && count($var)>0)
	{
		$response=count($var);
	}
	return $response;
}
}
if(function_exists('getPerticularValues')!==true)
{
function getPerticularValues($options)
{
	$response=0;
	$data=isset($options['data']) ? $options['data'] : 0;
	$get_fields=isset($options['fields']) ? $options['fields'] : 0;
	if(is_array($data)==true && count($data)>0 && is_array($get_fields)==true && count($get_fields)>0)
	{
		$response=array();
		foreach($get_fields as $field)
		{
			if(!empty($field) && isset($data[$field]))
			{
				$response[$field]=$data[$field];
			}
		}				
	}
	return $response;
}
}
if(function_exists('findKey')!==true)
{
function findKey($array, $keySearch)
{
    if (!is_array($array)) return false;
    if (array_key_exists($keySearch, $array)) return true;
    foreach($array as $key => $val)
    {
        if (findKey($val, $keySearch)) return true;
    }

    return false;
}
}
if(function_exists('searchValueInArray')!==true)
{
function searchValueInArray($options)
{
$data=checkVariable($options['data'],0);
$search=checkVariable($options['search'],0);
$withKey=checkVariable($options['withKey'],0,'intval');
$type=checkVariable($options['type'],0);
$isTime=checkVariable($options['isTime'],0);
$output=checkVariable($options['output'],0);
$isSingle=checkVariable($options['isSingle'],0);
$totalColumns=isEmptyArray($search);
$response=0;
if(isEmptyArray($data)>0 && $totalColumns>0)
{
$response =array();
$result=array();
foreach($search as $key=>$value)
{
if (findKey($data,$key)==true) 
{
$cmp= array_column($data,$key);
if($isTime==1 && is_numeric($value)==true)
{
$cmp=array_map(function($conVal){ if(strtotime($conVal)){ return strtotime($conVal);} else { return $conVal; } },$cmp);
}

$found=array_intersect_key($data, array_intersect($cmp, [$value]));
$foundKeys=array_keys($found);
if(isEmptyArray($foundKeys)>0)
{
$result[$key]=$foundKeys;
}
}
}
if(isEmptyArray($result)>0)
{
if($type==1)
{
if(isEmptyArray($result)==$totalColumns)
{	
$totalKeys=array_keys($result);
$totalKeys=isEmptyArray($totalKeys);
$finalOutput=array();
if($totalKeys>0)
{	
	if($totalKeys>1)
	{
		foreach($result as $key=>$value)
		{
			if(isEmptyArray($value)>0)
			{
				foreach($value as $findValue)
				{
					$found=1;
					foreach($result as $key2=>$value2)
					{
						if($key!=$key2)
						{
							if(in_array($findValue,$value2)==true)
							{
								$found++;
							}
						}
					}
					if($found==$totalKeys && in_array($findValue,$finalOutput)==false)
					{
						$finalOutput[]=$findValue;
					}
				}
				
			}
		}
	}
	else
	{
		foreach($result as $key=>$value)
		{
			if(isEmptyArray($value)>0)
			{
				foreach($value as $key2=>$value2)
				{
					if(in_array($value2,$finalOutput)==false)
					{
						$finalOutput[]=$value2;
					}
				}
				
			}
		}
	}
	
}
if(isEmptyArray($finalOutput))
{
foreach($finalOutput as $value)
{
$output=checkVariable($data[$value],0);
if(isEmptyArray($finalOutput)>0)
{
if($withKey==1)
{
$response[$value]=$output;	
}
else
{
$response[]=$output;
}
}
}
if(isEmptyArray($response)==1 && $isSingle==1)
{
$response =array_values($response)[0];
}
}
}
}
else
{
	
$finalOutput=array();
foreach($result as $key=>$value)
{
$finalOutput=array_merge($finalOutput,$value);
}
if(isEmptyArray($finalOutput)>0)
{
$finalOutput=array_unique($finalOutput);
foreach($finalOutput as $value)
{
$output=checkVariable($data[$value],0);
if(isEmptyArray($finalOutput)>0)
{
if($withKey==1)
{
$response[$value]=$output;	
}
else
{
$response[]=$output;
}
}
}
if(isEmptyArray($response)==1  && $isSingle==1)
{
$response =array_values($response)[0];
}
}


}
}
}
return $response;
}
}
if(function_exists('sortArrayByKeyValue')!==true)
{
function sortArrayByKeyValue($options)
{
	$response=0;
	$data=checkVariable($options['data'],0);
	$field=checkVariable($options['field'],0);
	$type=checkVariable($options['type'],0);
	$isTime=checkVariable($options['isTime'],0);
	if(isEmptyArray($data)>0 && !empty($field))
	{
	$sortData=$data;
	$sortType=($type==1) ? SORT_ASC : SORT_DESC ;
	$position = array_column($sortData, $field);
	if($isTime==1)
	{
		$position=array_map(function($conVal){ return strtotime($conVal); },$position);
	}
	array_multisort($position,$sortType, $sortData);
	$response=$sortData;
	}
	return $response;
}
}
if(function_exists('getArrayKeyValues')!==true)
{
function getArrayKeyValues($options)
{
	$response=0;
	$data=checkVariable($options['data'],0);
	$getFields=checkVariable($options['fields'],0);
	if(isEmptyArray($data)>0 && isEmptyArray($getFields)>0)
	{
		$response=array();
		foreach($getFields as $field)
		{
			if(!empty($field) && isset($data[$field]))
			{
				$response[$field]=$data[$field];
			}
		}
        if(isEmptyArray($data)>0 && isEmptyArray($response)<=0)
		{
			if(isEmptyArray($getFields)==1)
			{
				$field=reset($getFields);
				$response=array_column($data,$field);
			}
			else
			{
				foreach($data as $info)
				{
					if(isEmptyArray($info)>0)
					{
						$response[]=getArrayKeyValues(array('data'=>$info,'fields'=>$getFields));
					}
				}
			}
		}		
	}
	return $response;
}
}
if(function_exists('convertIntoIndianRupesh')!==true)
{
function convertIntoIndianRupesh($amount) 
{
	$response=0;
	if($amount>0)
	{
	$response=preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $amount);
	}
	return $response;
}
}

if(function_exists('numberToWords')!==true)
{
function numberToWords($number) 
{
		$no = round($number);
		$decimal = round($number - ($no = floor($number)), 2) * 100;    
		$digits_length = strlen($no);    
		$i = 0;
		$str = array();
		$words = array(
			0 => '',
			1 => 'One',
			2 => 'Two',
			3 => 'Three',
			4 => 'Four',
			5 => 'Five',
			6 => 'Six',
			7 => 'Seven',
			8 => 'Eight',
			9 => 'Nine',
			10 => 'Ten',
			11 => 'Eleven',
			12 => 'Twelve',
			13 => 'Thirteen',
			14 => 'Fourteen',
			15 => 'Fifteen',
			16 => 'Sixteen',
			17 => 'Seventeen',
			18 => 'Eighteen',
			19 => 'Nineteen',
			20 => 'Twenty',
			30 => 'Thirty',
			40 => 'Forty',
			50 => 'Fifty',
			60 => 'Sixty',
			70 => 'Seventy',
			80 => 'Eighty',
			90 => 'Ninety');
		$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
		while ($i < $digits_length) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += $divider == 10 ? 1 : 2;
			if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? '' : null;            
				$str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural;
			} else {
				$str [] = null;
			}  
		}
		$Rupees = implode(' ', array_reverse($str));
		$paise = ($decimal) ? " And Paise " . ($words[$decimal - $decimal%10]) ." " .($words[$decimal%10])  : '';
		return ($Rupees ?  $Rupees.' Rupees' : '') . $paise . " Only";
}	
}
if(function_exists('getUrlSegment')!==true)
{
function getUrlSegment($url='',$type=0,$total=0,$returnType=0,$separator='')
{
	$response=0;
	if(!empty($url))
	{
		$url = str_replace('\\', '/', $url);
		$url = str_replace('../', '', $url);
		$path = parse_url($url, PHP_URL_PATH);
		$segments = explode('/', $path);
		if($type==1)
		{
			$response=array_slice($segments,0,$total);
		}
		else
		{
			$total=$total * -1;
			$response=array_slice($segments,$total);
		}
		if($returnType==1)
		{
		    
		    if(empty($separator))
		    {
		    $separator=DIRECTORY_SEPARATOR;
		    }
			$response=join($separator,$response);
		}
	}
	return $response;
}
}