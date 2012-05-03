<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	public $theme = 'V1';
	public $components = array('Auth' => array(
											'loginRedirect' => array('controller' => 'uploads', 'action' => 'admin'),
											'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home')
											),'Session', 'Email', 'Cookie');
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Js' => array('Jquery'));
	
	public function beforeFilter() {
		$this->Auth->allow('index', 'view');
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
	* @param array $message The error message
	* @param array $subject The message subject line
	* @param array $layout You can customize the layout if needed
	* @return void
	*/
	protected function _sendErrorEmail($message = '',$subject = 'There was an error in the app',$layout='default') {
		if (!empty($this->request->data)) {
			//The administrator email address
			$admin = 'robksawyer@gmail.com';
			if (!empty($admin)) {
				$options = array(
									'layout'=>$layout,
									'subject'=>$subject,
									'view'=>'default'
									);
				$viewVars = array('content'=>$message);
	
				//Send the admin an email
				$this->_sendEmail($admin,$options,$viewVars);
				$this->log('Error email sent to '.$admin,'error_email_log');
			} else {
				// The email didn't send because the email wasn't valid
			}
		}
	}
	
}
