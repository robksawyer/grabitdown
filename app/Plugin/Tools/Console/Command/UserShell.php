<?php

if (!defined('CLASS_USER')) {
	define('CLASS_USER', 'User');
}

App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
//App::uses('AuthExtComponent', 'Tools.Controller/Component');

/**
 * create a new user from CLI
 * 
 * @cakephp 2.x
 * @author Mark Scherer
 * @license MIT
 * 2011-11-05 ms
 */
class UserShell extends AppShell {
	
	public $tasks = array();
	public $uses = array(CLASS_USER);


	//TODO: refactor (smaller sub-parts)
	public function main() {
		if (App::import('Component', 'AuthExt') && class_exists('AuthExtComponent')) {
			$this->Auth = new AuthExtComponent(new ComponentCollection());
		} else {
			App::import('Component', 'Auth');
			$this->Auth = new AuthComponent(new ComponentCollection());
		}

		while (empty($username)) {
			$username = $this->in(__('Username (2 characters at least)'));
		}
		while (empty($password)) {
			$password = $this->in(__('Password (2 characters at least)'));
		}

		$schema = $this->User->schema();

		if (isset($this->User->Role) && is_object($this->User->Role)) {
			$roles = $this->User->Role->find('list');

			if (!empty($roles)) {
				$this->out('');
				pr($roles);
			}

			$roleIds = array_keys($roles);
			while (!empty($roles) && empty($role)) {
				$role = $this->in(__('Role'), $roleIds);
			}
		} elseif (method_exists($this->User, 'roles')) {
			$roles = User::roles();

			if (!empty($roles)) {
				$this->out('');
				pr ($roles);
			}

			$roleIds = array_keys($roles);
			while (!empty($roles) && empty($role)) {
				$role = $this->in(__('Role'), $roleIds);
			}
		}
		if (empty($roles)) {
			$this->out('No Role found (either no table, or no data)');
			$role = $this->in(__('Please insert a role manually'));
		}

		$this->out('');
		$pwd = $this->Auth->password($password);

		$data = array('User'=>array(
			'password' => $pwd,
			'active' => 1
		));
		if (!empty($username)) {
			$data['User']['username'] = $username;
		}
		if (!empty($email)) {
			$data['User']['email'] = $email;
		}
		if (!empty($role)) {
			$data['User']['role_id'] = $role;
		}

		if (!empty($schema['status']) && method_exists('User', 'statuses')) {
			$statuses = User::statuses();
			pr($statuses);
			while (empty($status)) {
				$status = $this->in(__('Please insert a status'), array_keys($statuses));
			}
			$data['User']['status'] = $status;
		}

		if (!empty($schema['email'])) {
			$provideEmail = $this->in(__('Provide Email? '),array('y', 'n'), 'n');
			if ($provideEmail === 'y') {
				$email = $this->in(__('Please insert an email'));
				$data['User']['email'] = $email;
			}
			if (!empty($schema['email_confirmed'])) {
				$data['User']['email_confirmed'] = 1;
			}
		}

		$this->out('');
		$continue = $this->in(__('Continue? '), array('y', 'n'), 'n');
		if ($continue != 'y') {
			$this->error('Not Executed!');
		}

		$this->out('');
		$this->hr();
		if ($this->User->save($data)) {
			$this->out('User inserted! ID: '.$this->User->id);
		} else {
			$this->error('User could not be inserted ('.print_r($this->User->validationErrors, true).')');
		}
	}
	
}

