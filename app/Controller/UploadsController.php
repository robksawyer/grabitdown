<?php
App::uses('AppController', 'Controller');
App::uses('Vendor', 'Uploader.Uploader');
/**
 * Uploads Controller
 *
 * @property Upload $Upload
 */
class UploadsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('add','payment','paypal_set_ec','paypal_return','paypal_cancel');
	}
	
	/**
	 * admin area
	 *
	 * @return void
	 */
	public function admin() {
		$this->Upload->recursive = 0;
		//...
	}
	
	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		//Only show the uploads for the logged in user
		$this->Upload->recursive = 0;
		if(!$this->logged_in){
			$this->Session->setFlash(__('You must be logged in to view this page.'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		$this->paginate = array(
									'conditions'=>array('Upload.user_id'=>$this->current_user['id'])
									);
		$this->set('uploads', $this->paginate());
		$this->set(compact('auth'));
	}

	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->Upload->recursive = 0;
		
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) {
			throw new NotFoundException(__('Invalid upload'));
		}
		$upload = $this->Upload->read(null,$id);
		$auth = $this->Auth->user();
		if(empty($auth)){
			$this->Session->setFlash(__('You must be logged in to view this page.'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		//Check to see if the user should be able to view the file
		if($auth['id'] != $upload['Upload']['user_id']){
			$this->Session->setFlash(__('You must be logged in to view this page.'));
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
		
		$this->paginate = array(
			'Code'=>array(
				//'conditions' => array('Code.active' => '1'),
				'limit' => 10
			)
		);
		$upload['Code'] = $this->paginate('Code'); //Paginate the code results
		$active_codes = $this->Upload->Code->find('count',array('conditions'=>array(
			'Code.upload_id'=>$id,
			'Code.active'=>1,
		)));
		$all_codes = $this->Upload->Code->find('all',array('conditions'=>array(
																										'Code.upload_id'=>$id
																									),
																			'fields'=>array('Code.download_count')
																		)
																	);
		$all_codes = Set::format($all_codes,'{0}',array('{n}.Code.download_count'));
		$total_downloads = array_sum($all_codes);
		$this->set(compact('upload','active_codes','total_downloads'));
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
				$this->request->data['User']['role'] = 4; //Set the default role to user (see bootstrap.php)
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
							$this->Session->setFlash(__('Congratulations '.$user['User']['fullname'].'! You\'re almost done, we\'re just waiting for your payment.'));
							$this->redirect(array('action' => 'payment',
												'uid'=>$this->request->data['Upload']['id'],
												'uuid'=>$this->Upload->User->getLastInsertID()
												));
						} else {
							$this->Session->setFlash(__('Bummer :( Your file could NOT be uploaded.'));
							$this->log('The file could not be uploaded.','upload_debug');
							Debugger::log($data);
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
			//Get user account data
			$user = $this->Upload->User->read(null,$user_id);
			$this->set(compact('payment_options','upload_id','user_id','user'));
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
			//Abort if cancel button was pressed
			if (isset($this->request->data['cancel'])) {
				//Pass the user along to an action that will clear the account and the upload
				$this->redirect(array('controller'=>'users','action' => 'clear_user_data',$this->request->data['Upload']['user_id']));
				break;
			}
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
		//Pass the user along to an action that will clear the account and the upload
		debug($this->request);
		debug($this->request->data['Upload']['user_id']);
		//$this->redirect(array('controller'=>'users','action' => 'clear_user_data',$this->request->data['Upload']['user_id']));
		break;
		//$this->render('paypal_back');
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
			$this->log($paypal->errors,'paypal_debug');
			$result = false;
		}
		
		if ($result === false) {
			$this->Session->setFlash(__('Error while making payment, Please try again', true),'message_fail');
			
			// Send an email to the administor to see if he can resolve
			$message = 'There was an error generating the codes for the user\'s upload.';
			$message .= '\n The user\'s name is: '. $user['User']['fullname'];
			$message .= '\n The user\'s email is: '. $user['User']['email'];
			$message .= '\n Upload ID: '. $upload_id;
			$message .= '\n Total codes to generate: '. $total_codes;
			$subject = 'Payment error';
			
			$this->_sendErrorEmail($message,$subject);
			
		} else {
			$user_id = $this->request->params['named']['filter']['uuid'];
			$upload_id = $this->request->params['named']['filter']['uid'];
			
			//Generate file codes
			$codeCreationResult = $this->Upload->Code->generateCodes($upload_id,$total_codes);
			if($codeCreationResult === true){
				//Activate the user account
				$this->Upload->User->activate($user_id);
				
				//Add the total codes to the uploaded file for easy calculation and make the upload active
				$this->Upload->read(null, $upload_id);
				$this->Upload->set(array(
										'total_codes' => $total_codes,
										'active' => 1
										)
									);
				$this->Upload->save();
				
				//Send the user their activation email
				$user = $this->Upload->User->read(null,$user_id);
				$this->_sendActivationEmail(null,array('user'=>$user));
				
				//Codes generated successfully
				//$this->Session->setFlash(__('Thank you for purchasing.', true),'message_success');
			}else{
				//Code generation failed
				$this->Session->setFlash(__('Your purchase has completed, but there was an issue with the code generation. The administrator has been notified via email.', true),'message_fail');
				
				// Send an email to the administor to see if he can resolve
				$message = 'There was an error generating the codes for the user\'s upload.';
				$message .= '\n The user\'s name is: '. $user['User']['fullname'];
				$message .= '\n The user\'s email is: '. $user['User']['email'];
				$message .= '\n Upload ID: '. $upload_id;
				$message .= '\n Total codes to generate: '. $total_codes;
				$subject = 'Code Generation Error';
				$this->_sendErrorEmail($message,$subject);
			}
		}
		//This window will close and redirect the user to the login page. To change the redirect, update this in the Views -> Uploads -> paypal_back.ctp file.
		$this->render('paypal_back');
	}
	
	/**
	 * END PAYPAL PAYMENT RELATED
	 */
	
	/**
	* Checks if the email is in the system and authenticated, if yes create the token
	* save it and send the user an email
	*
	* @param boolean $admin Admin boolean
	* @param array $options Options
	* @return void
	*/
	protected function _sendActivationEmail($admin = null, $options = array()) {
		//Parse the options
		$user = $options['user'];
		if (!empty($user)) {
			$options = array(
							'layout'=>'signup_activate',
							'subject'=>'Verify and activate your account',
							'view'=>'default'
							);
			$viewVars = array('token'=>$user['User']['email_token'],'user_name'=>$user['User']['fullname']);

			//Send the email
			$this->_sendEmail($user['User']['email'],$options,$viewVars);
			
			//$this->set('token', $user['User']['email_token']);
			if ($admin) {
				$this->Session->setFlash(sprintf(
					__('%s has been sent an email with instructions to activate their account.', true),
					$user['User']['email']),'message_success');
				//$this->redirect(array('action' => 'index', 'admin' => true));
			} else {
				$this->Session->setFlash(__('Thanks for your purchase! You should receive an email shortly with the instructions needed to activate your account.', true),'message_success');
				//$this->redirect(array('action' => 'login'));
			}
		} else {
			// Send an email to the administor to see if he can resolve based on the PayPal email address used.
			$message = 'The user\'s activation email didn\'t send because the email wasn\'t valid';
			$message .= '\n The user\'s name is: '. $user['User']['fullname'];
			$message .= '\n The user\'s email is: '. $user['User']['email'];
			
			$this->_sendErrorEmail($message);
		}
	}
	
	/**
	* Sends the verification email
	*
	* This method is protected and not private so that classes that inherit this
	* controller can override this method to change the verification mail sending
	* in any possible way.
	*
	* @param string $to Receiver email address
	* @param array $options EmailComponent options
	* @param array $viewVars view variables to pass along
	* @return boolean Success
	*/
	protected function _sendEmail($to = null,$options = array(),$viewVars=array()) {
		if(!empty($to)){
			if(empty($options['view'])){
				$options['view'] = 'default';
			}
			
			$email = new CakeEmail('standard'); //Use the standard config template
			
			try{
				// success message
				$email->template($options['view'], $options['layout'])
							->emailFormat('html')
							->to($to)
							->subject($options['subject'])
							->viewVars($viewVars)
							->send();
				return true;
			}catch(Exception $e){
				// failure message
				$error_message = $e->getMessage();
				debug($error_message);
				$this->Session->setFlash(__('There was an error sending your email verification email. Please contact us.\n'.$error_message, true),'message_fail');
			}
		}
		return false;
	}

}

