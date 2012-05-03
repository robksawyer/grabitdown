<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	
	/**
	 * CakePHP beforeFilter
	 *
	 * @return void
	 * @author Rob Sawyer
	 **/
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->authenticate = array('Form' => array('fields' => array('username' => 'email', 'password' => 'passwd')));
		$this->Auth->allow('logout','clear_user_data','delete','reset','reset_password','verify','test_email');
		
		/*if (!Configure::read('App.defaultEmail')) {
			Configure::write('App.defaultEmail', 'noreply@' . env('HTTP_HOST'));
		}*/
	}
	
	/**
	 * Logs the user in
	 *
	 * @return void
	 * @author Rob Sawyer
	 **/
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
	}

	/**
	 * Logs the user out
	 *
	 * @return void
	 * @author Rob Sawyer
	 **/
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
	
	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		//Delete the user's data
		$this->deleteUserData($id);
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	/**
	 * Remove all of the user's old content
	 * @return bool
	 */
	protected function deleteUserData($user_id=null){
		$user = $this->User->read(null,$user_id);
		//Find all files created by the user and delete them
		$uploads = $this->User->Upload->find('all',array('user_id'=>$user_id));
		//Delete the physical files
		$this->Uploader = new Uploader();
		foreach($uploads as $upload){
			$this->Uploader->delete($upload['Upload']['path']);
		}
		//Delete the container directory (custom_path)
		$userFolderPath = $this->Uploader->baseDir.$this->Uploader->uploadDir.$user['User']['custom_path'];
		if(!empty($userFolderPath)){
			if (is_dir($userFolderPath)) {
			    rmdir($userFolderPath);
			}
		}
		return true;
	}
	
	/**
	 * Clears the user's data.
	 * The data is only cleared if the user account is not active. The account is likely not active because they didn't pay.
	 * @param id int
	 * @return void
	 */
	public function clear_user_data($id=null){
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$user = $this->User->read(null,$id);
		if(!$user['User']['active']){
			//Delete the user's data
			$this->deleteUserData($id);
			if ($this->User->delete()) {
				$this->Session->setFlash(__('Your changes were not saved and your account has been removed.', true));
				$this->redirect(array('controller'=>'uploads','action' => 'add'));
			}
		}else{
			/*
			 This should never happen because the user can't get past the add page if their 
			account already exists. They have to upload new files via the admin area.
			*/
			$this->Session->setFlash(__('Your changes were not saved.', true));
			$this->redirect(array('controller'=>'uploads','action' => 'add'));
		}
	}
	
	/**
	 * Confirm email action
	 *
	 * @param string $type Type
	 * @return void
	 */
	public function verify($type = 'email') {
		if (isset($this->request->params['pass']['1'])){
			$token = $this->request->params['pass']['1'];
		} else {
			$this->redirect(array('action' => 'login'), null, true);
		}

		if ($type === 'email') {
			$data = $this->User->validateToken($token);
		} elseif($type === 'reset') {
			$data = $this->User->validateToken($token, true);
		} else {
			$this->Session->setFlash(__d('users', 'The url you accessed is no longer valid', true));
			$this->redirect(array('action' => 'login'));
		}

		if ($data !== false) {
			$email = $data['User']['email'];
			$passwd = $data['User']['passwd'];
			unset($data['User']['email']);
			unset($data['User']['passwd']);

			if ($type === 'reset') {
				$newPassword = $data['User']['passwd'];
				$data['User']['passwd'] = $this->Auth->password($newPassword);
			}

			if ($type === 'email') {
				$data['User']['active'] = 1;
			}

			if ($this->User->save($data, false)) {
				if ($type === 'reset') {
					
					$options = array(
										'layout'=>'signup_activate',
										'subject'=>__d('users', 'Password Reset', true),
										'view'=>'default'
										);
					$viewVars = array('data'=>$data,'newPassword'=>$newPassword);
					$this->_sendEmail($email,$options,$viewVars);
					
					$this->Session->setFlash(__d('users', 'Your password was sent to your registered email account', true));
					$this->redirect(array('action' => 'login'));
				} else {
					unset($data);
					$data['User']['active'] = 1;
					$this->User->save($data);
					$this->Session->setFlash(__d('users', 'Your e-mail has been validated!', true));
					//Log the user in with the auto generated password and then send them along to the create password page
					$loginData = array('username'=>$email,'password'=>$passwd);
					$this->Auth->loginRedirect = array('admin'=>false,'controller'=>'users','action'=>'create_password');
					$this->Auth->login($loginData);
					//$this->redirect(array('action' => 'create_password'));
				}
			} else {
				$this->Session->setFlash(__d('users', 'There was an error trying to validate your e-mail address. Please check your e-mail for the URL you should use to verify your e-mail address.', true));
				$this->redirect(array('action' => 'login'));
			}
		} else {
			$this->Session->setFlash(__d('users', 'The url you accessed is no longer valid', true));
			$this->redirect('/');
		}
	}
	
	/**
	* Allows the user to enter a new password, it needs to be confirmed
	* @return void
	*/
	public function change_password() {
		if (!empty($this->request->data)) {
			$this->request->data['User']['id'] = $this->Auth->user('id');
			if ($this->User->changePassword($this->request->data)) {
				$this->Session->setFlash(__d('users', 'Password changed.', true));
				$this->redirect('/');
			}
		}
	}
	
	/**
	* Allows the user to create a password. This happens after the user verifies their email address
	* @return void
	*/
	public function create_password() {
		if (!empty($this->request->data)) {
			$this->request->data['User']['id'] = $this->Auth->user('id'); //Get the logged in user's id
			if ($this->User->verifyNewPassword($this->request->data)) {
				$this->Session->setFlash(__d('users', 'Password created.', true));
				$this->redirect(array('controller'=>'uploads','action'=>'index'));
			}
		}
	}

	/**
	* Reset Password Action
	*
	* Handles the trigger of the reset, also takes the token, validates it and let the user enter
	* a new password.
	*
	* @param string $token Token
	* @param string $user User Data
	* @return void
	*/
	public function reset_password($token = null, $user = null) {
		if(empty($token)) {
			$admin = false;
			if($user) {
				$this->request->data = $user;
				$admin = true;
			}
			$this->_sendPasswordReset($admin); //Show the forgot password form
		} else {
			$this->__resetPassword($token); //Show the reset password form
		}
	}
	
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	/**
	 * admin_view method
	 *
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * admin_edit method
	 *
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	/*public function test_email(){
		$options = array(
							'layout'=>'signup_activate',
							'subject'=>'Awesome it worked',
							'view'=>'default'
							);
		$user = array();
		$user['User'] = array();
		$user['User']['fullname'] = "Rob Sawyer";
		$user['User']['email_token'] = "ASF23asfasfK";
		$viewVars = array('user'=>$user);
		$this->_sendEmail("robksawyer@gmail.com",$options,$viewVars);
	}*/

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
			$email->template($options['view'], $options['layout'])
						->emailFormat('html')
						->to($to)
						->subject($options['subject'])
						->viewVars($viewVars)
						->send();
			return true;
		}
		return false;
	}
	
	/**
	* Checks if the email is in the system and authenticated, if yes create the token
	* save it and send the user an email
	*
	* @param boolean $admin Admin boolean
	* @param array $options Options
	* @return void
	*/
	protected function _sendPasswordReset($admin = null, $options = array()) {			
		if (!empty($this->request->data)) {
			
			$user = $this->User->passwordReset($this->request->data);

			if (!empty($user)) {
				$options = array(
									'layout'=>'password_reset_request',
									'subject'=>'Password Reset',
									'view'=>'default'
									);
				$viewVars = array('token'=>$user['User']['password_token'],'user'=>$user);
	
				//Send the email
				$this->_sendEmail($user['User']['email'],$options,$viewVars);
				
				$this->set('token', $user['User']['password_token']);
				if ($admin) {
					$this->Session->setFlash(sprintf(
						__d('users', '%s has been sent an email with instruction to reset their password.', true),
						$user['User']['email']));
					$this->redirect(array('action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash(__d('users', 'You should receive an email with further instructions shortly.', true));
					$this->redirect(array('action' => 'login'));
				}
			} else {
				$this->Session->setFlash(__d('users', 'No user was found with that email.', true));
				$this->redirect($this->referer('/'));
			}
		}
		$this->render('request_password_change');
	}

	/**
	* Sets the cookie to remember the user
	*
	* @param array Cookie component properties as array, like array('domain' => 'yourdomain.com')
	* @param string Cookie data keyname for the userdata, its default is "User". This is set to User and NOT using the 
	* model alias to make sure it works with different apps with different user models accross different (sub)domains.
	* @return void
	* @link http://api13.cakephp.org/class/cookie-component
	*/
	/*protected function _setCookie($options = array(), $cookieKey = 'User') {
		if (empty($this->request->data['User']['remember_me'])) {
			$this->Cookie->delete($cookieKey);
		} else {
			$validProperties = array('domain', 'key', 'name', 'path', 'secure', 'time');
			$defaults = array(
				'name' => 'rememberMe');

			$options = array_merge($defaults, $options);
			foreach ($options as $key => $value) {
				if (in_array($key, $validProperties)) {
					$this->Cookie->{$key} = $value;
				}
			}

			$cookieData = array();
			$cookieData[$this->Auth->fields['username']] = $this->request->data['User'][$this->Auth->fields['email']];
			$cookieData[$this->Auth->fields['password']] = $this->request->data['User'][$this->Auth->fields['password']];
			$this->Cookie->write($cookieKey, $cookieData, true, '1 Month');
		}
		unset($this->request->data['User']['remember_me']);
	}*/

	/**
	* This method allows the user to change his password if the reset token is correct
	*
	* @param string $token Token
	* @return void
	*/
	private function __resetPassword($token) {
		$user = $this->User->checkPasswordToken($token);
		if(empty($user)) {
			$this->Session->setFlash(__d('users', 'Invalid password reset token, try again.', true));
			$this->redirect(array('action' => 'reset_password'));
		}

		if (!empty($this->request->data)) {
			if ($this->User->resetPassword(Set::merge($user, $this->request->data))) {
				$this->Session->setFlash(__d('users', 'Password changed, you can now login with your new password.', true));
				$this->redirect($this->Auth->loginAction);
			}
		}

		$this->set('token', $token);
	}
}
