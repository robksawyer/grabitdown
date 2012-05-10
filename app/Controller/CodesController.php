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
		$this->Code->recursive = -1;
		if(empty($upload_id)){
			$this->Session->setFlash(__('The data provided was not valid.'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		$this->paginate = array(
						'conditions'=>array('Code.upload_id'=>$upload_id),
						'limit'=>'100',
						'recursive'=>'-1'
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
		$testToken = false;
		$code = $this->Code->find('first',array('conditions'=>array(
				'Code.token'=>$token,
				'Code.upload_id'=>$upload_id
				),
				'recursive' => '0'
			));
		//Try the test token
		if(empty($code)){
			$upload = $this->Code->Upload->find('first',array('conditions'=>array(
					'Upload.test_token'=>$token,
					'Upload.id'=>$upload_id
					),
					'recursive' => '0'
				));
			$code = $this->Code->find('first',array('conditions'=>array(
					'Code.upload_id'=>$upload_id
					),
					'recursive' => '0'
				));
			$testToken = true;
		}
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
		//Set the code if to specify which code to save to.
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
			$online = true;
			$ip = $this->RequestHandler->getClientIP(); //Get the user ip
			//Request the location data
			if($online){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://myip.dnsomatic.com/");
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$public_ip = curl_exec($ch);
				curl_close($ch);
				//Don't do a GeoIP search if running locally
				if($public_ip != '127.0.0.1'){
					$ip = $public_ip;
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
			if($testToken){
				$this->Code->Upload->id = $upload_id;
				$this->Code->Upload->set(array(
					'test_token_count' => intval($upload['Upload']['test_token_count']) + 1
				));
				$this->Code->Upload->save();
			}else{
				$this->Code->set(array(
					'ipAddress' => $ip,
					'download_count' => intval($code['Code']['download_count']) + 1,
					'last_download_time' => $currentTime
				));
			}
			
			//Save the updated data to the code record
			if(!$this->Code->save()){
				//Record failed to update.
				$this->Session->setFlash(__('The code could not be saved. Please, try again.'));
			}
			
			//TODO: Possibly how the user some information on the view and then redirect to the download.
			//$this->header("refresh:5; url='pagetoredirect.php'");
			//Finally, download the file
			$this->sendFile($upload);
		}
	}
	
	/**
	 * Download the file
	 */
	protected function sendFile($upload = null) {
		//Download the file
		//http://www.dereuromark.de/2011/11/21/serving-views-as-files-in-cake2/
		$file = $upload['Upload'];
		$this->viewClass = 'Media';
		$this->set(array(
							//'id' => $file['name'],
							'name' => trim(basename($file['name'],$file['ext']),'.'),
							'download' => true,
							'extension' => $file['ext'],
							'path' => 'webroot'.DS.substr($file['path'],1)
							));
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
