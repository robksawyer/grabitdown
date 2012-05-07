<?php
App::uses('AppController', 'Controller');
/**
 * Codes Controller
 *
 * @property Code $Code
 */
class CodesController extends AppController {


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('download');
	}
	
/**
 * index method
 * @param upload_id The upload id to show codes for
 * @return void
 */
	public function index($upload_id=null) {
		$this->Code->recursive = 0;
		if(empty($upload_id)){
			$this->Session->setFlash(__('The data provided was not valid.'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		$this->paginate = array(
						'conditions'=>array('Code.upload_id'=>$upload_id),
						'limit'=>'100'
					);
		$codes = $this->paginate();
		$upload = $this->Code->Upload->find('first',array('conditions'=>array('Upload.id'=>$upload_id)));
		$this->set(compact('codes','upload'));
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Code->id = $id;
		if (!$this->Code->exists()) {
			throw new NotFoundException(__('Invalid code'));
		}
		$this->set('code', $this->Code->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Code->create();
			if ($this->Code->save($this->request->data)) {
				$this->Session->setFlash(__('The code has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
		}
		$uploads = $this->Code->Upload->find('list');
		$this->set(compact('uploads'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Code->id = $id;
		if (!$this->Code->exists()) {
			throw new NotFoundException(__('Invalid code'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Code->save($this->request->data)) {
				$this->Session->setFlash(__('The code has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Code->read(null, $id);
		}
		$uploads = $this->Code->Upload->find('list');
		$this->set(compact('uploads'));
	}
	
	/**
	 * @param folder string The folder that the download should be in
	 * @param upload_id int The upload's id
	 * @param token string The random download token
	 * @return upload_id
	 */
	public function download($folder=null,$upload_id=null,$token=null){
		//Save the user's ip address that downloads the file 
		$ip = $this->RequestHandler->getClientIp();
		debug($ip);
		//Find the upload_id and then path by searching the folder and code
		//Find the code. If it exists check the folder
		$code = $this->Code->find('all',array('conditions'=>array(
				'Code.token'=>$token,
				'Code.upload_id'=>$upload_id
				)
			));
		debug($code);
		
		//The user is adding a comment
		/*if ($this->request->is('post')) {
			$this->Code->id = 
			if ($this->Code->save($this->request->data)) {
				$this->Session->setFlash(__('The code has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
		}*/
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Code->id = $id;
		if (!$this->Code->exists()) {
			throw new NotFoundException(__('Invalid code'));
		}
		if ($this->Code->delete()) {
			$this->Session->setFlash(__('Code deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Code was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Code->recursive = 0;
		$this->set('codes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->Code->id = $id;
		if (!$this->Code->exists()) {
			throw new NotFoundException(__('Invalid code'));
		}
		$this->set('code', $this->Code->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Code->create();
			if ($this->Code->save($this->request->data)) {
				$this->Session->setFlash(__('The code has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
		}
		$uploads = $this->Code->Upload->find('list');
		$this->set(compact('uploads'));
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Code->id = $id;
		if (!$this->Code->exists()) {
			throw new NotFoundException(__('Invalid code'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Code->save($this->request->data)) {
				$this->Session->setFlash(__('The code has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Code->read(null, $id);
		}
		$uploads = $this->Code->Upload->find('list');
		$this->set(compact('uploads'));
	}

/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Code->id = $id;
		if (!$this->Code->exists()) {
			throw new NotFoundException(__('Invalid code'));
		}
		if ($this->Code->delete()) {
			$this->Session->setFlash(__('Code deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Code was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
