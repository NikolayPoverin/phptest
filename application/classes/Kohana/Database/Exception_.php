<?php defined('SYSPATH') OR die('No direct script access.');

class Database_Exception extends Kohana_Database_Exception {
	
	public static function handler(Exception $e) 
	{
		
		//echo Debug::vars('5', 'Database_Exception extends Kohana_Database_Exception');
		$this->redirect('errorpage?err='.Text::limit_chars($e->getMessage()));
	}
	
}
