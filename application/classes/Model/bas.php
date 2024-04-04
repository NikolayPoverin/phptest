<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Bas extends Model
{
	//https://app.swaggerhub.com/apis/basip/panel-web-api/2.3.0#/admin/post_desktop_message
	
	public $baseurl = 'http://10.200.20.2/api';
	
	public $account_type = 'admin';
	
	public $server_type = 'bas';
	
	public $statusOnline = false;
	
	public $tokenTTT='';
	
	
	public function changeConfigIP ($data)// запись IP адреса в таб
	{
		//echo Debug::vars('38', $data); exit;
		$t1=microtime(1);
		$sql='delete from bas_param bp
				where bp.id_dev='.Arr::get($data, 'id_dev').'
				and bp.param=\'IP\'';
			try {

			$query2 = DB::query(Database::DELETE, $sql)
			->execute(Database::instance('fb'))
			;
			Log::instance()->add(Log::DEBUG, '#47 Данные из набора '.Debug::vars($data).' удалеы успешно перед последующей вставкой. SQL='.$sql);
			Log::instance()->add(Log::DEBUG, '#t6-time '.(microtime(1)-$t1));
			
				$sql='INSERT INTO BAS_PARAM (ID_DEV, PARAM, INTVALUE) VALUES ('.Arr::get($data, 'id_dev').', \'IP\',  \''.ip2long(Arr::get($data, 'new_IP')).'\');';
				//echo Debug::vars('38', $sql); exit;
				try{
					$query2 = DB::query(Database::INSERT, $sql)
					->execute(Database::instance('fb'))
						;
					Log::instance()->add(Log::DEBUG, '#54 Данные из набора '.Debug::vars($data).' вставлены успешно. SQL='.$sql);
					Log::instance()->add(Log::DEBUG, '#84-time '.(microtime(1)-$t1));
					$result=0;
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
				
		Log::instance()->add(Log::DEBUG, '#99-time '.(microtime(1)-$t1));		
		
		return $result;
	}
	public function getBasDeviceList($serverList = null)// получить id_dev контроллеров (вызывных панелей) типа bas
	{
		if(is_null($serverList))
		{
			$sql='select d.id_dev, d.name, bp.param, bp.intvalue as IP, bp2.strvalue as LASTEVENT, bp3.strvalue as ABOUT, bp2.INSERTTIME as INSERTTIME, bp4.strvalue as LOGIN, bp5.strvalue as PASS from  servertype st
                join servertypelist stp on stp.id_type=st.id
                join device d on d.id_server=stp.id_server and d.id_reader is null
                left join bas_param bp on bp.id_dev=d.id_dev  and bp.param=\'IP\'
                left join bas_param bp2 on bp2.id_dev=d.id_dev  and bp2.param=\'LASTEVENT\'
				left join bas_param bp3 on bp3.id_dev=d.id_dev  and bp3.param=\'ABOUT\'
				 left join bas_param bp4 on bp4.id_dev=d.id_dev  and bp4.param=\'login\'
				 left join bas_param bp5 on bp5.id_dev=d.id_dev  and bp5.param=\'password\'
                where st.sname =\'bas\'
				order by d.id_dev';
				
		} else {
		
		//выбираю ID сервера из полученного массива
			foreach ($serverList as $key=>$value)
			{
				$res[]=Arr::get($value, 'ID_SERVER');
			}
			//echo Debug::vars('21', $res, implode(",",$res)); exit;
			
			$sql='select d.id_dev, d.name,bp.param, bp.param, bp.intvalue as IP from device d
					left join bas_param bp on bp.id_dev=d.id_dev
					where d.id_server in ('.implode(",",$res).')
					and d.id_reader is null'; 
					
		} 		
		//echo Debug::vars('49', $sql); exit;
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
		 $sql='UPDATE CARDIDX SET
					DEVIDX = '.Arr::get($result_write,'uid', '404').',
					LOAD_TIME = \'now\',
					LOAD_RESULT = \'OK\'
				WHERE (ID_CARD = \''.Arr::get($datacard, 'ID_CARD').'\') AND (ID_DEV = '.Arr::get($datacard, 'ID_DEV').')';
		//echo Debug::vars('19',$result_write, Arr::get($result_write,'res'), $datacard, $sql); exit;
		Log::instance()->add(Log::DEBUG, '90 '. Debug::vars($sql));
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
			
		$sql='select first 200  cd.* from cardindev cd
			where cd.id_dev='.$id_dev;
		try {

				$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array();
		} catch (Exception $e) {
			Log::instance()->add(Log::DEBUG, $e->getMessage());
			$query=array();
		} 
		//echo Debug::vars('26', $query);exit;
		//include 'listCardForDel.txt';
		//$TTT[870846]=array("ID_CARDINDEV"=>870846, "ID_CARD"=>15171348, "ID_DEV"=>7, "OPERATION"=> 1, "ATTEMPTS"=> 0, "ID_CARDTYPE"=> 1);
		//$TTT[870846]=array("ID_CARDINDEV"=>870846, "ID_CARD"=>15171348, "ID_DEV"=>7, "OPERATION"=> 2, "ATTEMPTS"=> 0, "ID_CARDTYPE"=> 1);
		//include 'listCardForWrite.txt';
		//echo Debug::vars('80', $TTT); exit;
		//$query=$TTT;
		return $query;
	}
	
	
	
	
	public function makeRequest()// авторизация
	{
		//$this->testParking();
		
		$user='admin';
		$pass='171120';
		$token=$this->getToken($pass);
		$request = Request::factory('http://10.200.20.2/api/v1/login?username='.$user.'&password='.$token)
			->headers("Accept", "application/json")
			//-> method(Request::GET);
			-> method('GET');
		try{
		$response=$request->execute();
		//Log::instance()->add(Log::DEBUG, '17 '.Debug::vars($request, json_decode ($response->body(), true)));			
		return json_decode ($response->body(), true);
		} catch (Kohana_Request_Exception $e){
			
			return array('account_type'=>'no', 'desc'=>$e->getMessage());
			
		}
	}
	
	public function getToken($pass)
	{
		return md5($pass);
	}
	
	public function about()
	{
	
		$request = Request::factory($this->baseurl.'/info')
		//$request = Request::factory('http://10.200.20.2/api/info')
				->headers("Accept", "application/json")
				-> method(Request::GET);
				//->headers("Authorization", $token);
		try
		{	
			$response=$request->execute();
			return json_decode ($response->body(), true);
		} catch (Kohana_Request_Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	public function addCard($token, $card)
	{		

	$data=json_encode(array(
		'identifier_owner'=>array(
				'name'=>$card,
				'type'=>'owner'),
		'identifier_type'=>'card',
		'identifier_number'=>$card,
		'lock'=>'first'));
		try{
			//Log::instance()->add(Log::DEBUG, '49 '. Debug::vars($data, $token));
			$request = Request::factory('http://10.200.20.2/api/v1/access/identifier')
					->headers("Accept", "application/json")
					->headers("Content-Type", "application/json")
					->headers("Authorization", 'Bearer '.Arr::get($token, 'token'))
					->method('POST')
					->body($data)
					->execute();
		
			//echo Debug::vars('42', json_decode ($request->body(), true)); exit;
			Log::instance()->add(Log::DEBUG, '93 card='.$card.' '. Debug::vars($request->status()));
			Log::instance()->add(Log::DEBUG, '94 card='.$card.' '. Debug::vars(json_decode ($request->body(), true)));
			//return json_decode ($request->body(), true);
			return array('status'=>$request->status(), 'res'=>json_decode ($request->body(), true));
		} catch (Kohana_Request_Exception $e) {
				return array ('status'=>0, 'res'=>$e->getMessage());
		}
	}
	
	
	public function getInfoCard($token, $card)// попытка получить uid по номеру карты
	{		

 		//$request = Request::factory('http://10.200.20.2/api/v1/access/identifier/items?filter_field=identifier_number&filter_type=equal&filter_format=string&filter_value=721003') //
		$request = Request::factory('http://10.200.20.2/api/v1/access/identifier/items?filter_field=identifier_number&filter_type=equal&filter_format=string&filter_value='.$card) //
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.Arr::get($token, 'token'))
				->method('GET')
				//->body($data)
				->execute();
	
		//echo Debug::vars('42', json_decode ($request->body(), true)); exit;
		//Log::instance()->add(Log::DEBUG, '59 '. Debug::vars(json_decode ($request->body(), true)));
		//return Arr::get(Arr::get(Arr::get(json_decode ($request->body(), true), 'list_items'), 0), 'identifier_uid');
		return array('status'=>$request->status(), 'res'=>Arr::get(Arr::get(Arr::get(json_decode ($request->body(), true), 'list_items'), 0), 'identifier_uid'));
	}
	
	
	public function getInfoUID($token, $uid)// попытка получить информацию по пользователе по uid
	{		
		$request = Request::factory('http://10.200.20.2/api/v1/access/identifier/item/'.$uid) //
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.Arr::get($token, 'token'))
				->method('GET')
				//->body($data)
				->execute();
	
		//echo Debug::vars('42', json_decode ($request->body(), true)); exit;
		//Log::instance()->add(Log::DEBUG, '59 '. Debug::vars(json_decode ($request->body(), true)));
		//return Arr::get(Arr::get(Arr::get(json_decode ($request->body(), true), 'list_items'), 0), 'identifier_uid');
		return array('status'=>$request->status(), 'res'=>Debug::vars(json_decode ($request->body(), true),$uid));
	}
	
	
	public function delCard($token, $uid)
	{		
	// DELETE /api/v1/access/identifier/item/2 HTTP/1.1

		$request = Request::factory('http://10.200.20.2/api/v1/access/identifier/item/'.$uid)
			->headers("Accept", "application/json")
			->method(Request::DELETE)
			->headers("Authorization", 'Bearer '.Arr::get($token, 'token'));
			try{
			$response=$request->execute();
  
			Log::instance()->add(Log::DEBUG, '136 iud='.$uid.' '. Debug::vars($response->status()));
			Log::instance()->add(Log::DEBUG, '137 iud='.$uid.' '. Debug::vars(json_decode ($request->body(), true))."\r\n\r\n");
			//echo Debug::vars('42',$response->status(), json_decode ($response->body(), true)); //exit;
			Log::instance()->add(Log::DEBUG, '59 '.Debug::vars($response->status()).' '. Debug::vars(json_decode ($request->body(), true)));
			return array('status'=>$response->status(), 'res'=>json_decode ($response->body(), true));
		} catch (Kohana_Request_Exception $e) {
				return array ('status'=>0, 'res'=>$e->getMessage());
		}
	}
	
	
		
	
	public function getLog($token)// получить лог-файл
		{
			
			/* 'https://virtserver.swaggerhub.com/basip/panel-web-api/2.3.0/log/items?locale=en&
			from=1549286120038&
			to=1549286120038&
			limit=20&
			page_number=2&
			sort_type=asc&
			filter_field=category&
			filter_type=equal&
			filter_value=access' \
			-H 'accept: application/json' */
			//$data=json_encode(array('queryType'=>'GRZ', 'query'=>$grz));
			
			$data='filter_field=category&
			filter_type=equal&
			filter_value=access';
			
			$data='{
				  "filter_field": "category",
				  "filter_type": "equal",
				  "filter_value": "access"
				}';
			
		//	https://virtserver.swaggerhub.com/basip/panel-web-api/2.3.0/log/items?locale=en&from=1549286120038&to=1549286120038&limit=20&page_number=2&sort_type=asc&filter_field=category&filter_type=equal&filter_value=access
			
			
			//$request = Request::factory($this->baseurl.'/v1/log/items?filter_field=identifier_number&filter_type=equal&filter_format=string&filter_value=0') //')
			$request = Request::factory($this->baseurl.'/v1/log/items?limit=50&from=334067921&to='.time()) //')
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.Arr::get($token, 'token'))
				->method('GET')
				->body($data)
				->execute();
		//return json_decode ($request->body(), true);
		return Arr::get(json_decode ($request->body(), true), 'list_items');
		}
	
	public function getTime($token)// прочитать время
		{
		
			
			
			$request = Request::factory($this->baseurl.'/v1/device/time')
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.Arr::get($token, 'token'))
				->method('GET')
				
				->execute();
		//return json_decode ($request->body(), true);
		return json_decode ($request->body(), true);
		}
	
	
	public function bas_setTime($token)// установить время
		{
		
		$data='{
				  "timezone": "UTC+03:00",
				  "time_unix": '.time().'
				}';	
			
			$request = Request::factory($this->baseurl.'/v1/network/timezone/manual')
				->headers("Accept", "application/json")
				->headers("Content-Type", "application/json")
				->headers("Authorization", 'Bearer '.Arr::get($token, 'token'))
				->method('POST')
				->body($data)
				->execute();
		
		return json_decode ($request->body(), true);
		}
		
		public function getListCard($id_dev)// получить список карт для указанного id_dev и записать/удалить их из панели
	{
		Log::instance()->add(Log::DEBUG, '288 start model getListCard.');
		$auth=$this->makeRequest();
		Log::instance()->add(Log::DEBUG, '290 debug '. Debug::vars($auth));
				//echo Debug::vars('82', $auth, $this->account_type); exit;
				Log::instance()->add(Log::DEBUG, '292 debug '. Debug::vars($auth, $this->account_type));
				if(Arr::get($auth, 'account_type') == $this->account_type)// если авторизация выполнена и текущая роль admin, то начинаем работы с картами.
				{
					$result=Model::factory('bas')->getCardList(102);
					//echo Debug::vars('My_debug 169', $result); //exit;
					Log::instance()->add(Log::DEBUG, '297 получено карт для записи '. count($result));
					if($result){
						foreach($result as $key=>$value)
						{
							switch (Arr::get($value, 'OPERATION'))
							{
								case 1:// запись карты в панель
									$data=Model::factory('bas')->addCard($auth, Arr::get($value, 'ID_CARD'));
									Log::instance()->add(Log::DEBUG, '90 '. Debug::vars($data));
									switch (Arr::get($data, 'status'))
									{	
										case 200:
										case 400:
											//echo Debug::vars('188',$value, $data); exit;
											//зафиксировать ответ в таблице cardidx
											Model::factory('bas')->fixCardIdxOK($value, $data);
											
											//удалить строку из таблицы cardindev
											Model::factory('bas')->delFromCardindev($value);
											break;
										case 0:
											//всем записать большое количество попыток, чтобы прекратить записи в дальшейшем
											//выход из метода, полный выход
										default:
											//зафиксировать ответ в таблице cardidx как ошибку
											//увеличить количество попытко на 1
										
									}
								
								break;
								case 2:// удаление карты из панели
								
									$data=Model::factory('bas')->getInfoCard($auth, Arr::get($value, 'ID_CARD'));//получил результат запроса UID по номеру карты
									Log::instance()->add(Log::DEBUG, '200 '. Debug::vars($data));
									//echo Debug::vars('My_debug 198', $value, $data); //exit;
									switch (Arr::get($data, 'status'))
									{	
										case 200:
											//получен UID результат запроса
											if(Arr::get($data, 'res')) //если имеется UID
											{
												$data2=Model::factory('bas')->delCard($auth, Arr::get($data, 'res'));// удаляю карту из панели
												if(Arr::get($data2, 'status') == 200)
												{
													// если удаление прошло успешно, то
													//удалить строку из таблицы cardindev
													Model::factory('bas')->delFromCardindev($data);//удаляю строку с командой из таблицы cardindev
												} else {
													// не смогу удалить карту из панели
													//Model::factory('bas')->incrementAttemptsCardindev($data);//увеличиваю количество попыток attempts в таблице cardindev
													//Model::factory('bas')->fixCardIdxErr($value, $data);//фиксирую сообщение об ошибке в таблицу cardidx
												}
											}	
											
											break;
										default:
										// не смогу удалить карту из панели, т.к ответ за запрос неправильный
											Model::factory('bas')->iincrementAttemptsCardindev($data);//увеличиваю количество попыток attempts в таблице cardindev
											Model::factory('bas')->fixCardIdxErr($value, $data);//фиксирую сообщение об ошибке в таблицу cardidx	
											break;
											//не смог получить UID  от панели
									}
								
								break;
								
								
							}
							
					
							
						}
					}
				}
				Log::instance()->add(Log::DEBUG, '369 stop model getListCard.');
					return (date("H:i:s").' Метод action_getListCard свою работу завершил.'."\r\n\r\n");
		
	}
	
	// функция обновления одной строки в таблице bas_param
	//сначала выполняется удаление строки, а затем вставка с нужным параметром.
	
	public function update_row($var, $type)
	{
		// Подготовим запрос для удаления записи
				$sql_delete = "DELETE FROM bas_param
								WHERE id_dev = :id_dev
								AND param = 'IP'";

				// Выполним запрос DELETE
				$delete_query = DB::query(Database::DELETE, $sql_delete)
									->param(':id_dev', $id_dev);
		
	}

	
    public function updateData( $login , $password, $id_dev){ 
	//echo Debug::vars('134', $id_dev);exit;
		// Преобразуем $id_dev в целое число
		$id_dev = intval($id_dev);
		$login = strval($login);
		$password = strval($password);
		//echo Debug::vars('337', $password,$login); exit;
		// Проверяем наличие записи с таким же id_dev
		if (!empty($id_dev)) {
			// Проверяем и удаляем записи для 'login'
			$sql_select = "SELECT id_dev
			   FROM bas_param
			   WHERE id_dev = :id_dev
			   AND param = 'login'";

			$query_select = DB::query(Database::SELECT, $sql_select)
				->param(':id_dev', $id_dev)
				->execute(Database::instance('fb'));

			$existingRecord = $query_select->current();

			if ($existingRecord !== null) {
				$sql_delete = "DELETE FROM bas_param
					WHERE id_dev = :id_dev
					AND param = 'login'";

				$delete_query = DB::query(Database::DELETE, $sql_delete)
					->param(':id_dev', $id_dev);

				$delete_result = $delete_query->execute(Database::instance('fb'));
			}

			// Проверяем и удаляем записи для 'password'
			$sql_select = "SELECT id_dev
				FROM bas_param
				WHERE id_dev = :id_dev
				AND param = 'password'";

			$query_select = DB::query(Database::SELECT, $sql_select)
				->param(':id_dev', $id_dev)
				->execute(Database::instance('fb'));

			$existingRecord = $query_select->current();

			if ($existingRecord !== null) {
				$sql_delete = "DELETE FROM bas_param
					WHERE id_dev = :id_dev
					AND param = 'password'";

				$delete_query = DB::query(Database::DELETE, $sql_delete)
					->param(':id_dev', $id_dev);

				$delete_result = $delete_query->execute(Database::instance('fb'));
			}
		}

			
			try{
			// Вставка новой записи login
			$sql_insert_login = 'INSERT INTO bas_param (ID_DEV, PARAM, INTVALUE, STRVALUE) VALUES (:id_dev, \'login\', NULL, \''. $login . '\')' ;
			$insert_query_login = DB::query(Database::INSERT, iconv('UTF-8','windows-1251', $sql_insert_login))
									->param(':id_dev', $id_dev);
			$insert_result_login = $insert_query_login->execute(Database::instance('fb'));
			

			// Вставка новой записи password
			$sql_insert_password = 'INSERT INTO bas_param (ID_DEV, PARAM, STRVALUE) VALUES (:id_dev, \'password\',  \''. $password . '\')';
			$insert_query_password = DB::query(Database::INSERT,iconv('UTF-8','windows-1251', $sql_insert_password))
									   ->param(':id_dev', $id_dev);
			$insert_result_password = $insert_query_password->execute(Database::instance('fb'));
			return 0; // Возвращаем не успешного выполнение
		} catch (Exception $e){
			return 1; // Возвращаем успешное выполнение	
		}




		

		
	}
	
}


