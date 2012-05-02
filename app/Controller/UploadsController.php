<?php
App::uses('AppController', 'Controller');
App::uses('Vendor', 'Uploader.Uploader');
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
			//Check to see if the user has selected a file
			$this->Upload->set($this->request->data);
			if ($this->Upload->validates()) {
				$this->request->data['User']['role'] = 'user'; //Set the default role
				$userData['User'] = $this->request->data['User'];
				$user = $this->Upload->User->register($userData);
				if (!empty($user)) {
					//Create a folder based on the user's name
					$userFolder = $this->request->data['User']['custom_path'];
					unset($this->request->data['User']);
				
					//http://milesj.me/code/cakephp/uploader
					$this->Uploader = new Uploader(array(
										'tempDir' => TMP,
										'baseDir'	=> WWW_ROOT,
										'uploadDir'	=> 'files/uploads/'.$userFolder.'/',
										'maxNameLength' => 200
										));
									
					if ($data = $this->Uploader->upload('fileName')) {
						// Upload successful, do whatever
						//debug($data);
				
						//Add pertinent data to the array
						$this->request->data['Upload'] = $data;
						$this->request->data['Upload']['test_token'] = $this->Upload->generateToken($this->request->data['Upload']['name']);
						$this->request->data['Upload']['test_token_active'] = 1;
						$this->request->data['Upload']['active'] = 0; //Disable until the user pays
						$this->request->data['Upload']['user_id'] = $this->Upload->User->getLastInsertID();
						$this->request->data['Upload']['caption'] = $this->request->data['Upload']['custom_name'];
						unset($this->request->data['Upload']['custom_name']);
				
						$this->Upload->create();
						if ($this->Upload->save($this->request->data)) {
							//Set the upload id
							$this->request->data['Upload']['id'] = $this->Upload->getLastInsertID();
							$this->Session->setFlash(__('Congratulations! Your almost done – just pay and you\'re done.'));
							$this->redirect(array('action' => 'payment',
												'uid'=>$this->request->data['Upload']['id'],
												'uuid'=>$this->Upload->User->getLastInsertID()
												));
						} else {
							$this->Session->setFlash(__('Bummer :( Your file could NOT be uploaded.'));
						}
					}
				}
			}
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
	
	/**
	 * PAYPAL PAYMENT RELATED
	 */
	
	/**
	 * @param int upload_id
	 */
	public function payment() {
		if(!empty($this->request->named['uid']) && !empty($this->request->named['uuid'])){
			$payment_options = $this->Upload->Code->getPaymentOptions(); //Get the options array for the select list
			$upload_id = $this->request->named['uid'];
			$user_id = $this->request->named['uuid'];
			$this->set(compact('payment_options','upload_id','user_id'));
		}else{
			$this->Session->setFlash(__('There was an error with your entry.'));
			$this->redirect(array('action' => 'add'));
		}
	}

/**
 * Starts the transaction and gets the token. Afterwards it's passed off to the final transaction method.
 * @param 
 */
	public function paypal_set_ec() {
		if ($this->request->is('post')) {
			
			//Check to make sure that the total codes haven't already been added to this file
			$upload = $this->Upload->read(null,$this->request->data['Upload']['id']);
			if(intval($upload['Upload']['total_codes']) > 0){
				if(intval($upload['Upload']['total_codes']) == count($upload['Code'])){
					$this->Session->setFlash(__('Error! No more codes can be added to this upload. Please re-upload the file.', true),'message_fail');
					$this->render('paypal_back_to_add');
					return;
				}
			}
			
			//do paypal setECCheckout
			App::import('Model','Paypal');
			$paypal = new Paypal();
			$codePrice = $this->Upload->Code->getPrice($this->request->data['Upload']['total_codes']);
			$itemName = $this->Upload->Code->getItemName($this->request->data['Upload']['total_codes']);
			$nvpStr = $paypal->buildNVPString($codePrice, $itemName,
											$this->request->data['Upload']['user_id'],
											$this->request->data['Upload']['id'],
											$this->request->data['Upload']['total_codes']
											);
			if($paypal->setExpressCheckout($nvpStr)) {
				$result = $paypal->getPaypalUrl($paypal->token);
			}else {
				$this->log($paypal->errors);
				$result = false;
			}
			
			//debug($this->request);
			
			if(false !== $result) {
				//The result should look like the following
				//https://www.sandbox.paypal.com/incontext?token=EC-09N44269CG053064W
				$this->redirect($result);
			}else {
				$this->Session->setFlash(__('Error while connecting to PayPal, Please try again', true));
			}
		}
		
		$payment_options = $this->Upload->Code->getPaymentOptions(); //Get the options array for the select list
		$this->set(compact('payment_options'));
	} 

	/**
	* page when user clicks on Cancel on Paypal page
	*/
	public function paypal_cancel($id=null) {
		$this->layout = 'clean';
		$this->render('paypal_back');
	}

	/**
	 * Redirects buyer after the buyer approves the payment
	 */
	public function paypal_return() {
		$this->layout = 'clean';
		
		$payerId	= $this->request->query['PayerID'];
		$token		= $this->request->query['token'];
		/*
		 	If the buyer approves payment,you can optionally call GetExpressCheckoutDetails 
			to obtain buyer details to display to your webpage.
		*/
		
		//do paypal setECCheckout
		App::import('Model','Paypal');
		$paypal = new Paypal();
		//Build the NVP string
		$total_codes = $this->request->params['named']['filter']['total_codes'];
		$codePrice = $this->Upload->Code->getPrice($total_codes);
		$itemName = $this->Upload->Code->getItemName($total_codes);
		$nvpCheckoutStr = $paypal->buildNVPCheckoutString($token,$payerId,$codePrice,$itemName);
		if($paypal->doExpressCheckoutPayment($nvpCheckoutStr)) {
			$result = true;
		}else {
			$this->log($paypal->errors);
			$result = false;
		}
		
		if ($result === false) {
			$this->Session->setFlash(__('Error while making payment, Please try again', true),'message_fail');
		} else {
			$user_id = $this->request->params['named']['filter']['uuid'];
			$upload_id = $this->request->params['named']['filter']['uid'];
			
			//Generate file codes
			$codeCreationResult = $this->Upload->Code->generateCodes($upload_id,$total_codes);
			if($codeCreationResult === true){
				//Codes generated successfully
				$this->Session->setFlash(__('Thank you for purchasing.', true),'message_success');
				
				//Add the total codes to the uploaded file for easy calculation
				$this->Upload->read(null, $upload_id);
				$this->Upload->set(array(
										'total_codes' => $total_codes,
										'active' => 1
										)
									);
				$this->Upload->save();
			}else{
				//Code generation failed
				$this->Session->setFlash(__('Your purchase has completed, but there was an issue with the code generation.', true),'message_fail');
			}
		}
		
		$this->render('paypal_back');
	}
	
	/**
	 * END PAYPAL PAYMENT RELATED
	 */
}

