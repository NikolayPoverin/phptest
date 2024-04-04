<?php defined('SYSPATH') or die('No direct script access.');

//class Controller_Errorpage extends Controller_Template {
class Controller_Cvss extends Controller{
	
	public function action_index()
	{
		
	Log::instance()->add(Log::NOTICE, 'Начало работы. Получены данные от CVS 54'.is_array($_POST).', '.  Debug::vars($_POST));
		
	}
	
	
	
}

