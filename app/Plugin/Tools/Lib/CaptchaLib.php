<?php

App::uses('Security', 'Utility');
if (!defined('FORMAT_DB_DATE')) {
	define('FORMAT_DB_DATETIME', 'Y-m-d H:i:s');
}

/**
 * used by captcha helper and behavior
 */
class CaptchaLib {

	public static $defaults = array (
			'dummyField' => 'homepage',
			'method' => 'hash',
			'type' => 'both',			
			'checkSession' => false,
			'checkIp' => false,
			'salt' => '',
	);

	# what type of captcha
	public static $types = array('passive', 'active', 'both');
	
	# what method to use
	public static $methods = array('hash', 'db', 'session');


	public function __construct() {
		
	}
	
	
	/**
	 * @param array $data:
	 * - captcha_time, result/captcha
	 * @param array $options:
	 * - salt (required)
	 * - checkSession, checkIp, hashType (all optional)
	 * 2011-06-11 ms 
	 */
	public static function buildHash($data, $options, $init = false) {
		if ($init) {
			$data['captcha_time'] = time();
			$data['captcha'] = $data['result'];
		}
		
		$hashValue = date(FORMAT_DB_DATETIME, (int)$data['captcha_time']).'_';
		$hashValue .= ($options['checkSession'])?session_id().'_' : '';
		$hashValue .= ($options['checkIp'])?env('REMOTE_ADDR').'_' : '';
		$hashValue .= $data['captcha'];
		
		return Security::hash($hashValue, isset($options['hashType']) ? $options['hashType'] : null, $options['salt']);
	}

}