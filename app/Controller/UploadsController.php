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
									
				/*
					TODO Accept payment 
				*/

				if ($data = $this->Uploader->upload('fileName')) {
					// Upload successful, do whatever
					//debug($data);
					
					//Add pertinent data to the array
					$totalCodes = $this->request->data['Upload']['total_codes'];
					$this->request->data['Upload'] = $data;
					$this->request->data['Upload']['total_codes'] = $totalCodes;
					$this->request->data['Upload']['test_token'] = $this->Upload->generateToken($this->request->data['Upload']['name']);
					$this->request->data['Upload']['test_token_active'] = 1;
					$this->request->data['Upload']['active'] = 1;
					$this->request->data['Upload']['user_id'] = $this->Upload->User->getLastInsertID();
					$this->request->data['Upload']['caption'] = $this->request->data['Upload']['custom_name'];
					unset($this->request->data['Upload']['custom_name']);
					
					//Generate file codes
					$this->request->data['Code'] = $this->Upload->Code->generateCodes($this->request->data,10);
					if ($this->Upload->saveAll($this->request->data)) {
						//Set the upload id
						$this->request->data['Upload']['id'] = $this->Upload->getLastInsertID();
						$this->Session->setFlash(__('Congratulations! Your account has been created and your file codes have been generated.'));
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash(__('Bummer :( Your file could NOT be uploaded.'));
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
	
	public function payment() {
		$payment_options = $this->Upload->Code->getPaymentOptions(); //Get the options array for the select list
		$this->set(compact('payment_options'));
	}

	public function paypal_set_ec() {
		if ($this->request->is('post')) {
			//build nvp string
			//use your own logic to get and set each variable
			$returnURL = Router::url(array('controller'=>'uploads','action'=>'paypal_return'),true);
			$cancelURL = Router::url(array('controller'=>'uploads','action'=>'paypal_cancel'),true);
			
			$codePrice = $this->Upload->Code->getPrice($this->request->data['Upload']['total_codes']);
			
			$currencyCode = 'USD';
			$paymentAmount = $codePrice;
			$paymentItemAmount = $codePrice;
			$itemName = $this->Upload->Code->getItemName($this->request->data['Upload']['total_codes']);
			$paymentQty = 1;
			$totalPaymentAmount = $codePrice;
			
			$nvpStr=
			 "RETURNURL=$returnURL&CANCELURL=$cancelURL"
			."&PAYMENTREQUEST_0_CURRENCYCODE=$currencyCode"
			."&PAYMENTREQUEST_0_AMT=$paymentAmount"
			."&PAYMENTREQUEST_0_ITEMAMT=$paymentItemAmount"
			."&PAYMENTREQUEST_0_PAYMENTACTION=Sale"
			."&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital"
			."&L_PAYMENTREQUEST_0_NAME0=$itemName"
			."&L_PAYMENTREQUEST_0_AMT0=$totalPaymentAmount"
			."&L_PAYMENTREQUEST_0_QTY0=$paymentQty"
			."&NOSHIPPING=1"
			;
			//do paypal setECCheckout
			App::import('Model','Paypal');
			$paypal = new Paypal();
			if($paypal->setExpressCheckout($nvpStr)) {
				$result = $paypal->getPaypalUrl($paypal->token);
			}else {
				$this->log($paypal->errors);
				$result = false;
			}
			
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
	 *redirects buyer after the buyer approves the payment
	 */
	public function paypal_return($id=null) {
		$payerId	= $this->request->url['PayerID'];
		$token		= $this->request->url['token'];
		//get nvp string
		//use your own logic to get and set each variable
		$nvpStr=
			 "TOKEN=$token&PAYERID=$payerId"
			."&PAYMENTREQUEST_0_CURRENCYCODE=SGD"
			."&PAYMENTREQUEST_0_AMT=10.00"	
			."&PAYMENTREQUEST_0_ITEMAMT=10.00"
			."&AYMENTREQUEST_0_PAYMENTACTION=Sale"
			."&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital"  
			."&L_PAYMENTREQUEST_0_NAME0=test"
			."&L_PAYMENTREQUEST_0_QTY0=1"
			."&L_PAYMENTREQUEST_0_AMT0=10.00"
			;
		
		debug($nvpStr);
		//do paypal setECCheckout
		App::import('Model','Paypal');
		$paypal = new Paypal();
		if($paypal->doExpressCheckoutPayment($nvpStr)) {
			$result = true;
		}else {
			$this->log($paypal->errors);
			$result = false;
		}

		if (false == $result) {
			$this->Session->setFlash(__('Error while making payment, Please try again', true),'default', array(), 'bad');
		} else {
			$this->Session->setFlash(__('Thank you for purchasing our deal.', true),'default', array(), 'good');
		}
		
		$this->render('paypal_back');
	}
	
	/**
	 * END PAYPAL PAYMENT RELATED
	 */
}

