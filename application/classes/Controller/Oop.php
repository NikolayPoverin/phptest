<?php defined('SYSPATH') or die('No direct script access.');

//class Controller_Oop extends Controller_Template {
class Controller_Oop extends Controller {
	
	public $template = 'template';
	public $account_type = 'admin';
	
	
	public function before()
	{
			parent::before();
			$session = Session::instance();
			$request = Request::initial();
			/* try{
			DB::query(Database::SELECT, 'select count(*) from server')
                ->execute(Database::instance('fb_firebird'))
                ->as_array();
			} catch (Exception $e){
					echo Debug::vars('17', 'Нет базы данны '.$e);
					
					$this->redirect('errorpage?err='.$e);
			} */
		
	}
	
	public function action_index()
	{
		//$content=Model::Factory('GetCommand')->getTestTP();
		$deviceList=Controller_Dashboard::action_getDeviceList();
		$content=View::factory('basListTable', array(
			'deviceList' => $deviceList,
			));
			
		//echo Debug::vars('32', $deviceList, $tableDeviceList); exit;
		
		$result=array();
		$content = $content .  View::factory('Dashboard', array(
			'result'=>$result,
			
			));
		$this->template->content = $content;
		
	}
	
	public function action_basAbout()// получить информацию о панели. Тут можно будет извлекать версию прибора. Мы должны работать только с определенными моделями. Наверняка должны с прибормами aa12.
	{
		
		$ip='10.200.20.2';
		$ModelBas=new Model_Basoop();
		$ModelBas->init($ip);
		$result=$ModelBas->about();
		
		//вставка версии прибора в таблицу статистики
		//-- не реализовано
		
		Log::instance()->add(Log::DEBUG, date("H:i:s").' 57 Метод action_basAbout свою работу завершил. Результат: '. Debug::vars($result));
		Log::instance()->add(Log::DEBUG, "\r\n\r\n");
		return date("H:i:s").' Метод action_basAbout свою работу завершил. Результат: '. $result;
	}
	
	
	public function action_control_no_model()// обработка действий, не требующих подключение к панелям
	{
		//echo Debug::vars('65', 'POST: ', $this->request->post(), 'GET: ',$this->request->query()); exit;
			switch ($this->request->post('todo'))
			{
				
				
				case 'bas_changeIP2':// замена IP адреса
					$data=Validation::factory($_POST);
					$data->rule('id_dev', 'not_empty')
						->rule('id_dev', 'digit')
						->rule('new_IP', 'not_empty')
						->rule('new_IP', 'IP')
						;
					
					if($data->check())
					{
					//echo Debug::vars('93 OK', $data); exit;
					$result=Model::factory('bas')->changeConfigIP($data);
						if(Model::factory('bas')->changeConfigIP($data) == 0){
							$id_dev = $data['id_dev'];
							Session::instance()->set('alertOk', $data->errors('validation'));
							$this->redirect('/?id_dev=' . $id_dev); // Перенаправление на страницу успешного обновленияы
						}
						else{
							//Session::instance()->set('alertIPErr', $data = 'Ошибка Sql запроса');
							Session::instance()->set('alertIPErr', $data = __('sql_err_89', array('new_ip'=>Arr::get($data,'new_IP'))));
							$this->redirect('/');
						}
					
					} else {
						//echo Debug::vars('96 ERR', $data); exit;
						
						//$this->redirect('dashboard');
						Session::instance()->set('alertErr', $data->errors('Valid_mess'));
						
						$this->redirect('/');
					}
					$this->redirect('dashboard');				

				break;
			}
		
	}
	
	public function action_control()
	{
		//echo Debug::vars('33', 'POST: ', $this->request->post(), 'GET: ',$this->request->query()); exit;
		//echo Debug::vars('33', 'POST: ', $this->request->post(), $this->request->post('todo')); exit;
		$t1=microtime(1);
		Log::instance()->add(Log::DEBUG, '#68-c-time '.(microtime(1)-$t1));	
		
		
		$ip='10.200.20.2';
		//$ip='192.168.1.5';

		$ModelBas=new Model_Basoop();
		$ModelBas->init($ip);
		
		Log::instance()->add(Log::DEBUG, '#74-c-time '.(microtime(1)-$t1));		
		//$ModelBas=$this->$ModelBasGlobas();
		
				
				Log::instance()->add(Log::DEBUG, '#78-c-time '.(microtime(1)-$t1));
			switch ($this->request->post('todo'))
			{
				
				case 'bas_about':
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
					$card='112233';
					$result=$ModelBas->getInfoCard($card);
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
				
				case 'getDeviceList':// получить список устройств типа bas-ip
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
								Log::instance()->add(Log::DEBUG, '149 Список контроллеров bas-IP:'. Debug::vars($deviceList));
								$result=Arr::get($deviceList, 'res');
						}
						
						Session::instance()->set('ok_mess', array('ok_mess' => 'Данные обновлены успешно'));
						//echo Debug::vars('117', $deviceList); exit;
						
					} else {
						//echo Debug::vars('88 ERR'); exit;
						Session::instance()->set('e_mess', array('Ошибка '.Arr::get($data, 'result').' не могу выдать данные по КПП.'));
						Session::instance()->set('e_mess', $data->errors('Valid_mess'));
						Log::instance()->add(Log::DEBUG, '160 ошибка при получении списка контроллеров bas-ip '.$data->errors('Valid_mess'));
						//$this->redirect('/');
						$result=Arr::get($deviceList, 'res');
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
					Log::instance()->add(Log::DEBUG, '228 '.$data->errors('Valid_mess'));
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
		

	
	
}