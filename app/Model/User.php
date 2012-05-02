<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
/**
 * User Model
 *
 * @property File $File
 */
class User extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'custom_path';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Utils.Sluggable' => array(
			'label' => 'custom_path',
			'method' => 'multibyteSlug'
		)
	);
	
/**
 * Validation parameters
 *
 * @var array
 */
	public $validate = array(
		'fullname' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'required' => true, 
				'allowEmpty' => false,
				'message' => 'Please enter your name, so we\'ll know what to call you.'
			),
			'name_min' => array(
				'rule' => array('minLength', '3'),
				'message' => 'Your name must be more than 3 characters, right?'
			)
		),
		'custom_path' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'required' => true, 
				'allowEmpty' => false,
				'message' => 'Please enter a custom URL'
			),
			'alpha' => array(
				'rule'=>array('alphaNumeric'), 
				'message' => 'The custom URL must be alphanumeric'
			),
			'unique_custom_path' => array(
				'rule'=>array('isUnique','custom_path'),
				'message' => 'This custom URL is already in use.'
			),
			'custom_path_min' => array(
				'rule' => array('minLength', '3'),
				'message' => 'The custom URL must have at least 3 characters.'
			)
		),
		'email' => array(
			'isValid' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'Please enter a valid email address.'
			),
			'isUnique' => array(
				'rule' => array('isUnique','email'),
				'message' => 'This email is already in use. Maybe you should login.'
			)
		),
		'tos' => array(
			'rule' => array('custom','[1]'),
			'message' => 'You must agree to the terms of service.'
		)
	);
	
	/**
	 * 
	 */
	public function beforeSave() {
		if (isset($this->data[$this->alias]['passwd'])) {
			$this->data[$this->alias]['passwd'] = AuthComponent::password($this->data[$this->alias]['passwd']);
		}
		return true;
	}

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'user_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	
	/**
	 * Custom validation method to ensure that the two entered passwords match
	 *
	 * @param string $password Password
	 * @return boolean Success
	 */
		public function confirmPassword($password = null) {
			if ((isset($this->data[$this->alias]['passwd']) && isset($password['temppassword']))
				&& !empty($password['temppassword'])
				&& ($this->data[$this->alias]['passwd'] === $password['temppassword'])) {
				return true;
			}
			return false;
		}

	/**
	 * Compares the email confirmation
	 *
	 * @param array $email Email data
	 * @return boolean
	 */
		public function confirmEmail($email = null) {
			if ((isset($this->data[$this->alias]['email']) && isset($email['confirm_email']))
				&& !empty($email['confirm_email'])
				&& (strtolower($this->data[$this->alias]['email']) === strtolower($email['confirm_email']))) {
					return true;
			}
			return false;
		}

	/**
	 * Validates the user token
	 *
	 * @param string $token Token
	 * @param boolean $reset Reset boolean
	 * @param boolean $now time() value
	 * @return mixed false or user data
	 */
		public function validateToken($token = null, $reset = false, $now = null) {
			if (!$now) {
				$now = time();
			}

			$this->recursive = -1;
			$data = false;
			$match = $this->find(array(
				$this->alias . '.email_token' => $token),
				'id, email, email_token_expires, role');

			if (!empty($match)){
				$expires = strtotime($match[$this->alias]['email_token_expires']);
				if ($expires > $now) {
					$data[$this->alias]['id'] = $match[$this->alias]['id'];
					$data[$this->alias]['email'] = $match[$this->alias]['email'];
					$data[$this->alias]['email_authenticated'] = '1';
					$data[$this->alias]['role'] = $match[$this->alias]['role'];

					if ($reset === true) {
						$data[$this->alias]['passwd'] = $this->generatePassword();
						$data[$this->alias]['password_token'] = null;
					}

					$data[$this->alias]['email_token'] = null;
					$data[$this->alias]['email_token_expires'] = null;
				}
			}
			return $data;
		}

	/**
	 * Updates the last activity field of a user
	 *
	 * @param string $user User ID
	 * @return boolean True on success
	 */
		public function updateLastActivity($userId = null) {
			if (!empty($userId)) {
				$this->id = $userId;
			}
			if ($this->exists()) {
				return $this->saveField('last_activity', date('Y-m-d H:i:s', time()));
			}
			return false;
		}

	/**
	 * Checks if an email is in the system, validated and if the user is active so that the user is allowed to reste his password
	 *
	 * @param array $postData post data from controller
	 * @return mixed False or user data as array on success
	 */
		public function passwordReset($postData = array()) {
			$user = $this->find('first', array(
				'conditions' => array(
					$this->alias . '.active' => 1,
					$this->alias . '.email' => $postData[$this->alias]['email'])));

			if (!empty($user) && $user[$this->alias]['email_authenticated'] == 1) {
				$sixtyMins = time() + 43000;
				$token = $this->generateToken();
				$user[$this->alias]['password_token'] = $token;
				$user[$this->alias]['email_token_expires'] = date('Y-m-d H:i:s', $sixtyMins);
				$user = $this->save($user, false);
				return $user;
			} elseif (!empty($user) && $user[$this->alias]['email_authenticated'] == 0){
				$this->invalidate('email', __('This Email Address exists but was never validated.', true));
			} else {
				$this->invalidate('email', __('This Email Address does not exist in the system.', true));
			}
			return false;
		}

	/**
	 * Checks the token for a password change
	 * 
	 * @param string $token Token
	 * @return mixed False or user data as array
	 */
		public function checkPasswordToken($token = null) {
			$user = $this->find('first', array(
				'contain' => array(),
				'conditions' => array(
					$this->alias . '.active' => 1,
					$this->alias . '.password_token' => $token,
					$this->alias . '.email_token_expires >=' => date('Y-m-d H:i:s'))));
			if (empty($user)) {
				return false;
			}
			return $user;
		}

	/**
	 * Resets the password
	 * 
	 * @param array $postData Post data from controller
	 * @return boolean True on success
	 */
		public function resetPassword($postData = array()) {
			$result = false;
			$tmp = $this->validate;
			$this->validate = array(
				'new_password' => $this->validate['passwd'],
				'confirm_password' => array(
					'required' => array(
						'rule' => array('compareFields', 'new_password', 'confirm_password'), 
						'message' => __('The passwords are not equal.', true))));

			$this->set($postData);
			if ($this->validates()) {
				//App::uses('Core', 'Security');
				//$this->data[$this->alias]['passwd'] = Security::hash($this->data[$this->alias]['new_password'], null, true);
				$this->data[$this->alias]['passwd'] = AuthComponent::password($this->data[$this->alias]['new_password']);
				$this->data[$this->alias]['password_token'] = null;
				$result = $this->save($this->data, false);
			}
			$this->validate = $tmp;
			return $result;
		}

	/**
	 * Changes the password for a user
	 *
	 * @param array $postData Post data from controller
	 * @return boolean True on success
	 */
		public function changePassword($postData = array()) {
			$this->set($postData);
			//$tmp = $this->validate;
			$this->validate = $this->validatePasswordChange;

			if ($this->validates()) {
				//App::uses('Core', 'Security');
				//$this->data[$this->alias]['passwd'] = Security::hash($this->data[$this->alias]['new_password'], null, true);
				$this->data[$this->alias]['passwd'] = AuthComponent::password($this->data[$this->alias]['new_password']);
				$this->save($postData, array(
					'validate' => false,
					'callbacks' => false));
				//$this->validate = $tmp;
				return true;
			}

			//$this->validate = $tmp;
			return false;
		}

	/**
	 * Validation method to check the old password
	 *
	 * @param array $password 
	 * @return boolean True on success
	 */
		public function validateOldPassword($password) {
			if (!isset($this->data[$this->alias]['id']) || empty($this->data[$this->alias]['id'])) {
				if (Configure::read('debug') > 0) {
					throw new OutOfBoundsException(__('$this->data[\'' . $this->alias . '\'][\'id\'] has to be set and not empty', true));
				}
			}

			$passwd = $this->field('passwd', array($this->alias . '.id' => $this->data[$this->alias]['id']));
			//App::uses('Core', 'Security');
			//if ($passwd === Security::hash($password['old_password'], null, true)) {
			if ($passwd === AuthComponent::password($password['old_password'], null, true)) {
				return true;
			}
			return false;
		}

	/**
	 * Validation method to compare two fields
	 *
	 * @param mixed $field1 Array or string, if array the first key is used as fieldname
	 * @param string $field2 Second fieldname
	 * @return boolean True on success
	 */
		public function compareFields($field1, $field2) {
			if (is_array($field1)) {
				$field1 = key($field1);
			}
			if (isset($this->data[$this->alias][$field1]) && isset($this->data[$this->alias][$field2]) && 
				$this->data[$this->alias][$field1] == $this->data[$this->alias][$field2]) {
				return true;
			}
			return false;
		}

	/**
	 * Returns all data about a user
	 *
	 * @param string $slug user slug
	 * @return array
	 */
		public function view($slug = null) {
			$user = $this->find('first', array(
				'contain' => array(
					//'Tag',
					'Detail'),
				'conditions' => array(
					$this->alias . '.slug' => $slug,
					'OR' => array(
						'AND' =>
							array($this->alias . '.active' => 1, $this->alias . '.email_authenticated' => 1),
							//array($this->alias . '.active' => 1, $this->alias . '.account_type' => 'remote')
							))));

			if (empty($user)) {
				throw new Exception(__('The user does not exist.', true));
			}

			return $user;
		}

	/**
	 * Registers a new user
	 *
	 * @param array $postData Post data from controller
	 * @param boolean $useEmailVerification If set to true a token will be generated
	 * @return mixed
	 */
		public function register($postData = array(), $useEmailVerification = true, $generatePassword = true) {
			$postData = $this->_beforeRegistration($postData, $useEmailVerification);
			$this->_removeExpiredRegistrations();

			$this->set($postData);
			if ($this->validates()) {
				//App::uses('Core', 'Security');
				if($generatePassword === false){
					//Happens in beforeSave now
					//$postData[$this->alias]['passwd'] = Security::hash($postData[$this->alias]['passwd'], 'sha1', true);
				}else{
					$postData[$this->alias]['passwd'] = $this->generatePassword();
					//$postData[$this->alias]['passwd'] = Security::hash($postData[$this->alias]['passwd'], 'sha1', true);
				}
				$this->create();
				return $this->save($postData, false);
			}

			return false;
		}

	/**
	 * Registers a new user
	 *
	 * @param array $postData Post data from controller
	 * @param boolean $useEmailVerification If set to true a token will be generated
	 * @return mixed
	 */
		public function setupUser($postData = array(), $useEmailVerification = true, $generatePassword = true) {
			$postData = $this->_beforeRegistration($postData, $useEmailVerification);
			$this->_removeExpiredRegistrations();

			//$this->set($postData);
			//if ($this->validates()) {
				//App::uses('Core', 'Security');
				if($generatePassword === false){
					//Happens in beforeSave now
					//$postData[$this->alias]['passwd'] = Security::hash($postData[$this->alias]['passwd'], 'sha1', true);
				}else{
					$postData[$this->alias]['passwd'] = $this->generatePassword();
					//$postData[$this->alias]['passwd'] = Security::hash($postData[$this->alias]['passwd'], 'sha1', true);
				}
				
				return $postData[$this->alias];
			//}

			// return false;
		}
	/**
	 * Activates the user account
	 * @param id int The user id to activate
	 */
	public function activate($id=null){
		$user = $this->read(null,$id);
		if(!empty($user)){
			if($user['User']['active'] === 0){
				$this->id = $id;
				$this->set('active',1);
				$this->save();
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Resends the verification if the user is not already validated or invalid
	 *
	 * @param array $postData Post data from controller
	 * @return mixed False or user data array on success
	 */
		public function resendVerification($postData = array()) {
			if (!isset($postData[$this->alias]['email']) || empty($postData[$this->alias]['email'])) {
				$this->invalidate('email', __('Please enter your email address.', true));
				return false;
			}

			$user = $this->find('first', array(
				'contain' => array(),
				'conditions' => array(
					$this->alias . '.email' => $postData[$this->alias]['email'])));

			if (empty($user)) {
				$this->invalidate('email', __('The email address does not exist in the system', true));
				return false;
			}

			if ($user[$this->alias]['email_authenticated'] == 1) {
				$this->invalidate('email', __('Your account is already authenticaed.', true));
				return false;
			}

			if ($user[$this->alias]['active'] == 0) {
				$this->invalidate('email', __('Your account is disabled.', true));
				return false;
			}

			$user[$this->alias]['email_token'] = $this->generateToken();
			$user[$this->alias]['email_token_expires'] = date('Y-m-d H:i:s', time() + 86400);

			return $this->save($user, false);
		}

	/**
	 * Generates a password
	 *
	 * @param int $length Password length
	 * @return string
	 */
		public function generatePassword($length = 10) {
			srand((double)microtime() * 1000000);
			$password = '';
			$vowels = array("a", "e", "i", "o", "u");
			$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
								"cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
			for ($i = 0; $i < $length; $i++) {
				$password .= $cons[mt_rand(0, 31)] . $vowels[mt_rand(0, 4)];
			}
			return substr($password, 0, $length);
		}

	/**
	 * Generate token used by the user registration system
	 *
	 * @param int $length Token Length
	 * @return string
	 */
		public function generateToken($length = 10) {
			$possible = '0123456789abcdefghijklmnopqrstuvwxyz';
			$token = "";
			$i = 0;

			while ($i < $length) {
				$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
				if (!stristr($token, $char)) {
					$token .= $char;
					$i++;
				}
			}
			return $token;
		}

	/**
	 * Optional data manipulation before the registration record is saved
	 *
	 * @param array post data array
	 * @param boolean Use email generation, create token, default true
	 * @return array
	 */
		protected function _beforeRegistration($postData = array(), $useEmailVerification = true) {
			if ($useEmailVerification == true) {
				$postData[$this->alias]['email_token'] = $this->generateToken();
				$postData[$this->alias]['email_token_expires'] = date('Y-m-d H:i:s', time() + 86400);
			} else {
				$postData[$this->alias]['email_authenticated'] = 1;
			}
			$postData[$this->alias]['active'] = 0; //By default set the account to inactive
			return $postData;
		}
		
	/**
	 * Removes all users from the user table that are outdated
	 *
	 * Override it as needed for your specific project
	 *
	 * @return void
	 */
		protected function _removeExpiredRegistrations() {
			$this->deleteAll(array(
				$this->alias . '.email_authenticated' => 0,
				$this->alias . '.email_token_expires <' => date('Y-m-d H:i:s')
				)
			);
		}
		
	/**
	 * Adds a new user
	 * 
	 * @param array post data, should be Controller->data
	 * @return array
	 */
		public function add($postData = null) {
			if (!empty($postData)) {
				$this->create();
				if ($this->save($postData)) {
					return true;
				}
			}
		}

	/**
	 * Edits an existing user
	 *
	 * @param string $userId User ID
	 * @param array $postData controller post data usually $this->data
	 * @return mixed True on successfully save else post data as array
	 */
		public function edit($userId = null, $postData = null) {
			$user = $this->find('first', array(
				'contain' => array(
					'Detail'),
				'conditions' => array(
					$this->alias . '.id' => $userId)));

			$this->set($user);
			if (empty($user)) {
				throw new OutOfBoundsException(__('Invalid User', true));
			}

			if (!empty($postData)) {
				$this->set($postData);
				$result = $this->save(null, true);
				if ($result) {
					$this->data = $result;
					return true;
				} else {
					return $postData;
				}
			}
		}
	

}
