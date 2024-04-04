<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Door extends Model
{
	//https://app.swaggerhub.com/apis/basip/panel-web-api/2.3.0#/admin/post_desktop_message
	
	//public $baseurl = 'http://10.200.20.2/api';
	public $baseurl = '';
	
	public $account_type = '-';
	
	public $server_type = 'bas';
	
	public $statusOnline = false;
	
	public $tokenTTT='';
	
	public function init ($ip)
	{
	
		$this->baseurl=$ip;
		$a=$this->makeRequest();
		if($a)
		{
		$this->statusOnline=True;
		$this->account_type=Arr::get($a, 'account_type');
		$this->tokenTTT=Arr::get($a, 'token');
		} else {
		$this->statusOnline=False;
		$this->account_type='no';
			
		}
		return;
	
	}
	public function IntToIP ($intIP)// преобразование IP адреса
	{
		$mm= explode (".", long2ip($intIP));
		$tt=$mm[3].'.'.$mm[2].'.'.$mm[1].'.'.$mm[0];
		
		return $tt;
	}
	
	public function getBasDeviceList($serverList)// получить id_dev контроллеров (вызывных панелей) типа bas
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
	
	

	
}


