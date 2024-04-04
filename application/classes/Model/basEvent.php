<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_basEvent extends Model
{
	
	/*  "timestamp" => <small>float</small> 1678077114137
    "category" => <small>string</small><span>(6)</span> "access"
    "priority" => <small>string</small><span>(6)</span> "medium"
    "info" => <small>array</small><span>(2)</span> <span>(
        "text" => <small>string</small><span>(73)</span> "Valid identifier 3090069 was used, owner Administrator, lock 0 was opened"
        "model" => <small>array</small><span>(5)</span> <span>(
            "lock" => <small>integer</small> 0
            "owner" => <small>string</small><span>(13)</span> "Administrator"
            "number" => <small>string</small><span>(7)</span> "3090069"
            "apartment_address" => <small>string</small><span>(0)</span> ""
            "type" => <small>string</small><span>(4)</span> "card"
        )</span>
    )</span>
    "name" => <small>array</small><span>(2)</span> <span>(
        "text" => <small>string</small><span>(25)</span> "Lock opened by identifier"
        "key" => <small>string</small><span>(34)</span> "access_granted_by_valid_identifier" */
		
		
	public $timestamp = '';
	public $category = '-';
	public $info = 'bas';
	public $name = false;
	public $number='';
	
	
	
	public function init ($args)
	{
		//Log::instance()->add(Log::DEBUG, '#34-event-oop args=  '.Debug::vars($args));	//exit;
		$this->timestamp=Arr::get($args, 'timestamp');
		$this->number=Arr::get(Arr::get(Arr::get($args, 'info'), 'model'), 'number');
		$this->name=Arr::get(Arr::get($args, 'name'), 'key');
		
		
		return;
	
	}
	
	
	
	
	
}


