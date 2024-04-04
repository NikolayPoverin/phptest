<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Basoop extends Model
{
	//https://app.swaggerhub.com/apis/basip/panel-web-api/2.3.0#/admin/post_desktop_message
	
	//public $baseurl = 'http://10.200.20.2/api';
	public $baseurl = '';
	
	public $account_type = '-';
	
	public $server_type = 'bas';
	
	public $statusOnline = false;
	
	public $tokenTTT='';
	
	
	public $id_dev='';
	public $id_ctrl='';
	public $id_dev_door0='';
	public $id_dev_door1='';
	public $name='';
	public $device_model='';
	public $framework_version='';
	public $device_name='';
	public $firmware_version='';
	public $api_version='';
	
	public function init_dev($id)
	{
		$this->id_dev=$id;
		$sql='select d.name, d.id_ctrl, d2.id_dev as id_dev_door0,d3.id_dev as id_dev_door1, bp.intvalue as IP from device d
				join bas_param bp on d.id_dev=bp.id_dev
				join device d2 on d2.id_ctrl=d.id_ctrl and d2.id_reader=0
				left join device d3 on d3.id_ctrl=d.id_ctrl and d3.id_reader=1
				where bp.param=\'IP\'
            and bp.id_dev='.$id;

			try {

				$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array();
		
				foreach($query as $key=>$value)
				{
					
					$this->id_ctrl=Arr::get($value, 'ID_CTRL', 0);
					$this->id_dev_door0=Arr::get($value, 'ID_DEV_DOOR0', 0);
					$this->id_dev_door1=Arr::get($value, 'ID_DEV_DOOR1', 0);
					$this->name=Arr::get($value, 'NAME', 0);
					$this->baseurl=long2ip(Arr::get($value, 'IP', 0));
				}
				//Log::instance()->add(Log::DEBUG, '#100 '. $sql.' '. debug::vars($query, $value)); exit;
				//Log::instance()->add(Log::DEBUG, '#29 '. Debug::vars($this->id_ctrl, $this->id_dev_door0, $this->id_dev_door1, $this->baseurl));
								
				//Log::instance()->add(Log::DEBUG, '#29 '.long2Ip(Arr::get($query, 'IP')));

		
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#31 '.$e->getMessage());

		}	
			 //Log::instance()->add(Log::DEBUG, '#37 Создание объекта для IP='.$this->baseurl); 
			 $this->init($this->baseurl);
			
			 return;
	}
	
	public function init ($ip)
	{
		
		$this->baseurl=$ip;
		$auth=$this->makeRequest();
		//Log::instance()->add(Log::DEBUG, '76 '.Debug::vars($auth)); exit;
		
		if($auth)
		{
		$this->statusOnline=True;
		$this->account_type=Arr::get($auth, 'account_type');
		$this->tokenTTT=Arr::get($auth, 'token');
		$about=$this->about();
		
		$this->device_model=Arr::get($about, 'device_model');
		$this->framework_version=Arr::get($about, 'framework_version');
		$this->device_name=Arr::get($about, 'device_name');
		$this->firmware_version=Arr::get($about, 'firmware_version');
		$this->api_version=Arr::get($about, 'api_version');
		return TRUE;
			
		} else {
			$this->account_type='no';
			return FALSE;
			
		}
		return;
	
	}
	
	
	public function changeConfigIP ($data)// запись IP адреса в таб
	{
		//echo Debug::vars('38', $data); exit;
		$sql='delete from bas_param bp
				where bp.id_dev='.Arr::get($data, 'id_dev').'
				and bp.param=\'IP\'';
			try {

			$query2 = DB::query(Database::DELETE, $sql)
			->execute(Database::instance('fb'))
			;
			//Log::instance()->add(Log::DEBUG, '#47 Данные из набора '.Debug::vars($data).' удалеы успешно перед последующей вставкой. SQL='.$sql);
			
				$sql='INSERT INTO BAS_PARAM (ID_DEV, PARAM, INTVALUE) VALUES ('.Arr::get($data, 'id_dev').', \'IP\',  \''.ip2long(Arr::get($data, 'new_IP')).'\');';
				try{
					$query2 = DB::query(Database::INSERT, $sql)
					->execute(Database::instance('fb'))
						;
					Log::instance()->add(Log::DEBUG, '#54 Данные из набора '.Debug::vars($data).' вставлены успешно. SQL='.$sql);
				}  catch (Exception $e) {
					Log::instance()->add(Log::DEBUG, '#56 вставка данных  из набора '.Debug::vars($data).' прошла с ошибкой. SQL='.$sql.' '. $e->getMessage());
				$query=0;
				//echo Debug::vars('47','exit'); exit;
				$result=1; // т.е. ошибка, надо разбираться.
			}
		
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, '#51 '.$e->getMessage());
				$query=0;
				//echo Debug::vars('47','exit'); exit;
				$result=1; // т.е. ошибка, надо разбираться.
			}
				
				
		
		return $result;
	}
	

	public function getBasDeviceList_0($serverList)// получить id_dev контроллеров (вызывных панелей) типа bas
	{
		
		//echo Debug::vars('15', $serverList); //exit;
		//выбираю ID сервера из полученного массива
		foreach ($serverList as $key=>$value)
		{
			$res[]=Arr::get($value, 'ID_SERVER');
		}
		//echo Debug::vars('21', $res, implode(",",$res)); exit;
		
		$sql='select d.id_dev, d.name, bd.ip, bd.port, bd.connectionstring,bd.dev_version, bd.last_event from device d
				left join bas_device bd on bd.id_dev=d.id_dev
				where d.id_server in ('.implode(",",$res).')
				and d.id_reader is null'; 
				
		try {

				$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array()
				;
				return array ('status'=>0, 'res'=>$query);
		
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#28 '.$e->getMessage());
			
			return array ('status'=>1, 'res'=>$e->getMessage());
		}	
		
	}
	
	public function getBasServerList()// получить id транспортных серверов типа bas
	{
		$sql='select d.* from server d
		    join servertypelist stl on stl.id_server=d.id_server
            join servertype stt on stt.id=stl.id_type
            where stt.sname=\''.$this->server_type.'\''; 
		try {

				$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array()
				;
				return array ('status'=>0, 'res'=>$query);
		
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#28 '.$e->getMessage());
			
			return array ('status'=>1, 'res'=>$e->getMessage());
		}	
		
	}
	
	
	public function fixCardIdxOK($datacard, $result_write )
	{
		//echo Debug::vars('12', $result_write, $datacard); //exit;
		if(Arr::get(Arr::get($result_write,'res'),'uid', '-1')>0)
		{
		 $sql='UPDATE CARDIDX SET
					DEVIDX = '.Arr::get(Arr::get($result_write,'res'),'uid', '-1').',
					LOAD_TIME = \'now\',
					LOAD_RESULT = \'OK\'
				WHERE (ID_CARD = \''.Arr::get($datacard, 'ID_CARD').'\') AND (ID_DEV = '.Arr::get($datacard, 'ID_DEV').')';
		} else {
		 $sql='UPDATE CARDIDX SET
					DEVIDX = '.Arr::get(Arr::get($result_write,'res'),'uid', '-1').',
					LOAD_TIME = \'now\',
					LOAD_RESULT = \'OK\',
					NOTE = \'Identifier with that type and number already exist\'
				WHERE (ID_CARD = \''.Arr::get($datacard, 'ID_CARD').'\') AND (ID_DEV = '.Arr::get($datacard, 'ID_DEV').')';
			
			
		}
		
		//Log::instance()->add(Log::DEBUG, '90 '. Debug::vars($sql));
		try {

				$query = DB::query(Database::UPDATE, $sql)
				->execute(Database::instance('fb'))
				;
				//echo Debug::vars('25',$query); exit;
		
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#28 '.$e->getMessage());
			$query=0;
		}
		
		
		return;
		
	}
	
	
	
	public function fixCardIdxErr($datacard ) // запись сообщения об ошибке в таблицу cardidx
	{
		//echo Debug::vars('198', $datacard); exit;
		if(Arr::get(Arr::get($result_write,'res'),'uid', '-1')>0)
		{
		 $sql='UPDATE CARDIDX SET
					DEVIDX = '.Arr::get(Arr::get($result_write,'res'),'uid', '-1').',
					LOAD_TIME = \'now\',
					LOAD_RESULT = \'OK\'
				WHERE (ID_CARD = \''.Arr::get($datacard, 'ID_CARD').'\') AND (ID_DEV = '.Arr::get($datacard, 'ID_DEV').')';
		} else {
		 $sql='UPDATE CARDIDX SET
					DEVIDX = '.Arr::get(Arr::get($result_write,'res'),'uid', '-1').',
					LOAD_TIME = \'now\',
					LOAD_RESULT = \'ERR\',
					NOTE = \'Identifier with that type and number already exist\'
				WHERE (ID_CARD = \''.Arr::get($datacard, 'ID_CARD').'\') AND (ID_DEV = '.Arr::get($datacard, 'ID_DEV').')';
			
			
		}
		
		//Log::instance()->add(Log::DEBUG, '90 '. Debug::vars($sql));
		try {

				$query = DB::query(Database::UPDATE, $sql)
				->execute(Database::instance('fb'))
				;
				//echo Debug::vars('25',$query); exit;
		
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#28 '.$e->getMessage());
			$query=0;
		}
		
		
		return;
		
	}
	
	public function fixCardIdxErrNoConnect($messErr, $id_dev ) // запись сообщения об ошибке messErr в таблицу cardidx для контроллерв, принадлежащим контроллеру шв_вумпри отсутствии связи
	{
		//echo Debug::vars('198', $messErr, $id_dev); exit;
		//echo Debug::vars('240', $messErr); //exit;

		 $sql='UPDATE CARDIDX SET
					
					LOAD_TIME = \'now\',
					LOAD_RESULT =\''.Text::limit_chars( 'ERR '.str_replace("'", " ",  $messErr), 100).'\',
					NOTE = \''.Text::limit_chars(str_replace("'", " ",  $messErr), 100).'\'
				WHERE  (ID_DEV in (select d2.id_dev from device d
					join device d2 on d2.id_ctrl=d.id_ctrl
					where d.id_dev='.$id_dev.')
					and (ID_DB=1))';
					
		$sql2='UPDATE CARDINDEV
		SET ATTEMPTS = 97
		WHERE (id_dev in(select d2.id_dev from device d
								join device d2 on d2.id_ctrl=d.id_ctrl
								where d.id_dev='.$id_dev.')
								and (ID_DB=1))';
					
		
	
		Log::instance()->add(Log::DEBUG, '251 '. $sql.' '. $sql2);
		
		try {

				$query = DB::query(Database::UPDATE, $sql)
				->execute(Database::instance('fb'))
				; 
				$query = DB::query(Database::UPDATE, $sql2)
				->execute(Database::instance('fb'))
				; 
				//echo Debug::vars('25',$query); exit;
		
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#273 '.$e->getMessage());
			$query=0;
		}
		
		
		return 0;
		
	}
	
	public function delFromCardindev($datacard)
	{
		//echo Debug::vars('108', $datacard); //exit;
		$sql='delete from cardindev cd
				where cd.id_cardindev='.Arr::get($datacard,'ID_CARDINDEV');
				//echo Debug::vars('62',$sql, $datacard); exit;
				try {

				$query2 = DB::query(Database::DELETE, $sql)
				->execute(Database::instance('fb'))
				;
				//echo Debug::vars('42',$query2); exit;
				$result=0; // т.е. все хорошо.
		
				} catch (Exception $e) {
					Log::instance()->add(Log::DEBUG, '#28 '.$e->getMessage());
					$query=0;
					//echo Debug::vars('47','exit'); exit;
					$result=1; // т.е. ошибка, надо разбираться.
				}
			
	}
	
	
	public function getCardList($id_dev)
	{
			
		$sql='select cd.id_cardindev, cd.id_card, cd.id_dev,cd.operation from cardindev cd
            join device d on d.id_dev=cd.id_dev
            join device d2 on d2.id_ctrl=d.id_ctrl and d2.id_reader is null
            where d2.id_dev='.$id_dev;
			
			
		//Log::instance()->add(Log::DEBUG, '210 выборка номеров карт для обработки SQL='. $sql);
		try {

				$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array();
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, '#359 '. $e->getMessage());
			$query=array();
		} 
		
		return $query;
	}
	
	
	
	
	public function makeRequest()// авторизация
	{
		//$this->testParking();
		
		$user='admin';
		$pass='171120';
		//$pass='2644256';
		$token=$this->getToken($pass);
		//echo Debug::vars('183', $this->baseurl); exit;
		//Log::instance()->add(Log::DEBUG, '245 Отладка авторизацию $this->baseurl='.$this->baseurl);
		//Log::instance()->add(Log::DEBUG, '247 Адрес авторизацию http://'.$this->baseurl.'/api/v1/login?username='.$user.'&password='.$token);
		//$request = Request::factory('http://10.200.20.2/api/v1/login?username='.$user.'&password='.$token)
		//$request = Request::factory('http://'.$this->baseurl.'/api/v1/login?username='.$user.'&password='.$token)
		$request = Request::factory('http://'.$this->baseurl.'/api/v1/login')
			->query(array('username'=>$user, 'password'=>$token))
			->headers("Accept", "application/json")
			//-> method(Request::GET);
			->headers("Authorization", 'Bearer ')
			-> method('GET');
		try{
		$response=$request->execute();
		//Log::instance()->add(Log::DEBUG, '17 '.Debug::vars($request, json_decode ($response->body(), true)));		
			$this->statusOnline=true;		
			return json_decode ($response->body(), true);
		} catch (Kohana_Request_Exception $e){
			Log::instance()->add(Log::DEBUG, '253 Проблема с авторизацией'.$e->getMessage());	

			return array('account_type'=>'no', 'desc'=>$e->getMessage());
			
		}
		
	}
	
	public function getToken($pass)
	{
		return md5($pass);
	}
	
	public function about()
	{
	
		$request = Request::factory('http://'.$this->baseurl.'/api/info')
		//$request = Request::factory('http://10.200.20.2/api/info')
				->headers("Accept", "application/json")
				-> method(Request::GET);
				//->headers("Authorization", $token);
		try
		{	
			$response=$request->execute();
			return json_decode ($response->body(), true);
		} catch (Kohana_Request_Exception $e) {
			$this->statusOnline=False;
			return $e->getMessage();
		}
	}
	
	
	public function addCard($card)
	{		

	$data=json_encode(array(
		'identifier_owner'=>array(
				'name'=>$card,
				'type'=>'owner'),
		'identifier_type'=>'card',
		'identifier_number'=>ltrim($card, '0'),
		'lock'=>'all'));
		try{
			//Log::instance()->add(Log::DEBUG, '49 '. Debug::vars($data, $token));
			$request = Request::factory('http://'.$this->baseurl.'/api/v1/access/identifier')
					->headers("Accept", "application/json")
					->headers("Content-Type", "application/json")
					->headers("Authorization", 'Bearer '.$this->tokenTTT)
					->method('POST')
					->body($data)
					->execute();
		
			//Log::instance()->add(Log::DEBUG, '94 card='.$card.' '. Debug::vars(json_decode ($request->body(), true)));
			//return json_decode ($request->body(), true);
			return array('status'=>$request->status(), 'res'=>json_decode ($request->body(), true));
		} catch (Kohana_Request_Exception $e) {
				return array ('status'=>0, 'res'=>$e->getMessage());
		}
	}
	
	
	public function getInfoCard($card)// попытка получить uid по номеру карты
	{		

 		$request = Request::factory('http://'.$this->baseurl.'/api/v1/access/identifier/items?filter_field=identifier_number&filter_type=equal&filter_format=string&filter_value='.$card) //
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.$this->tokenTTT)
				->method('GET')
				//->body($data)
				->execute();
	
		//echo Debug::vars('42', json_decode ($request->body(), true)); exit;
		//Log::instance()->add(Log::DEBUG, '59 '. Debug::vars(json_decode ($request->body(), true)));
		//return Arr::get(Arr::get(Arr::get(json_decode ($request->body(), true), 'list_items'), 0), 'identifier_uid');
		return array('status'=>$request->status(), 'res'=>Arr::get(Arr::get(Arr::get(json_decode ($request->body(), true), 'list_items'), 0), 'identifier_uid'));
	}
	
	
	public function getInfoUID($uid)// попытка получить информацию по пользователе по uid
	{		
		//$request = Request::factory('http://10.200.20.2/api/v1/access/identifier/item/'.$uid) //
		$request = Request::factory('http://'.$this->baseurl.'/api/v1/access/identifier/item/'.$uid) //
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.$this->tokenTTT)
				->method('GET')
				//->body($data)
				->execute();
	
		//echo Debug::vars('42', json_decode ($request->body(), true)); exit;
		//Log::instance()->add(Log::DEBUG, '59 '. Debug::vars(json_decode ($request->body(), true)));
		//return Arr::get(Arr::get(Arr::get(json_decode ($request->body(), true), 'list_items'), 0), 'identifier_uid');
		return array('status'=>$request->status(), 'res'=>Debug::vars(json_decode ($request->body(), true),$uid));
	}
	
	
	public function delCard($uid)
	{		
	// DELETE /api/v1/access/identifier/item/2 HTTP/1.1

		//$request = Request::factory('http://10.200.20.2/api/v1/access/identifier/item/'.$uid)
		$request = Request::factory('http://'.$this->baseurl.'/api/v1/access/identifier/item/'.$uid)
			->headers("Accept", "application/json")
			->method(Request::DELETE)
			->headers("Authorization", 'Bearer '.$this->tokenTTT);
			try{
			$response=$request->execute();
  
			//Log::instance()->add(Log::DEBUG, '136 карта iud='.$uid.' удалена успешно. '. Debug::vars($response->status()));
			//Log::instance()->add(Log::DEBUG, '372 iud='.$uid.' '. Debug::vars(json_decode ($request->body(), true))."\r\n\r\n");
			//echo Debug::vars('42',$response->status(), json_decode ($response->body(), true)); //exit;
			//Log::instance()->add(Log::DEBUG, '59 '.Debug::vars($response->status()).' '. Debug::vars(json_decode ($request->body(), true)));
			return array('status'=>$response->status(), 'res'=>json_decode ($response->body(), true));
		} catch (Kohana_Request_Exception $e) {
				return array ('status'=>0, 'res'=>$e->getMessage());
		}
	}
	
	
	
	public function getLastEventID()// получить метку времени последнего события
	{
		$sql='select bp.strvalue as LASTEVENT from bas_param bp
			where bp.id_dev='.$this->id_dev.' and bp.param=\'LASTEVENT\'';
		
		$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->get('LASTEVENT');
		//Log::instance()->add(Log::DEBUG, '#494 Получен lastvent '.$query.' SQL='.$sql);
		return $query;

	}		

	public function setLastEventID($eventid)// установить метку времени последнего события
	{
		//удаляю старый setLastEventID
		$sql='delete from bas_param bp
			where bp.id_dev='.$this->id_dev.' and bp.param=\'LASTEVENT\'';
		
			try {

			$query2 = DB::query(Database::DELETE, $sql)
			->execute(Database::instance('fb'))
			;
			
			//Log::instance()->add(Log::DEBUG, '#504 Старый as event удален успешно. SQL='.$sql);
				$sql='INSERT INTO BAS_PARAM (ID_DEV, PARAM, STRVALUE) VALUES ('.$this->id_dev.', \'LASTEVENT\',  \''.$eventid.'\');';
				try{
					$query2 = DB::query(Database::INSERT, $sql)
					->execute(Database::instance('fb'))
						;
						//Log::instance()->add(Log::DEBUG, '#504 Новый lastvent вставлен успешно. SQL='.$sql);
					
				}  catch (Exception $e) {
					//Log::instance()->add(Log::DEBUG, '#513 вставка данных  lastvent прошла с ошибкой. SQL='.$sql.' '. $e->getMessage());
				$query=0;
				//echo Debug::vars('47','exit'); exit;
				$result=1; // т.е. ошибка, надо разбираться.
			}
		
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, '#51 Удаление старого lastevent произошло с ошибкой '.$e->getMessage());
				$query=0;
				//echo Debug::vars('47','exit'); exit;
				$result=1; // т.е. ошибка, надо разбираться.
			}
				
				
		
		return $result;
		return 0;

	}		

	
	/* 
	$param - имя параметра
	$intvalue - параметр формата int
	$strvalue - параметр формата string
	*/
	public function saveParam($param, $intvalue, $strvalue)// запись (обновление) параметра в таблице bas_param 8.03.2023
	{
		//удаляю старое значение параметра
		$sql='delete from bas_param bp
			where bp.id_dev='.$this->id_dev.' and bp.param=\''.$param.'\'';
		
			try {

			$query2 = DB::query(Database::DELETE, $sql)
			->execute(Database::instance('fb'))
			;
			
			if(is_null($intvalue)) $sql='INSERT INTO BAS_PARAM (ID_DEV, PARAM, INTVALUE, STRVALUE) VALUES ('.$this->id_dev.', \''.$param.'\', NULL,\''.$strvalue.'\')';
			if(is_null($strvalue)) $sql='INSERT INTO BAS_PARAM (ID_DEV, PARAM, INTVALUE, STRVALUE) VALUES ('.$this->id_dev.', \''.$param.'\', '.$intvalue.', NULL)';
				//Log::instance()->add(Log::DEBUG, '#580. SQL='.$sql);
				try{
					$query2 = DB::query(Database::INSERT, $sql)
					->execute(Database::instance('fb'))
						;
						
					
				}  catch (Exception $e) {
					
				$query=0;
				//echo Debug::vars('47','exit'); exit;
				$result=1; // т.е. ошибка, надо разбираться.
			}
		
			} catch (Exception $e) {
				Log::instance()->add(Log::DEBUG, '#51 Удаление старого lastevent произошло с ошибкой '.$e->getMessage());
				$query=0;
				//echo Debug::vars('47','exit'); exit;
				$result=1; // т.е. ошибка, надо разбираться.
			}
				
				
		
		return $result;
		return 0;

	}		

	public function EventInsert($id_db=1, $id_eventtype = NULL, $id_cntrl= NULL, $id_reader= NULL, $note= NULL, $time= NULL, $id_video= NULL, $id_user= NULL, $ess1= NULL, $ess2= NULL, $idsource= NULL, $idserverts= NULL)// вставка события от устройства
	{
		
		//EXECUTE PROCEDURE DEVICEEVENTS_INSERT(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)
		//$sql="EXECUTE PROCEDURE DEVICEEVENTS_INSERT(1, ".Arr::get($eventcode, 'eventName').", ".$this->id_ctrl.", ".$this->id_reader.", '000001001A', 'now', NULL, 1, 2, 1, NULL, NULL)";// выполняется, событие вставляется, но не происходит автообновление Монитора. Надо обновлять вручную.
		$sql="EXECUTE PROCEDURE DEVICEEVENTS_INSERT(".$id_db.", ".$id_eventtype.", ".$id_cntrl.", ".$id_reader.", '".$note."', '". $time."', NULL, NULL, NULL, 1, ".$idsource.", ".(int)($idserverts / 1000).")";// выполняется, событие вставляется, но не происходит автообновление Монитора. Надо обновлять вручную.
			
		//Log::instance()->add(Log::DEBUG, '#562 sql=  '.$note.' , '.$sql);	//exit;
			try {

			DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->as_array();
			
			Log::instance()->add(Log::DEBUG, '#569 Событие вставлено успешно. SQL='.$sql);
			$result=1;
			} catch (Exception $e) {
				//Log::instance()->add(Log::DEBUG, '#572 Событие вставлено с ошибкой с ошибкой '.$e->getMessage());
				$query=0;
				
				$result=1; // т.е. ошибка, надо разбираться.
			}
				
				
		
		return $result;
	}		

	public function getEvent()// получить последние события
	{
		
		
		$from=$this->getLastEventID();// получить дату последного прочитанного события.
		Log::instance()->add(Log::DEBUG, '#545 Получение событий начиная с метки '.date ('d.m.Y H:i:s',$from/1000).' ('.$from.')');
		return $this->getLog($from, 50);

	}		

	public function getLog($from = 0, $limit=50)// получить лог-файл
		{
			
		//	https://virtserver.swaggerhub.com/basip/panel-web-api/2.3.0/log/items?locale=en&from=1549286120038&to=1549286120038&limit=20&page_number=2&sort_type=asc&filter_field=category&filter_type=equal&filter_value=access
			
			$request = Request::factory('http://'.$this->baseurl.'/api/v1/log/items')
				->query(array(
					//'from'=>1549286120038,
					'from'=>$from,
					'limit'=>$limit,
					'page_number' => 2,
					'sort_type' => 'asc',
					'filter_field'=>'category', 
					'filter_type'=>'equal', 
					'filter_value'=>'access'))
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.$this->tokenTTT)
				->method('GET')
				->execute();
			//return json_decode ($request->body(), true);
			//Log::instance()->add(Log::DEBUG, 'event-495 request log '. Debug::vars($request));
			//Log::instance()->add(Log::DEBUG, 'event-496 Ожидаю события '. Debug::vars($request->body()));
			return Arr::get(json_decode ($request->body(), true), 'list_items');
		}
	
	public function getTime()// прочитать время
		{
				$request = Request::factory('http://'.$this->baseurl.'/api/v1/device/time')
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.$this->tokenTTT)
				->method('GET')
				->execute();
		//return json_decode ($request->body(), true);
		return json_decode ($request->body(), true);
		}
	
	
	public function setTime()// установить время
		{
		
		$ntp=$this->getNTPStatus();
		if(!Arr::get($ntp, 'enabled'))
		{
		$data='{
				  "timezone": "UTC+03:00",
				  "time_unix": '.time().'
				}';	
			
			$request = Request::factory('http://'.$this->baseurl.'/api/v1/network/timezone/manual')
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.$this->tokenTTT)
				->method('POST')
				->body($data)
				->execute();
				return json_decode ($request->body(), true);
		} else {
			Log::instance()->add(Log::DEBUG, '#677 Установка времени невозможна. Для установки времени необходимо выключить NTP в настройках устройства.');
			return 1;
		}
		
		
		}
		
	public function getNTPStatus()// получить статус NTP
		{
		
		$data='{
				  "timezone": "UTC+03:00",
				  "time_unix": '.time().'
				}';	
			
			$request = Request::factory('http://'.$this->baseurl.'/api/v1/network/ntp')
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.$this->tokenTTT)
				->method('GET')
				->body($data)
				->execute();
		
		return json_decode ($request->body(), true);
		}
		
	
	
}


