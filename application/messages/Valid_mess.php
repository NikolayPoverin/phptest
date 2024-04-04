<?php defined('SYSPATH') or die('No direct script access.'); 
$messages = array(
   'login'   =>
      array(
         'not_empty'       => ':field Не может быть пустым',
		 'max_length'   => ':field не должно превышать :param2 символов',
		 'alpha_numeric' => ':field должны быть только цифры и буквы',
      ),
   'password'  =>
      array(
         'not_empty'       => ':field Не может быть пустым',
         'max_length'   => ':field не должно превышать :param2 символов',
		 'alpha_numeric' => ':field должны быть только цифры и буквы',
      ),
	'id_dev'  =>
      array(
         'not_empty'       => ':field Не может быть пустым',
         'digit'   => ':field только цифры'
      ),
	'new_IP'  =>
      array(
         'not_empty'       => ':field Не может быть пустым',
         'IP'   => ':field Формат должен быть IP'
      ),
	  
	  
   'sex' =>
      array(
         'not_empty'       => ':field must not be empty',
      ),
   'type_error'   =>
      array(
         'regex'     => ':field не совпадает с заданным форматом',
      ),
   'descr'  =>
      array(
         'not_empty'       => ':field must not be empty',
      ),
   'img' =>
      array(
         'Upload::not_empty' => ':field must not be empty',
         'Upload::type' => ':filed is not allowed file type',
      ),
 
   'captcha'   =>
      array(
         'Captcha::valid'=> ':field captcha not valid'
      ),
	
	'new_card'   =>
      array(
         'not_empty'=> 'Номер карты не может быть пустым',
         'regex'=> 'Номер карты должен быть числом!',
         'Карта уже зарегистрирована.'=> 'Номер карты 999!',
         
      ),
	  
	'card'   =>
      array(
         't555'=> ':value Карта уже зарегистрирована.',
       ),
	  
	'tablo'   =>  array(
         'regex'=> 'Формат адреса табло неправильный.',
      ),
	  
	  'new_place_number'   =>  array(
		'Model_place::unique_place' => 'Машиноместо :value уже зарегистрировано в парковочной системе',
		'Model_place::maxcount_place' => 'Номер машиноместа :value превышает количество машиномест на парковке',
        'digit'=> 'Номер машиноместа :value должен быть цифрой',
      ),
	  
	  
);
 
return $messages;