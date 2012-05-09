<?php

App::uses('CakeRequest', 'Network');

/**
 * A wrapper to access not only cakes request data about known mobile agents.
 * It also allows to whitelist and blacklist certain agents.
 * Last but not least it should be capable of detecting if it is a real user or a bot
 * 
 * @author Mark Scherer
 * @license MIT
 * @cakephp 2
 * 
 * 2011-04-05 ms
 */
class UserAgentLib extends CakeRequest {

	public $whitelist = array(
		'OMNIA7',
	);

	public $blacklist = array(
		'UP\.Browser'
	);
	
	public $searchBots = array(
		'Mirago' => 'HenryTheMiragorobot',
		'Google' => 'Googlebot',
		'Scooter' => 'Scooter',
		'MSN' => 'msnbot',
		'Yahoo' => 'YahooSeeker',
		'GigaBot' => 'GigaBot',
		'Linguee' => 'Linguee Bot',
		'WebAlta' => 'WebAlta Crawler',
		'Yandex' => 'Yandex',
		'Bot (no details)' => 'PF:INET',
		'Sitedomain' => 'Sitedomain-Bot',
		'Askpeter' => 'askpeter_bot'
	);
	
	public $path = null;

	public function __construct($agents = array()) {
		
		$this->path = VENDORS.'files'.DS;
	}


	public function isBot() {
		$file = $this->path.'bots.txt';
		if (file_exists($file)) {
			
		}
	}

	/**
	 * better handling of mobile agents
	 * including whitelist and blacklist
	 * 2011-04-05 ms
	 */
	public function isMobile() {
		$devices = $this->getMobileDevices();
		
		$pattern = '/' . implode('|', $devices) . '/i';
		return (bool)preg_match($pattern, env('HTTP_USER_AGENT'));
	}


	/**
	* checks bot against list
	* @param string $userAgent
	* @return string
	* //TODO use browscap here too if necessary
	* 2009-12-26 ms
	*/
	public function getAgent($agent) {
		if (empty($agent)) {
			 return '';
		}
		foreach ($this->searchBots as $name => $pattern) {
			if (eregi($pattern, $agent)) {
				return $name;
			}
		}
		return '';
	}
	
	/**
	* checks user against known platforms
	* @param string $userAgent
	* @return string
	* 2010-09-21 ms
	*/
	public function getPlatform($agent) {
		if (strpos($agent, "Win95") || strpos($agent, "Windows 95")) {
			return "Windows 95";
		}
		if (strpos($agent, "Win98") || strpos($agent, "Windows 98")) {
			return "Windows 98";
		}
		if (strpos($agent, "WinNT") || strpos($agent, "Windows NT")) {
			return "Windows NT";
		}
		if (strpos($agent, "WinNT 5.0") || strpos($agent, "Windows NT 5.0")) {
			return "Windows 2000";
		}
		if (strpos($agent, "WinNT 5.1") || strpos($agent, "Windows NT 5.1")) {
			return "Windows XP";
		}
		if (strpos($agent, "Windows")) { # OWN ONE
			return "Windows";
		}
		if (strpos($agent, "Linux")) {
			return "Linux";
		} 
		if (strpos($agent, "OS/2")) {
			return "OS/2";
		} 
		if (strpos($agent, "Sun")) {
			return "Sun OS";
		} 
		if (strpos($agent, "Macintosh") || strpos($agent, "Mac_PowerPC")) {
			return "Mac OS";
		} 
		return "";
	}


	/**
	 * fetches url with curl if available
	 * fallbacks: cake and php
	 * 2010-09-09 ms
	 **/
	public function getMobileDevices() {
		$is = array(); //$this->RequestHandler->mobileUA;
		$is = $this->_detectors['mobile']['options'];
		
		$is = array_merge($is, $this->_getMobileWhitelist());
		$blacklist = $this->_getMobileBlacklist();
		foreach ($blacklist as $agent) {
			if (in_array($agent, $is)) {
				$key = array_shift(array_keys($is, $agent));
				unset($is[$key]);
			}
		}
		return $is;
	}
	
	protected function _getMobileWhitelist() {
		$res = $this->whitelist;
		/*
		$file = $this->path.'mobile_devices.txt';
		if (file_exists($file)) {
			
		}
		*/
		return $res;
	}
	
	protected function _getMobileBlacklist() {
		$res = $this->blacklist;
		return $res;
	}

}


