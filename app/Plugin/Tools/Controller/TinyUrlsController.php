<?php
/*

Apply this route (/Config/routes.php):

Router::connect('/s/:id',
	array('plugin'=>'tools', 'controller'=>'tiny_urls', 'action'=>'go'),
	array('id'=>'[0-9a-zA-Z]+'));

Result:
/domain/s/ID

*/

class TinyUrlsController extends ToolsAppController {

	//public $uses = array('Tools.TinyUrl');

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
	 * main redirect function
	 * 2011-07-11 ms
	 */
	public function go() {
		if (empty($this->request->params['id'])) {
			throw new NotFoundException();
		}
		$entry = $this->TinyUrl->translate($this->request->params['id']);
		if (empty($entry)) {
			throw new NotFoundException();
		}

		//$message = $entry['TinyInt']['flash_message'];
		$url = $entry['TinyUrl']['target'];

		if (!empty($message)) {
			$type = !empty($entry['TinyUrl']['flash_type']) ? $entry['TinyUrl']['flash_type'] : 'success';
			$this->Common->flashMessage($message, $type);
		}
		$this->TinyUrl->up($entry['TinyUrl']['id'], array('field'=>'used', 'modify'=>true, 'timestampField'=>'last_used'));
		$this->redirect($url, 301);
	}


	public function admin_index() {
		//TODO

		if ($this->Common->isPost()) {
			$this->TinyUrl->set($this->request->data);
			if ($this->TinyUrl->validates()) {
				$id = $this->TinyUrl->generate($this->TinyUrl->data['TinyUrl']['url']);
				$this->Common->flashMessage('New Key: '.h($id), 'success');
				$url = $this->TinyUrl->urlByKey($id);
				$this->set(compact('url'));
				$this->request->data = array();
			}
		}

		$tinyUrls = $this->TinyUrl->find('count', array('conditions'=>array()));

		$this->set(compact('tinyUrls'));
	}

	public function admin_listing() {

	}

	public function admin_reset() {
		if (!$this->Common->isPost()) {
			throw new MethodNotAllowedException();
		}
		$this->TinyUrl->truncate();
		$this->Common->flashMessage(__('Done'), 'success');
		$this->Common->autoRedirect(array('action'=>'index'));
	}

}

