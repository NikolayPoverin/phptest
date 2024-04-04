    <?php defined('SYSPATH') or die('No direct script access.');
 
    class Task_basrwrfid extends Minion_Task {
		
		    protected $_options = array(
        // param name => default value
        'id_dev'   => '7',
       
		);
	
        
       protected function _execute(array $params)
       {
			$t1=microtime(1);
           Log::instance()->add(Log::DEBUG, 'rw-14 start basrwrfid.php');
		//получаю список контроллеров, в которые надо загрузить карты
		$sql='select distinct d2.id_dev from cardindev cd
			join device d on cd.id_dev=d.id_dev
			join device d2 on d2.id_ctrl=d.id_ctrl and  d2.id_reader is null
			join servertypelist stl on stl.id_server=d2.id_server
			join servertype srt on srt.id=stl.id_type
			where srt.sname=\'bas\'';

		$query = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array()
				;
		Log::instance()->add(Log::DEBUG, 'rw=28 количество карт для обработки '. count($query));	
		
		if(count($query)==0)		
		{
			Log::instance()->add(Log::DEBUG, 'rw=32 Нет карт для обработки. Работа задачи basrwrfid прекращается.');	
			//выход в связи с отсутствием предмета обработки
			exit;
			
		}

		foreach ($query as $key=>$value)
		{
		$ModelBas=new Model_Basoop();
		$ModelBas->init_dev(Arr::get($value, 'ID_DEV'));
		Log::instance()->add(Log::DEBUG, 'rw=34 Начало работы с панелью '.$ModelBas->baseurl.', id_dev= '.$ModelBas->id_dev.', device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'".');
		$cardList=$ModelBas->getCardList(Arr::get($value, 'ID_DEV'));
					//echo Debug::vars('My_debug 169', $result); exit;
					//Log::instance()->add(Log::DEBUG, '44 список карт для обработки '.Debug::vars($result));
		if($cardList)
		{
			foreach($cardList as $key=>$value)
			{
				switch (Arr::get($value, 'OPERATION'))
				{
					case 1:// запись карты в панель
						Log::instance()->add(Log::DEBUG, 'command writekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'"');
								
						if(!$ModelBas->statusOnline) 
						{
							Log::instance()->add(Log::DEBUG, 'answer writekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'" Result: Err,  Desc="Online no"');	
							//выход, т.к. нет связи с устройством
							
							exit;
						}
								
						if($ModelBas->account_type != 'admin') 
						{
							Log::instance()->add(Log::DEBUG, 'answer writekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'" Result: Err,  Desc="Auth no grant"');	
							//выход, т.к. нет связи с устройством
							exit;
						}
							
						$data=$ModelBas->addCard(Arr::get($value, 'ID_CARD'));
						Log::instance()->add(Log::DEBUG, '70 данные после записи карты '.Debug::vars($data));
													
									if (Arr::get($data, 'status') == 200)
									{	
									
											//зафиксировать ответ в таблице cardidx
											$ModelBas->fixCardIdxOK($value, $data);
											
											//удалить строку из таблицы cardindev
											$ModelBas->delFromCardindev($value);
											Log::instance()->add(Log::DEBUG, 'answer writekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'" Result: OK');
									} else {
											//зафиксировать ответ в таблице cardidx как ошибку
											//увеличить количество попытко на 1
											$ModelBas->incrementAttemptsCardindev($data);//увеличиваю количество попыток attempts в таблице cardindev
											Log::instance()->add(Log::DEBUG, 'answer writekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'" Result: Err, Desc="'.Debug::vars(Arr::get($data, 'res')).'"');
										
									}
								
					break;	
					case 2:// удаление карты из панели
									Log::instance()->add(Log::DEBUG, 'command deletekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'"');
								
								if(!$ModelBas->statusOnline) 
								{
									Log::instance()->add(Log::DEBUG, 'answer deletekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'" Result: Err,  Desc="Online no"');	
									//выход, т.к. нет связи с устройством
									exit;
								}
								
								if($ModelBas->account_type != 'admin') 
								{
									Log::instance()->add(Log::DEBUG, 'answer deletekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'" Result: Err,  Desc="Auth no grant"');	
									//выход, т.к. нет связи с устройством
									exit;
								}
								
								
								$data=$ModelBas->delCard(Arr::get($value, 'ID_CARD'));//получил результат запроса UID по номеру карты
									if (Arr::get($data, 'status') == 200)
									{	
										
											$ModelBas->delFromCardindev($value);//удаляю строку с командой из таблицы cardindev
											Log::instance()->add(Log::DEBUG, 'answer deletekey device="'.iconv('windows-1251','UTF-8',$ModelBas->name).'", key="'.Arr::get($value, 'ID_CARD').'". Result: OK');
									} else {
											$ModelBas->iincrementAttemptsCardindev($data);//увеличиваю количество попыток attempts в таблице cardindev
											$ModelBas->fixCardIdxErr($value, $data);//фиксирую сообщение об ошибке в таблицу cardidx	
											Log::instance()->add(Log::DEBUG, 'answer deletekey device="'.$ModelBas->api_version.'", key="'.Arr::get($value, 'ID_CARD').'". Result: Err');
										
									}
								
					break;
					default:
							Log::instance()->add(Log::DEBUG, '129 Операция .'. Arr::get($value, 'OPERATION').' не может быть выполнена. Набор данных: '. Debug::vars($value) );	
					break;
					}
							
					
							
			}
			// завершена обработка списка карт
		} else 
		{
			Log::instance()->add(Log::DEBUG, '373 Список карт устройства '.Arr::get($value, 'ID_DEV').' пуст.');	
		}
		//завершена обработка списка контроллеров, в которые надо загрузить или удалить идентификаторы
		} 
			Log::instance()->add(Log::DEBUG, '142 stop minion basrwrfid.php. timeExecute='. (microtime(1)-$t1));
	}
		
		
	
	
}
