<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dashboard extends Controller_Template {
	
	public $template = 'template';
	public $account_type = 'admin';
	
	public function before()
	{
			parent::before();
			$session = Session::instance();
			$request = Request::initial();
			
	}
	
	public function action_index()
	{
		$deviceList=Model::factory('bas')->getBasDeviceList();
		

			
		//echo Debug::vars('32', $deviceList); exit;
		
		$result=array();
		$content = View::factory('Dashboard', array(
			'result'=>$result,
			'deviceList' => Arr::get($deviceList, 'res'),
			
			));
		$this->template->content = $content;
		
	}
	
	public function action_auth()
	{
		//echo Debug::vars('33', 'POST: ', $this->request->post(), 'GET: ',$this->request->query()); exit;
		//echo Debug::vars('33', 'POST: ', $this->request->post(), $this->request->post('todo')); exit;
		switch ($this->request->post('todo'))
		{
			
			case 'bas_about':
				//$auth=Model::factory('bas')->makeRequest();
				//$result=Model::factory('bas')->about($auth);
				
				$ModelBas=Model::factory('bas');
				//$ModelBas->baseurl = 'http://10.200.20.2/api';
				//$result=Model::factory('bas')->about();
				$result=$ModelBas->about();
			break;
			//echo Debug::vars('39', $auth, $about); exit;
			
			case 'bas_addCard':
			$auth=Model::factory('bas')->makeRequest();
				$result=Model::factory('bas')->addCard($auth);
			break;
			case 'bas_delCard':
			$auth=Model::factory('bas')->makeRequest();
				$result=Model::factory('bas')->delCard($auth);
			break;
			case 'bas_getInfoCard':
				$auth=Model::factory('bas')->makeRequest();
				$card='112233';
				$result=Model::factory('bas')->getInfoCard($auth,$card);
			break;
			//echo Debug::vars('39', $auth, $about); exit;
			
			case 'bas_getInfoUID':
				$auth=Model::factory('bas')->makeRequest();
				$uid='2577';
				$result=Model::factory('bas')->getInfoUID($auth,$uid);
			break;
			//echo Debug::vars('39', $auth, $about); exit;
			
			case 'bas_getLog':
			$auth=Model::factory('bas')->makeRequest();
				$result=Model::factory('bas')->getLog($auth);
			break;
			//echo Debug::vars('39', $auth, $about); exit;
			
			case 'bas_getTime':
			$auth=Model::factory('bas')->makeRequest();
				$result=Model::factory('bas')->getTime($auth);
			break;
			
			case 'bas_setTime':
				$auth=Model::factory('bas')->makeRequest();
				$result=Model::factory('bas')->bas_setTime($auth);
			break;
			
			case 'bas_getCardList':// запись и удаление карт из котроллера.
				$id_dev = $this->request->param('id');
				$result=$this->action_getListCard($id_dev);
			break;
			
			case 'bas_aboutUser':// выборка информации по имени пользователя
				//$id_dev = $this->request->param('id');
				//$result=$this->action_getListCard($id_dev);
			break;
			
			case 'bas_getServerList':// получить список транспортных серверов с типом bas
				//echo Debug::vars('88 debug'); exit;
				
				$result=Model::factory('bas')->getBasServerList();
				
			break;
			
			case 'bas_getDeviceList':// получить список устройств типа bas-ip
				//echo Debug::vars('88 debug'); exit;
				
				$bas=Model::factory('bas');
				$data=Validation::factory($bas->getBasServerList());
				$data->rule('status', 'in_array', array(':value', array('0')))
					->rule('res', 'not_empty');
				
				if($data->check())
				{
					//echo Debug::vars('88 OK', Arr::get($data, 'res')); //exit;
					
					$deviceList=$bas->getBasDeviceList(Arr::get($data, 'res'));
					if(Arr::get($deviceList, 'status') == 0)//ответ успешно сформирован
					{
							$result=Arr::get($deviceList, 'res');
					}
					
					Session::instance()->set('ok_mess', array('ok_mess' => 'Данные обновлены успешно'));
					//echo Debug::vars('117', $deviceList); exit;
					
				} else {
					//echo Debug::vars('88 ERR'); exit;
					Session::instance()->set('e_mess', array('Ошибка '.Arr::get($data, 'result').' не могу выдать данные по КПП.'));
					Session::instance()->set('e_mess', $data->errors('Valid_mess'));
					Log::instance()->add(Log::DEBUG, '141 '.$data->errors('Valid_mess'));
					$this->redirect('/');
				}
				
				
			break;
			
			
			
			case 'bas_delCardList':
				$auth=Model::factory('bas')->makeRequest();
				for($i=1000; $i<2000; $i++)
				{
						$ttt=Model::factory('bas')->delCard($auth,$i); 
						//echo Debug::vars('88', $ttt);

				}
				exit;
			break;
			
			
			
			
			
			default:
				$this->redirect('dashboard');
		}
		
		$content= View::factory('Dashboard', array(
			'result'=>$result,
			));
		$this->template->content = $content;
	}
	
	
	public function action_getDeviceList()
	{
		$bas=Model::factory('bas');
				$data=Validation::factory($bas->getBasServerList());
				$data->rule('status', 'in_array', array(':value', array('0')))
					->rule('res', 'not_empty');
				
				if($data->check())
				{
					//echo Debug::vars('88 OK', Arr::get($data, 'res')); //exit;
					
					$deviceList=$bas->getBasDeviceList(Arr::get($data, 'res'));
					if(Arr::get($deviceList, 'status') == 0)//ответ успешно сформирован
					{
							$result=Arr::get($deviceList, 'res');
					}
					
					Session::instance()->set('ok_mess', array('ok_mess' => 'Данные обновлены успешно'));
					//echo Debug::vars('117', $deviceList); exit;
					
				} else {
					//echo Debug::vars('88 ERR'); exit;
					Session::instance()->set('e_mess', array('Ошибка '.Arr::get($data, 'result').' не могу выдать данные по КПП.'));
					Session::instance()->set('e_mess', $data->errors('Valid_mess'));
					Log::instance()->add(Log::DEBUG, '200 '.$data->errors('Valid_mess'));
					//$this->redirect('/');
					$result=array();
				}
			return $result;
		
	}
	
	
	public function action_command()// обработка команд, полученный в веб-панели.  в Kohana v3.1 + класс запроса имеет query() и post() методы. https://askdev.ru/q/kak-ispolzovat-this-request-param-of-kohana-dlya-polucheniya-peremennyh-zaprosa-339785/
	{
		
		echo Debug::vars('33', 'POST: ', $this->request->post(), 'GET: ',$this->request->query()); exit;
		$answer_array=explode("&",$this->request->body());
		$answer=array('ID'=>1, 'Return'=>2, 'CMD'=>3);
		$answer=array();
		//ID=6541344&Return=0&CMD=CONTROL+DEVICE
		// парсер с целью выделения результатов выполнения команды.
		foreach($answer_array as $var)
		{
			if(strpos ($var, 'ID=') === 0) $answer['ID']=substr ($var, strlen('ID='));
			if(strpos ($var, 'Return=') === 0) $answer['Return']=substr ($var, strlen('Return='));
			if(strpos ($var, 'CMD=') === 0) $answer['CMD']=substr ($var, strlen('CMD='));
		}
				
	
		$command=$this->request->query('command');
		// 0 - нет команды
	// 1 - записать карты
	// 2 - удалить карты
	// 3 - открыть дверь
	// 4 - добавить ФИО
	// 5 - удалить ФИО
	// 6 - получить информацию о пиплах
	// 7 - restart
	// 8 - extusers
		$result=Model::Factory('GetCommand')->setCommand($command);
		//echo Debug::vars('46', $command); exit;
		HTTP::redirect('/dashboard?res='.$result);
		
	}
		
	public function action_getListCard()// получить список карт для указанного id_dev и записать/удалить их из панели
	{
		Log::instance()->add(Log::DEBUG, '244 start action_getListCard.');
		$auth=Model::factory('bas')->makeRequest();
				//echo Debug::vars('82', $auth, $this->account_type); exit;
				
				if(Arr::get($auth, 'account_type') == $this->account_type)// если авторизация выполнена и текущая роль admin, то начинаем работы с картами.
				{
					Log::instance()->add(Log::DEBUG, '250 авторизация в устройстве выполнена');
					$result=Model::factory('bas')->getCardList(102);
					Log::instance()->add(Log::DEBUG, '252 список карт для обработки '.Debug::vars($result));
					//echo Debug::vars('My_debug 169', $result); exit;
					if($result){
						foreach($result as $key=>$value)
						{
							//echo Debug::vars('My_debug 255', $value); exit;
							switch (Arr::get($value, 'OPERATION'))
							{
								case 1:// запись карты в панель
								//echo Debug::vars('My_debug 259', $value); //exit;
								Log::instance()->add(Log::DEBUG, '263 запись карты '.Debug::vars($value));
									$data=Model::factory('bas')->addCard($auth, Arr::get($value, 'ID_CARD'));
									Log::instance()->add(Log::DEBUG, '264 результат записи карты '. Debug::vars($data). Debug::vars($value));
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
								//echo Debug::vars('My_debug 283', $value); exit;
								Log::instance()->add(Log::DEBUG, '289 удаление карты '.Debug::vars($value));
									$data=Model::factory('bas')->getInfoCard($auth, Arr::get($value, 'ID_CARD'));//получил результат запроса UID по номеру карты
									Log::instance()->add(Log::DEBUG, '290 Информация по удаляемой карте '. Debug::vars($data));
									//echo Debug::vars('My_debug 198', $value, $data); //exit;
									switch (Arr::get($data, 'status'))
									{	
										case 200:
											//получен UID результат запроса
											if(Arr::get($data, 'res')) //если имеется UID
											{
												Log::instance()->add(Log::DEBUG, '298 Удаление карты, которая найдена в контроллере '. Debug::vars($data, $value));
												//echo Debug::vars('296', $data, $value); exit;
												$data2=Model::factory('bas')->delCard($auth, Arr::get($data, 'res'));// удаляю карту из панели
												if(Arr::get($data2, 'status') == 200)
												{
													// если удаление прошло успешно, то
													//удалить строку из таблицы cardindev
													Model::factory('bas')->delFromCardindev($value);//удаляю строку с командой из таблицы cardindev
												} else {
													// не смогу удалить карту из панели
													//Model::factory('bas')->incrementAttemptsCardindev($data);//увеличиваю количество попыток attempts в таблице cardindev
													//Model::factory('bas')->fixCardIdxErr($value, $data);//фиксирую сообщение об ошибке в таблицу cardidx
												}
											}
											//echo Debug::vars('309', $data, $value); exit;
											if( is_Null(Arr::get($data, 'res'))) //этой карты нет в устройстве. 
											{
												//echo Debug::vars('My_debug 311', $value); exit;
												Log::instance()->add(Log::DEBUG, '316 Удаление карты. Карты в контроллере нет, удаляю только строку из таблицы cardindev.'. Debug::vars($value));
												Model::factory('bas')->delFromCardindev($value);//удаляю строку с командой из таблицы cardindev
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
					} else {
					Log::instance()->add(Log::DEBUG, '338 список карт для обработки пуст.');	
					}
				}
		Log::instance()->add(Log::DEBUG, '339 '.date("H:i:s").' Метод action_getListCard свою работу завершил.'."\r\n\r\n");
		return (date("H:i:s").' Метод action_getListCard свою работу завершил.'."\r\n\r\n");
		
	}
	public function action_update() {
		//echo Debug::vars('337', $_POST); exit;
		$new_IP = $_POST['new_IP'];
		$login = $_POST['login'];
		$password = $_POST['password'];
		$id_dev = $_POST['id_dev'];

		// Здесь можно добавить дополнительную проверку данных

		$model_bas = Model::factory('Bas'); // Создание экземпляра модели Bas
		$result = $model_bas->updateData($new_IP, $login, $password, $id_dev); // Вызов метода updateData() из модели

		// Можно добавить дополнительные действия после обновления данных

		$this->redirect('/'); // Перенаправление на страницу успешного обновления
	}
	
}

