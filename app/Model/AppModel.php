<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	/**
	 * Creates a unique slug
	 */
	function createSlug ($string, $id=null) {
		$slug = Inflector::slug ($string,'-');
		$slug = low ($slug);
		$i = 0;
		$params = array ();
		$params ['conditions']= array();
		$params ['conditions'][$this->name.'.slug']= $slug;
		if (!is_null($id)) {
			$params ['conditions']['not'] = array($this->name.'.id'=>$id);
		}
		while (count($this->find ('all',$params))) {
			if (!preg_match ('/-{1}[0-9]+$/', $slug )) {
				$slug .= '-' . ++$i;
			} else {
				$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
			}
			$params ['conditions'][$this->name . '.slug']= $slug;
		}
		return $slug;
	}
	
	/**
	 * Generates a unique token
	 */
	function generateToken($fileName = '',$secret = ''){
		// Settings to generate the URI
		$secret = $this->secret;             // Same as AuthTokenSecret
		//$protectedPath = "/downloads/";        // Same as AuthTokenPrefix
		$ipLimitation = true;                 // Same as AuthTokenLimitByIp
		$hexTime = dechex(time());             // Time in Hexadecimal      
		$fileName = $this->fileName;    // The file to access


		// Let's generate the token depending if we set AuthTokenLimitByIp
		if ($ipLimitation) {
		  $token = md5($secret . $fileName . $hexTime . $_SERVER['REMOTE_ADDR']);
		}else {
		  $token = md5($secret . $fileName. $hexTime);
		}

		// We build the unique token
		return $token;
	}
}
