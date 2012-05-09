<?php
if (!defined('CLASS_USER')) {
	define('CLASS_USER', 'User');
}

App::uses('ToolsAppController', 'Tools.Controller');

class QloginController extends ToolsAppController {

	public $uses = array('Tools.Qlogin');
	
	public $components = array('Tools.Common');

	public function beforeFilter() {		
		parent::beforeFilter();
		
		if (isset($this->Auth)) {
			$this->Auth->allow('go');
		}
	}


	/****************************************************************************************
	* ADMIN functions
	****************************************************************************************/



	/**
	 * main login function
	 * 2011-07-11 ms
	 */
	public function go($key) {
		$entry = $this->Qlogin->translate($key);
		$default = '/';
		if ($this->Session->read('Auth.User.id') && isset($this->Auth->loginRedirect)) {
			$default = $this->Auth->loginRedirect;
		}

		if (empty($entry)) {
			$this->Common->flashMessage(__('Invalid Key'), 'error');
			$this->Common->autoRedirect($default);
		}
		//die(returns($entry));
		$uid = $entry['CodeKey']['user_id'];
		$url = $entry['CodeKey']['url'];
		
		if (!$this->Session->read('Auth.User.id')) {
			$this->User = ClassRegistry::init(CLASS_USER);
			# needs to be logged in
			$user = $this->User->get($uid);
			if (!$user) {
				$this->Common->flashMessage(__('Invalid Account'), 'error');
				$this->Common->autoRedirect($default);
			}
			
			if ($this->Auth->login($user['User'])) {
				$this->Session->write('Auth.User.Login.qlogin', true);
				if (!Configure::read('Qlogin.suppressMessage')) {
					$this->Common->flashMessage(__('You successfully logged in via qlogin'), 'success');
				}
			}
		}
		$this->redirect($url);
	}


	public function admin_index() {
		//TODO
		
		if ($this->Common->isPost()) {
			$this->Qlogin->set($this->request->data);
			if ($this->Qlogin->validates()) {
				$id = $this->Qlogin->generate($this->Qlogin->data['Qlogin']['url'], $this->Qlogin->data['Qlogin']['user_id']);
				$this->Common->flashMessage('New Key: '.h($id), 'success');
				$url = $this->Qlogin->urlByKey($id);
				$this->set(compact('url'));
				$this->request->data = array();
			}
		}
		$this->User = ClassRegistry::init(CLASS_USER);
		$users = $this->User->find('list');
		
		$this->CodeKey = ClassRegistry::init('Tools.CodeKey');
		$qlogins = $this->CodeKey->find('count', array('conditions'=>array('type'=>'qlogin')));
		
		$this->set(compact('users', 'qlogins'));
	}
	
	public function admin_listing() {
		
	}

	public function admin_reset() {
		if (!$this->Common->isPost()) {
			throw new MethodNotAllowedException();
		}
		$this->CodeKey = ClassRegistry::init('Tools.CodeKey');
		$this->CodeKey->deleteAll(array('type'=>'qlogin'));
		$this->Common->flashMessage(__('Success'), 'success');
		$this->Common->autoRedirect(array('action'=>'index'));
	}

}

