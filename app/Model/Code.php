<?php
App::uses('AppModel', 'Model');
/**
 * Code Model
 *
 * @property Upload $Upload
 */
class Code extends AppModel {

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'upload_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
/**
 * Generates a set of codes
 * 
 * @param array post data, should be Controller->data
 * @param int codeCount Total codes to generate
 * @return array
 */
	public function generateCodes($postData = null, $codeCount = 0) {
		if (!empty($codeCount)) {
			for($i=0;$i<$codeCount;$i++){
				$postData[$this->alias][$i] = array();
				//$postData['Code'][$i]['upload_id'] = $postData['Upload']['id'];
				$postData[$this->alias][$i]['token'] = $this->generateToken(25);
				$postData[$this->alias][$i]['active'] = 1;
			}
			/*if($this->saveAll($postData)){
				
			}else{
				return false;
			}*/
		}
		
		/*$this->create();
		if ($this->saveAll($postData)) {
			return true;
		}*/
		return $postData[$this->alias];
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
}
