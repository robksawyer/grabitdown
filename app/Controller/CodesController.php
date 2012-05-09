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
		$this->Auth->allow('getit');
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
	public function getit($folder=null,$upload_id=null,$token=null){
		//Find the upload_id and then path by searching the folder and code
		//Find the code. If it exists check the folder
		$code = $this->Code->find('first',array('conditions'=>array(
				'Code.token'=>$token,
				'Code.upload_id'=>$upload_id
				)
			));
		if(empty($code)){
			$this->Session->setFlash(__('Invalid code'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		$this->Code->Upload->recursive = 0;
		$upload = $this->Code->Upload->find('first',array('conditions'=>array('Upload.id'=>$upload_id)));
		if(empty($upload)){
			$this->Session->setFlash(__('There was an issue finding your download. Please contact us with your download code.'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		$this->Code->id = $code['Code']['id'];
		//The user is adding a comment
		if ($this->request->is('post')) {
			if ($this->Code->save($this->request->data)) {
				$this->Session->setFlash(__('Your comment has been added. Thanks!'));
				$this->request->data['Code']['comment'] = ''; //Clear the comment field
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Your comment could not be added. Please, try again.'));
			}
		}else{
			$online = false;
			$ip = $this->RequestHandler->getClientIP(); //Get the user ip
			//Request the location data
			if($online){
				//Don't do a GeoIP search if running locally
				if($ip != '127.0.0.1'){
					//http://ipinfodb.com/ (Currently using the lite version - lower accuracy)
					$api_key = '7b3f09e733864e7658dbee31a1ba527f4ceaf1af2742e8996c43fcb76fccb7fe';
					try {
						$xml = Xml::toArray(Xml::build("http://api.ipinfodb.com/v3/ip-city?format=xml&key=$api_key&ip=$ip"));
						/*	array(
							'statusCode' => 'OK',
							'statusMessage' => '',
							'ipAddress' => '127.0.0.1',
							'countryCode' => '-',
							'countryName' => '-',
							'regionName' => '-',
							'cityName' => '-',
							'zipCode' => '-',
							'latitude' => '0',
							'longitude' => '0',
							'timeZone' => '-'
						)*/
					} catch (XmlException $e) {
						throw new InternalErrorException();
					}

					//Set region specific information, if possible
					if(!empty($xml['Response'])){
						$this->Code->set(array(
							'cityName' => $xml['Response']['cityName'],
							'regionName' => $xml['Response']['regionName'],
							'countryName' => $xml['Response']['countryName'],
							'zipCode' => $xml['Response']['zipCode'],
							'latitude' => $xml['Response']['latitude'],
							'longitude' => $xml['Response']['longitude'],
							'timeZone' => $xml['Response']['timeZone']
						));
					}
				}
			}
			
			//Set basic info
			$currentTime = date('Y-m-d H:i:s');
			$this->Code->set(array(
				'ipAddress' => $ip,
				'download_count' => intval($code['Code']['download_count']) + 1,
				'last_download_time' => $currentTime
			));
			
			//Download the file
			//http://www.dereuromark.de/2011/11/21/serving-views-as-files-in-cake2/
			//$file = Router::url($upload['Upload']['path'],true);
			$file = $upload['Upload']['path'];
			//If the upload name isn't concatenated on, the downloaded file name will be a collection of the URL params
			$this->request->params['pass'][4] = $upload['Upload']['name'];
			debug($this->request);
			$this->response->download($file);
			debug($this->response);
			//Save the updated data to the code record
			if(!$this->Code->save()){
				//Record failed to update.
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
		}
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
