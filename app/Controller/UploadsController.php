<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'Uploader.Uploader');
/**
 * Uploads Controller
 *
 * @property Upload $Upload
 */
class UploadsController extends AppController {

	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Upload->recursive = 0;
		$this->set('uploads', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		$this->set('upload', $this->Upload->read(null, $id));
	}
	
/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			
			//Validate the entered data
			//$this->Upload->set($this->request->data);
			//if($this->Upload->validates()){
				$this->request->data['User']['role'] = 'user'; //Set the default role
				$user = $this->Upload->User->register($this->request->data);
				if (!empty($user)) {
					
					//Create a folder based on the user's name
					$userFolder = $this->request->data['User']['custom_path'];
					
					//http://milesj.me/code/cakephp/uploader
					$this->Uploader = new Uploader(array(
										'tempDir' => TMP,
										'baseDir'	=> WWW_ROOT,
										'uploadDir'	=> 'files/uploads/'.$userFolder.'/',
										'maxNameLength' => 200
										));
										
					//Accept payment 
					

					if ($data = $this->Uploader->upload('fileName')) {
						// Upload successful, do whatever
						//debug($data);
						
						//Add pertinent data to the array
						$this->request->data['Upload'] = $data;
						$this->request->data['Upload']['test_token'] = $this->Upload->generateToken($this->request->data['Upload']['name']);
						$this->request->data['Upload']['test_token_active'] = 1;
						$this->request->data['Upload']['active'] = 1;
						$this->request->data['Upload']['user_id'] = $this->Upload->User->getLastInsertID();
						
						//Generate file codes
						$codes = $this->Upload->Code->generateCodes($this->request->data,10);
						if(!empty($codes)){
							$this->request->data['Code'] = $codes;
						}else{
							//Unable to generate codes 
						}
					
						debug($this->request->data);
						$this->Upload->create();
						if ($this->Upload->saveAll($this->request->data)) {
							$this->Session->setFlash(__('Congratulations! Your account has been created and your file codes have been generated.'));
							//Set the upload id
							$this->request->data['Upload']['id'] = $this->Upload->getLastInsertID();
							$this->redirect(array('action' => 'index'));
						} else {
							$this->Session->setFlash(__('Bummer :( Your file could NOT be uploaded.'));
						}
					}
				}	
			//}
		}
	
		$users = $this->Upload->User->find('list');
		$this->set(compact('users'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Upload->save($this->request->data)) {
				$this->Session->setFlash(__('The upload has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Upload->read(null, $id);
		}
		$users = $this->Upload->User->find('list');
		$this->set(compact('users'));
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
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		//Delete the physical file 
		$this->Uploader = new Uploader();
		$this->Uploader->delete($this->Upload->path);
		
		if ($this->Upload->delete()) {
			$this->Session->setFlash(__('Upload deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Upload was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Upload->recursive = 0;
		$this->set('uploads', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		$this->set('upload', $this->Upload->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Upload->create();
			if ($this->Upload->save($this->request->data)) {
				$this->Session->setFlash(__('The upload has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
			}
		}
		$users = $this->Upload->User->find('list');
		$this->set(compact('users'));
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Upload->save($this->request->data)) {
				$this->Session->setFlash(__('The upload has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Upload->read(null, $id);
		}
		$users = $this->Upload->User->find('list');
		$this->set(compact('users'));
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
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		if ($this->Upload->delete()) {
			$this->Session->setFlash(__('Upload deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Upload was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
