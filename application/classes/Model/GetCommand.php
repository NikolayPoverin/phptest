<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_GetCommand extends Model
{
	
	//проверка наличия команды для указанного устройства
	// ответ: true - есть команда для исполнения, false - нет команды для исполнения
	public function checkCommandForDevice($device_info)
    {
        $id_dev = Arr::get($device_info, 'device_id');
		$is_reg = Arr::get($device_info, 'is_registrator');
        if(!$id_dev){
            $id_dev=0;
        }
        $sql="select first 1 operation, id_cardindev, id_pep from cardindev cd where cd.id_dev=". $id_dev;
		if($is_reg==-2147483648){
			$sql='select first 1 operation, id_cardindev, id_pep from cardindev cd where (cd.id_dev is null OR cd.id_dev ='.$id_dev.')';
		}
        // Log::instance())->add(Log::DEBUG, '(22) SQL-запрос '.$sql);
        $operationQuery = DB::query(Database::SELECT, iconv('UTF-8','windows-1251',$sql))
            ->execute(Database::instance('fb_firebird'))
            ->as_array();
        // Log::instance())->add(Log::DEBUG, '(26) Отправляем в БД запрос: ' . $sql);
        // Log::instance())->add(Log::DEBUG, '(27) Получен ответ от БД: ' . DEBUG::vars($operationQuery));
        if ($operationQuery) {
            // Log::instance())->add(Log::DEBUG, '(29) Найдена операция для устроства');
            return true;
        } else {
            // Log::instance())->add(Log::DEBUG, '(32) Не найдена операция для устроства');
            return false;
        }
    }
	
	
	
	// фомирование команды для записи в устройство
    public function getCommandForDevice($device_info)
    {

        // Читаем запись о команде из БД и получаем команду для устройства
        // Log::instance())->add(Log::DEBUG, '(10) device_id ' . Arr::get($device_info, 'device_id'));
        $commandParams = $this->getCommandParams($device_info);
        // Log::instance())->add(Log::DEBUG, '(12) commandParams ' . Debug::vars($commandParams));
        return $this->genCommand($commandParams);
    }

    
	//формирование команды для для устройства
	public function getCommandParams($device_info)
    {
        $res = array();
		$id_dev = Arr::get($device_info, 'device_id');
		$is_reg = Arr::get($device_info, 'is_registrator');
		$sql='select first 1 operation, id_cardindev, id_pep from cardindev cd where cd.attempts<3 AND cd.id_dev='. $id_dev;
		if($is_reg==-2147483648){
			$sql='select first 1 operation, id_cardindev, id_pep from cardindev cd where (cd.id_dev is null OR cd.id_dev ='.$id_dev.')';
		}
        //Log::instance()->add(Log::DEBUG, '(24) SQL-запрос '.$sql.'.');
        $operationQuery = DB::query(Database::SELECT, iconv('UTF-8','windows-1251',$sql))
            ->execute(Database::instance('fb_firebird'))
            ->as_array();
         //Log::instance()->add(Log::DEBUG, '(60) Отправляем в БД запрос: ' . $sql);
         //Log::instance()->add(Log::DEBUG, '(61) Ответ от БД 1: ' . Debug::vars($operationQuery));
         //Log::instance()->add(Log::DEBUG, '(62 ID_PEP = '.Debug::vars(Arr::get($query,0)));
         //Log::instance()->add(Log::DEBUG, '(63) ID_PEP = '.Arr::get(Arr::get($operationQuery,0), 'id_pep'));
        if ($operationQuery) {
            //$sql = 'SELECT PeopleID, Surname, Name FROM art_people WHERE PeopleID=' . Arr::get(Arr::get($operationQuery, 0), 'id_pep');
            //$peopleQuery = DB::query(Database::SELECT, $sql)
            //    ->execute(Database::instance('fb_mysql'))
            //    ->as_array();
            //// Log::instance())->add(Log::DEBUG, '(59) IDPEP: '.Arr::get(Arr::get($operationQuery, 0), 'ID_PEP'));
            $sql='select ID_PEP, SURNAME, NAME from PEOPLE cd where cd.ID_PEP='.Arr::get(Arr::get($operationQuery, 0), 'ID_PEP');
            $peopleQuery = DB::query(Database::SELECT, iconv('UTF-8','windows-1251',$sql))
                ->execute(Database::instance('fb_firebird'))
                ->as_array();
            // Log::instance())->add(Log::DEBUG, '(29) Ответ от БД 2: '.debug::vars($peopleQuery));

            //$sql = 'SELECT IdentID, Content FROM art_people_identifiers WHERE PeopleID=' . Arr::get(Arr::get($operationQuery, 0), 'id_pep');
            //$identQuery = DB::query(Database::SELECT, $sql)
            //    ->execute(Database::instance('fb_mysql'))
            //   ->as_array();

            //// Log::instance())->add(Log::DEBUG, '(71) IDPEP: '.Arr::get($operationQuery, 0));
            $sql='select IDX_USER, FP_TAMPLATE, FP_LENGTH from ZKSOFT_FP_TAMPLATE cd where cd.IDX_USER='.Arr::get(Arr::get($operationQuery, 0), 'ID_PEP');
            $identQuery = DB::query(Database::SELECT, $sql)
                ->execute(Database::instance('fb_firebird'))
                ->as_array();
			// Log::instance())->add(Log::DEBUG, '(35) Запрос в БД 3: '.$sql);
            // Log::instance())->add(Log::DEBUG, '(35) Ответ от БД 3: '.debug::vars($identQuery));


            

            $res['operationID'] = Arr::get(Arr::get($operationQuery, 0), 'id');
            $res['OPERATION'] = $this->decodeCommand(Arr::get(Arr::get($operationQuery, 0), 'OPERATION'));
            $res['ID_CARDINDEV'] = Arr::get(Arr::get($operationQuery, 0), 'ID_CARDINDEV');
            $res['ID_PEP'] = Arr::get(Arr::get($operationQuery, 0), 'ID_PEP');
            //$res['firstName']=iconv('windows-1251','UTF-8', Arr::get(Arr::get($peopleQuery,0), 'Name'));
            //$res['lastName']=iconv('windows-1251','UTF-8',Arr::get(Arr::get($peopleQuery,0), 'Surname'));
            $res['firstName'] = Arr::get(Arr::get($peopleQuery, 0), 'NAME');
            $res['lastName'] = Arr::get(Arr::get($peopleQuery, 0), 'SURNAME');
            $res['biophoto'] = Arr::get(Arr::get($identQuery, 0), 'FP_TAMPLATE');
            $res['photo_size'] = strlen(Arr::get($res, 'biophoto'));

        } else {
            // Log::instance())->add(Log::DEBUG, '(22) Для устройства с SN=' . $id_dev . ' команд в таблице cardindev нет.');
        }

        return $res;
    }

    public function decodeCommand($operation)
    {
        $command = "foo";
        switch ($operation) {

            case 0:
                $command = "no_command";
                break;
            case 6:
            case 1:
                $command = "add_person";
                break;
            case 2:
                $command = "delete_person";
                break;
            case 3:
                $command = "update_person";
                break;
            case 4:
                $command = "open_door";
                break;
            case 7:
                $command = "face delete";
                break;

        }
        return $command;
    }

    public function genCommand($commandParams)
    {
        $commandForDevice = 'default';
        $operationID = ARR::get($commandParams, 'operationID');
        $operation = Arr::get($commandParams, 'OPERATION');
        $id_cardindev = Arr::get($commandParams, 'ID_CARDINDEV');
        $id_pep = Arr::get($commandParams, 'ID_PEP');
        $firstName = Arr::get($commandParams, 'firstName');
        $lastName = Arr::get($commandParams, 'lastName');
        $fp_template = Arr::get($commandParams, 'fp_template');
        $biophoto = Arr::get($commandParams, 'biophoto');
        $photo_size = Arr::get($commandParams, 'photo_size');

        // Log::instance())->add(Log::DEBUG, '(50 Операция: '.$operation);
        switch ($operation) {
            case "add_person":
                $commandForDevice = "C:" . $id_cardindev . ":DATA UPDATE user CardNo=\tPin=" . $id_pep . "\tPassword=\tgroup=1\tstartTime=0\tendTime=0\tname=\tprivilege=14\n";
                $commandForDevice .= "C:" . $id_cardindev . ":DATA UPDATE extuser Pin=" . $id_pep . "\tFunSwitch=0\tFirstName=" . iconv('windows-1251','UTF-8',$firstName) . "\tLastName=" . iconv('windows-1251','UTF-8',$lastName). "\tpersonalvs=255" . "\n";
                $commandForDevice .= "C:" . $id_cardindev . ":DATA UPDATE userauthorize Pin=" . $id_pep . "\tAuthorizeTimezoneId=1\tAuthorizeDoorId=1\tDevID=1\n";
                //$commandForDevice .= "C:" . $id_cardindev . ":DATA UPDATE templatev10 Pin=" . $id_pep . "\tFingerID=6\tValid=1\ttemplate=" . $fp_template . "\n";
                //$commandForDevice.="C:".$id_cardindev.":DATA UPDATE biophoto Pin=".$id_pep."\tType=9\tSize=".$photo_size."\tContent=".$biophoto."\tFormat=0\tUrl=";
                //$commandForDevice .= "C:" . $id_cardindev . ":DATA UPDATE biophoto PIN=" . $id_pep . "\tType=9\tSize=" . $photo_size . "\tContent=" . $biophoto . "\tFormat=0\tUrl=";
                //$commandForDevice.="C:123:DATA UPDATE biophoto PIN=" . $id_pep . "\tType=9\tSize=60936\tContent=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\tFormat=0";

                if($biophoto){
                    $commandForDevice .= "C:" . $id_cardindev . ":DATA UPDATE biophoto PIN=" . $id_pep . "\tType=9\tFormat=0\tSize=" . $photo_size . "\tContent=" . $biophoto;
                }
                break;
            case "delete_person":
                $commandForDevice = "C:" . $id_cardindev . ":DATA DELETE biophoto PIN=" . $id_pep."\n";
                //$commandForDevice = "C:" . $id_cardindev . ":DATA DELETE biophoto Pin=" . $id_pep."\n";
                $commandForDevice .= "C:" . $id_cardindev . ":DATA DELETE templatev10 Pin=" . $id_pep."\n";
                $commandForDevice .= "C:" . $id_cardindev . ":DATA DELETE userauthorize Pin=" . $id_pep."\n";
                $commandForDevice .= "C:" . $id_cardindev . ":DATA DELETE extuser Pin=" . $id_pep."\n";
                $commandForDevice .= "C:" . $id_cardindev . ":DATA DELETE user Pin=" . $id_pep;
                break;
            case "update_person":
                $commandForDevice = "C:" . $id_cardindev . ":DATA UPDATE extuser Pin=" . $id_pep . "\tFunSwitch=0\tFirstName=" . $firstName . "\tLastName=" . $lastName . "\n";
                break;
            case "open_door":
                $commandForDevice = "C:" . $id_cardindev . ":CONTROL DEVICE 01010105";
                break;
            case "no_command":
                $commandForDevice = "OK";
                break;
            case "add_face":
                $commandForDevice .= "C:" . $id_cardindev . ":DATA UPDATE biophoto PIN=" . $id_pep . "\tType=9\tSize=" . $photo_size . "\tContent=" . $biophoto . "\tFormat=0\tUrl=";

                break;
        }
        // Log::instance())->add(Log::DEBUG, '(116 Сгенерирована команда: ' . $commandForDevice);
        return $commandForDevice;

    }

    public function setCommand($command) // установка команд разных
    {

        $sql = 'INSERT INTO CARDINDEV (ID_DB,ID_CARD,DEVIDX,ID_DEV,OPERATION,ATTEMPTS,ID_PEP) 
			VALUES (1,NULL,NULL,841,' . $command . ',0,1)';

        $res = -2;
        try {
            $res = DB::query(Database::INSERT, $sql)
                ->execute(Database::instance('fb'));
        } catch (Exception $e) {
            //echo Debug::vars('38');
            $res = -1;
        }
        // Log::instance())->add(Log::DEBUG, '(48) В базу данных записана команда ' . $command . ' для выполнения.');
        return $res;
    }

    public function delCommand($id_cardindev) // Удалить команду при успешном её выполнении
    {
        if ($id_cardindev != 0) {
            //$sql = 'DELETE FROM art_command_order WHERE id_cardindev=' . $id_cardindev;
            $sql=' delete from cardindev cd where id_cardindev='.$id_cardindev;
            // Log::instance())->add(Log::DEBUG, '(128) Удаление команды ' . $sql);
            try {
                //$res = DB::query(Database::DELETE, $sql)
                //    ->execute(Database::instance('fb_mysql'));
                $res = DB::query(Database::DELETE, $sql)
                    ->execute(Database::instance('fb_firebird'));
            } catch (Exception $e) {
                //echo Debug::vars('38');
                $res = 63;
            }

        }
        return $res;
    }
	public function updateInCardidx($id_cardindev, $description) // Удалить команду при успешном её выполненииrw
    {
        if ($id_cardindev != 0){
			
			
		$query = DB::update('CARDIDX')
			->set(array('LOAD_TIME' => 'now','LOAD_RESULT' => $description))
			->where('ID_CARDINDEV', '=', $id_cardindev)
			->execute(Database::instance('fb_firebird'));
			
            // Log::instance())->add(Log::DEBUG, '(268) Обновление информации в cardidx ');

        }
    }
    public function attCommand($id_cardindev) // Увеличение количества попыток при неуспешном выполнении команды
    {
        //$sql = 'SELECT ATTEMPTS FROM art_command_order where id_cardindev=' . $id_cardindev;
        $sql='select attempts from cardindev cd where id_cardindev='.$id_cardindev;
        //$res = DB::query(Database::SELECT, $sql)
        //    ->execute(Database::instance('fb_mysql'))
        //    ->get('ATTEMPTS');

        $res = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->get('ATTEMPTS');
        // Log::instance())->add(Log::DEBUG, '(128) Попыток: ' . $res);
        $sql='update cardindev cd set cd.attempts='.($res+1).' where id_cardindev='.$id_cardindev;
        $sql = 'UPDATE art_command_order SET ATTEMPTS=' . ($res + 1) . ' WHERE id_cardindev=' . $id_cardindev;
        try {
            //$res = DB::query(Database::UPDATE, $sql)
            //    ->execute(Database::instance('fb_mysql'));
            $res = DB::query(Database::UPDATE, $sql)
                ->execute(Database::instance('fb_firebird'));
        } catch (Exception $e) {
            //echo Debug::vars('38');
            $res = 64;
        }
        return $res;
    }

    public function getDeviceInfo($sn = false)// возращает имя (статус) устройства
    {
        // 0 - все ОК, можно работать,
        //-1 - указанный SN не найден
        //-2 - работа запрещена
        //-100 - не указан SN
		// на выходе массив array(device_id, work_status, is_registrator)
        $res = array();
        if (empty($sn)) {
            $res['device_id'] = '';
            $res['work_status'] = -100;
			$res['is_registrator']=0;
        } else {
			$query = DB::select()
				->from('DEVICE')
				->where('TAGNAME','=',$sn)
                ->execute(Database::instance('fb_firebird'))
                ->as_array();
			$query = $query[0];
                if ($query) {
					//// Log::instance())->add(Log::DEBUG, '(304) Ответ от БД'.Debug::vars($query).'SN='.$sn);
                    $res['device_id'] = Arr::get($query, 'ID_DEV');
                    $res['work_status'] = 0;
					$res['is_registrator']=Arr::get($query, 'FLAG');
				}
            }
        // Log::instance())->add(Log::DEBUG, '(388) Массив инфы'.Debug::vars($res).'SN='.$sn);

        return $res;
    }
	
	public function getTestTP()
	{
		//$query = DB::select('FP_TAMPLATE')
		//			->from('ZKSOFT_FP_TAMPLATE')
		//			->where('IDX_USER','=',1052);
		//$answer = $query->execute(Database::instance('fb_firebird'))->as_array();
		
		
		$db = new PDO('odbc:SDuo');
         $stmt = $db->prepare("select IDX_USER, FP_TAMPLATE, FP_LENGTH from ZKSOFT_FP_TAMPLATE cd where cd.IDX_USER=1052");
         $db->beginTransaction();
         $stmt->execute();
         $db->commit();
		
		
		
		$sql='select IDX_USER, FP_TAMPLATE, FP_LENGTH from ZKSOFT_FP_TAMPLATE cd where cd.IDX_USER=1052';
            $identQuery = DB::query(Database::SELECT, $sql)
                ->execute(Database::instance('fb_firebird'))
                ->as_array();
		echo Debug::vars('324', $identQuery);
		$identQuery = $identQuery[0];
		$hex = Arr::get($identQuery, 'FP_TAMPLATE' );
		echo $this->hexToStr($hex); 
		// Log::instance())->add(Log::DEBUG, '(388) Ajnj '.$this->hexToStr($hex));
		exit;
	}
	
	public function hexToStr($hex){
		$string='';
		//for($i=0;$i<strlen($hex)-1;$i+=2){
		foreach(str_split($hex,2) as $pair){
			//$string.=chr(hexdec($hex[$i].$hex[$i+1]));
			$string.=chr(hexdec($pair));
		}
		//$string = iconv('UTF-8','windows-1251',"string");
		return $string;
	}

    public function insertFaceTamplate($IDX_FINGER,$ID_DB,$ID_CARD,$IDX_USER,$FP_TAMPLATE,$FP_LENGTH)// отработка команды вставки BLOB в бд СКУД
    {
        //------------------- https://www.php.net/manual/ru/pdo.lobs.php
        // Log::instance())->add(Log::DEBUG, '(22) Начинаем запись лица в таблицу');
        // Log::instance())->add(Log::DEBUG, '(23) Лицо: '.$FP_TAMPLATE);

        $db = new PDO('firebird:dbname=172.28.40.100:C:\\Program Files (x86)\\Cardsoft\\DuoSE\\Access\\SHIELDPRO_REST.GDB','SYSDBA','temp');
        $stmt = $db->prepare("INSERT INTO ZKSOFT_FP_TAMPLATE (IDX_FINGER,ID_DB,ID_CARD,IDX_USER,FP_TAMPLATE,FP_LENGTH) VALUES(?,?,?,?,?,?)");


        $stmt->bindParam(1, $IDX_FINGER);
        $stmt->bindParam(2, $ID_DB);
        $stmt->bindParam(3, $ID_CARD);
        $stmt->bindParam(4, $IDX_USER);
        $stmt->bindParam(5, $FP_TAMPLATE);
        $stmt->bindParam(6, $FP_LENGTH);

        $db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $db->beginTransaction();
        $stmt->execute();
        $db->commit();
        $db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }
    public function insertFaceTamplate1($IDX_FINGER,$ID_DB,$ID_CARD,$IDX_USER,$FP_TAMPLATE,$FP_LENGTH)
    {

    // Log::instance())->add(Log::DEBUG, '(22) Начинаем запись лица в таблицу');
    // Log::instance())->add(Log::DEBUG, '(23) Лицо: '.$FP_TAMPLATE);
    $query = DB::insert('ZKSOFT_FP_TAMPLATE', array('IDX_FINGER', 'ID_DB', 'ID_CARD','IDX_USER', 'FP_TAMPLATE', 'FP_LENGTH'))
        ->values(array($IDX_FINGER,$ID_DB,$ID_CARD,$IDX_USER,$FP_TAMPLATE,$FP_LENGTH))
        ->execute(Database::instance('fb_firebird'));
    }

	public function ClearRegistrator($device_info, $pin){
		if(Arr::get($device_info,'work_status') == 0) 
		{
			try
			{
				$sql = "INSERT INTO CARDINDEV (ID_DB,DEVIDX,ID_DEV,OPERATION,ATTEMPTS,ID_PEP,TIME_STAMP,ID_CARDTYPE,FROMUSER)";
				$sql.= "VALUES (1,NULL,NULL,2,0,".$pin.",'now',4,'SYSDBA');";
				// Log::instance())->add(Log::DEBUG, '(411) Отправляем в БД запрос: ' . $sql);
				DB::query(Database::INSERT, $sql)
					->execute(Database::instance('fb_firebird'));
			}
			catch(Exception $e){
				// Log::instance())->add(Log::DEBUG, '(419) Команда на очистку добавлена');
			}
		}else{
			// Log::instance())->add(Log::DEBUG, '(415) Не найдено регистрационное устройство');
		}
		// Log::instance())->add(Log::DEBUG, '(419) Конец очистки');
	}
	public function InsertCard($ID_CARD, $pin, $cardType){
		try{
			$sql = "INSERT INTO CARD (ID_CARD,ID_DB,ID_PEP,ID_ACCESSNAME,TIMESTART,TIMEEND,NOTE,STATUS,\"ACTIVE\",FLAG,ID_CARDTYPE) VALUES ('" . $ID_CARD . "',1," . $pin . ",NULL,'now','1.01.2022',NULL,0,1,0,".$cardType.");";
			// Log::instance())->add(Log::DEBUG, '(421) Отправляем в БД запрос: ' . $sql);
			DB::query(Database::INSERT, $sql)
				->execute(Database::instance('fb_firebird'));
		}
		catch(Exception $e){
			// Log::instance())->add(Log::DEBUG, '(431) Карта добавлена');
		}
		// Log::instance())->add(Log::DEBUG, '(419) конец карты');
	}


}