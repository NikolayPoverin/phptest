    <?php defined('SYSPATH') or die('No direct script access.');
	/*
	20.05.2023
	Класс предназначен для сбора событий из устройств bas-ip
	В ходе работы используется класс 
		Model_Basoop() для связи к устройствами bas-ip
		Model_BasEvent() для анализа событий и записи их в в БД СКУД
	*/
 
    class Task_basgetevent extends Minion_Task {
		
		    protected $_options = array(
        // param name => default value
        //'id_dev'   => '7',
		'aaa'=>'111',
       
		);
	
        
        protected function _execute(array $params)
        {
			$t1=microtime(1);
          // Log::instance()->add(Log::DEBUG, 'event-14 start basgetevent.php');
		//получаю список контроллеров для сбора событий
		$sql='select d.id_dev, bp.intvalue from device  d
            join bas_param bp on bp.id_dev=d.id_dev  and bp.param=\'IP\'
            join servertypelist stl on stl.id_server=d.id_server
            join servertype srt on srt.id=stl.id_type
            where srt.sname=\'bas\'';
			
	

		$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array()
				;
		
	if(count($query)>0)
	{
		foreach ($query as $key=>$value)
		{
			
			$ModelBas=new Model_Basoop();
			
			$ModelBas->init_dev(Arr::get($value, 'ID_DEV')); //создаю модель панель для указанного ID пользователя
			//Log::instance()->add(Log::DEBUG, 'ev=34 Начало работы с панелью '.$ModelBas->baseurl.', id_dev= '.$ModelBas->id_dev.', device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'".');
			if($ModelBas->statusOnline)// если панель на связи, то начинаю выборку событий.
			{
						
			//Log::instance()->add(Log::DEBUG, '#43 Панель '.$ModelBas->baseurl.' на связи '.$ModelBas->statusOnline.', '.$ModelBas->device_model.', '.$ModelBas->framework_version.', '.$ModelBas->firmware_version.', '.$ModelBas->api_version);
			
			$ModelBas->saveParam('ABOUT', NULL, $ModelBas->statusOnline.', '.$ModelBas->device_model.', '.$ModelBas->framework_version.', '.$ModelBas->firmware_version.', '.$ModelBas->api_version); //сохраняю параметры панели в БД СКУД.
			
			//Log::instance()->add(Log::DEBUG, 'event-43 провожу авторизацию панели id_dev='. $ModelBas->id_dev.', id_ctrl='. $ModelBas->id_ctrl.',  baseurl='. $ModelBas->baseurl.',  name=\''. iconv('windows-1251', 'UTF-8',$ModelBas->name).'\'');
		
					if($ModelBas->account_type == 'admin')// если авторизация выполнена и текущая роль admin, то начинаю выборку событий.
					{
						Log::instance()->add(Log::DEBUG, 'event-260 авторизация в устройстве baseurl='. $ModelBas->baseurl.',  name=\''. iconv('windows-1251', 'UTF-8',$ModelBas->name).'\' выполнена успешно.');
						
						$ModelBas->setTime();// устанавливаю часы панели
						$result=$ModelBas->getEvent();// выбираю события из панели
						//Log::instance()->add(Log::DEBUG, 'event-56 Получил от '.$ModelBas->baseurl.' массив из '.count($result).' событий');
						
						if($result){
							foreach($result as $key2=>$value2)
							{
								//echo Debug::vars('My_debug 255', $value); exit;
								//Log::instance()->add(Log::DEBUG, 'event-55 Обрабатывается событие '.Debug::vars($value));
								$event=new Model_BasEvent();//создаю модель события
								$event->init($value2);	
												
								switch ($event->name)//начинаю анализ события по их имени
								{
									case 'lock_was_opened_by_exit_btn':// дверь открыта кнопкой
										

									$ModelBas->EventInsert(1, 49, $ModelBas->id_ctrl, 0, NULL,  date ('d.m.Y H:i:s', $event->timestamp/1000), NULL, NULL, NULL, NULL, 2, $event->timestamp);	
									
									Log::instance()->add(Log::DEBUG, '+Device="'.iconv('windows-1251', 'UTF-8',$ModelBas->name).', Readdate =#'.date('d.m.Y H:i:s').', DeviceDate=#'.date ('d.m.Y H:i:s', $event->timestamp/1000).', EventCode=49, Door=0, EventIndex='.$event->timestamp);
									break;
									case 'access_denied_by_not_valid_input_code':// access_denied_by_not_valid_input_code прохода запрещен, невалидный номер карты
									
										//Log::instance()->add(Log::DEBUG, 'event-76 неизвестная карта '.Debug::vars($event));
										$ModelBas->EventInsert(1, 91, $ModelBas->id_ctrl, 0, NULL,  date ('d.m.Y H:i:s', $event->timestamp/1000), NULL, NULL, NULL, NULL, 2, $event->timestamp);
										Log::instance()->add(Log::DEBUG, '+Device="'.iconv('windows-1251', 'UTF-8',$ModelBas->name).', Readdate =#'.date('d.m.Y H:i:s').'#, DeviceDate=#'.date ('d.m.Y H:i:s', $event->timestamp/1000).'#, EventCode=91, Door=0, EventIndex='.$event->timestamp.', Card="'.$event->number.'"');
									break;
									
									case 'access_granted_by_master_code':// access_granted_by_master_code
									
										//Log::instance()->add(Log::DEBUG, 'event-70 Дверь открыта мастер-кодом'. Debug::vars($event));
										$ModelBas->EventInsert(1, 90, $ModelBas->id_ctrl, 0, NULL,  date ('d.m.Y H:i:s', $event->timestamp/1000), NULL, NULL, NULL, NULL, 2, $event->timestamp);	
									
									Log::instance()->add(Log::DEBUG, '+Device="'.iconv('windows-1251', 'UTF-8',$ModelBas->name).', Readdate =#'.date('d.m.Y H:i:s').', DeviceDate=#'.date ('d.m.Y H:i:s', $event->timestamp/1000).', EventCode=90, Door=0, EventIndex='.$event->timestamp);
									break;
									
									case 'access_granted_by_valid_identifier':// access_granted_by_valid_identifier проход по валидного идентификатору
									
										Log::instance()->add(Log::DEBUG, 'event-87 Дверь открыта картой '.$event->number);
										$ModelBas->EventInsert(1, 50, $ModelBas->id_ctrl, 0, $event->number,  date ('d.m.Y H:i:s', $event->timestamp/1000), NULL, NULL, NULL, NULL, 2, $event->timestamp);
										Log::instance()->add(Log::DEBUG, '+Device="'.iconv('windows-1251', 'UTF-8',$ModelBas->name).', Readdate =#'.date('d.m.Y H:i:s').'#, DeviceDate=#'.date ('d.m.Y H:i:s', $event->timestamp/1000).'#, EventCode=50, Door=0, EventIndex='.$event->timestamp.', Card="'.$event->number.'"');
									break;
									
									default:
									
									Log::instance()->add(Log::DEBUG, 'event-94 Неизвестное событие '.Debug::vars($event));
									
									 /* 
									 ID_DB INTEGER,
									ID_EVENTTYPE INTEGER,
									ID_CTRL INTEGER,
									ID_READER INTEGER,
									NOTE VARCHAR(100),
									"TIME" TIMESTAMP,
									ID_VIDEO INTEGER,
									ID_USER INTEGER,
									ESS1 INTEGER,
									ESS2 INTEGER,
									IDSOURCE INTEGER,
									IDSERVERTS INTEGER) */
		
									$ModelBas->EventInsert(1, 0, $ModelBas->id_ctrl, 0, Arr::get(Arr::get($value2, 'name'), 'key'),  date ('d.m.Y H:i:s', Arr::get($value2, 'timestamp')/1000), NULL, NULL, NULL, NULL, 2, Arr::get($value2, 'timestamp'));	
							
								}
								
							$ModelBas->setLastEventID($event->timestamp);//записываю номер последнего обработанного события
								
							}
						} else {
							Log::instance()->add(Log::DEBUG, '121 Новых событий '.$ModelBas->baseurl.' нет.');
						}
					} else {
						//авторизация выполнена неуспешно
													
						Log::instance()->add(Log::DEBUG, 'event-136 Авторизация для устройства '.Arr::get($value, 'ID_DEV').' не выполнена. Ответ устрйоства: '. Debug::vars($ModelBas->account_type));	
					}
				//Log::instance()->add(Log::DEBUG, date("H:i:s").' ev- 137 работа с контроллером '.$ModelBas->baseurl.' завершена.'."\r\n\r\n");
									
			} else {
				// панель не на связи
				Log::instance()->add(Log::DEBUG, 'event-151 с контроллером '.$ModelBas->baseurl.' связи нет'); 
				
			} 
			
		} 
	} else {
			Log::instance()->add(Log::DEBUG, 'event-153 Нет устройств Bas-ip');
		}
		
		
		//Log::instance()->add(Log::DEBUG, 'event-142 stop minion basgetevent.php. Выборка событий завершена. Время выполнения timeExecute='. (microtime(1)-$t1));
	}

	
}