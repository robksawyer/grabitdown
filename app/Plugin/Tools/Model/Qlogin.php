<?php
//TODO: later Auth Plugin

App::uses('ToolsAppModel', 'Tools.Model');

/**
 * Manage Quick Logins
 * 
 * @author Mark Scherer
 * @cakephp 2.0
 * @license MIT
 * 2011-11-17 ms
 */
class Qlogin extends ToolsAppModel {

	public $useTable = false;

	public $validate = array(
		'url' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateUrl' => array(
				'rule' => array('validateUrl', array('deep'=>false, 'sameDomain'=>true, 'autoComplete'=>true)),
				'message' => 'valErrInvalidQloginUrl',
				'last' => true
			)
		),
		'user_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),		
			/*
			'validateUnique' => array(
				'rule' => array('validateUnique', array('url')),
				'message' => 'key already exists',
			),
			*/
		),
	);
	
	protected function _useKey($key) {
		if (!isset($this->CodeKey)) {
			$this->CodeKey = ClassRegistry::init('Tools.CodeKey');
		}
		return $this->CodeKey->useKey('qlogin', $key);
	}
	
	protected function _newKey($uid, $content) {
		if (!isset($this->CodeKey)) {
			$this->CodeKey = ClassRegistry::init('Tools.CodeKey');
		}
		return $this->CodeKey->newKey('qlogin', null, $uid, $content);
	}
	
	public function translate($key) {
		$res = $this->_useKey($key);
		if (!$res) {
			return false;
		}
		$res['CodeKey']['content'] = unserialize($res['CodeKey']['content']);
		$res['CodeKey']['url'] = Router::url($res['CodeKey']['content'], true);
		return $res;
	}
	
	/**
	 * generates a qlogin key
	 * @param mixed $url
	 * @param string $uid
	 * @return string $key
	 * 2011-07-12 ms
	 */
	public function generate($url, $uid) {
		$content = serialize($url);
		return $this->_newKey($uid, $content);
	}
	
	public function urlByKey($key) {
		return Router::url(array('admin'=>'', 'plugin'=>'tools', 'controller'=>'qlogin', 'action'=>'go', $key), true);
	}
	
	/**
	 * makes an absolute url string ready to input anywhere
	 * uses generate() internally to get the key
	 * @param mixed $url
	 * @return string $url (absolute) 
	 */
	public function url($url, $uid = null) {
		if ($uid === null) {
			$uid = $this->Session->read('Auth.User.id');
		}
		$key = $this->generate($url, $uid);
	 	return $this->urlByKey($key);
	}
	
}
