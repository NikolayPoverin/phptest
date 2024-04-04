<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 Класс для работы с событиями (в основном с таблицей events в БД СКУД.
 */
abstract class Event {
	
	//вставка события в таблицу events
	public function EventInsert($id_db=1, 
		$id_eventtype = NULL, 
		$id_cntrl= NULL, 
		$id_reader= NULL, 
		$note= NULL, 
		$time= NULL, 
		$id_video= NULL, 
		$id_user= NULL, 
		$ess1= NULL, 
		$ess2= NULL, 
		$idsource= NULL, 
		$idserverts= NULL)// вставка события от устройства
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
	
	}

} // End Event
