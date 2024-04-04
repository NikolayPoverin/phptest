<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_TestPanel extends Model
{
    public function AddPersone($surname,$name){
        $sql = 'INSERT INTO `CARDINDEV` (`Surname`, `Name`) VALUES (\''.$surname.'\', \''.$name.'\')';
        DB::query(Database::INSERT, $sql)
            ->execute(Database::instance('fb_mysql'));
            
    }
    public function AddIdentifier($identType,$content,$peopleID){
        $sql = 'INSERT INTO `art_people_identifiers` (`Type`, `Content`, `PeopleID`) VALUES ('.$identType.', `'.$content.'`, '.$peopleID.')';
        DB::query(Database::INSERT, $sql)
            ->execute(Database::instance('fb_mysql'));
    }
    public function AddOperation($deviceID,$operation,$peopleID,$cardindev){
		try{
        $sql = 'INSERT INTO CARDINDEV (OPERATION,ID_PEP,ID_DB) VALUES ('.$operation.', '.$peopleID.',1)';
        DB::query(Database::INSERT, $sql)
                 ->execute(Database::instance('fb_firebird'));
		}
		catch(Exception $e){
		}
    }
}