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
	
	public $tenCodes = 5.00;
	public $oneHundredCodes = 25.00;
	public $oneThousandCodes = 50.00;
	public $tenThousandCodes = 125.00;
	public $oneHundredThousandCodes = 250.00;
	
/**
 * Generates a set of codes
 * 
 * @param array post data, should be Controller->data
 * @param int codeCount Total codes to generate
 * @return array
 */
	public function generateCodes($postData = null, $codeCount = 0) {
		if ($codeCount > 0) {
			for($i=0;$i<$codeCount;$i++){
				$postData[$this->alias][$i] = array();
				//$postData[$this->alias][$i]['upload_id'] = $postData['Upload']['id'];
				$postData[$this->alias][$i]['token'] = $this->generateToken(25);
				$postData[$this->alias][$i]['active'] = 1;
			}
			/*unset($postData['Upload']);
			if($this->saveAll($postData,array('validate'=>'none'))){
				return true;
			}*/
			return $postData[$this->alias];
		}
			
		return false;
		
		/*$this->create();
		if ($this->saveAll($postData)) {
			return true;
		}*/
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
	 * Returns the code price
	 *	@param int codeCount code count 
	 */
	public function getPrice($codeCount=0){
		//Code prices 
		$totalPrice = 0;
		switch($codeCount){
			case 0:
				$totalPrice = 0;
				break;
			case 10:
				$totalPrice = $this->tenCodes;
				break;
			case 100:
				$totalPrice = $this->oneHundredCodes;
				break;
			case 1000:
				$totalPrice = $this->oneThousandCodes;
				break;
			case 10000:
				$totalPrice = $this->tenThousandCodes;
				break;
			case 100000:
				$totalPrice = $this->oneHundredThousandCodes;
				break;
		}
		return $totalPrice;
	}
	
	public function getPaymentOptions(){
		$payment_options = array(
								'10'=>'10 $'.$this->tenCodes.' USD',
								'100'=>'100 $'.$this->oneHundredCodes.' USD',
								'1000'=>'1,000 $'.$this->oneThousandCodes.' USD',
								'10000'=>'10,000 $'.$this->tenThousandCodes.' USD',
								'100000'=>'100,000 $'.$this->oneHundredThousandCodes.' USD'
								);
		return $payment_options;
	}
	
	/**
	 * @param totalCodes The total number of codes the user is purchasing
	 */
	public function getItemName($totalCodes){
		// called as CakeNumber
		App::uses('CakeNumber', 'Utility');
		$itemName = CakeNumber::format($totalCodes, array(
		    'places' => 0,
		    'before' => '',
		    'escape' => false,
		    'thousands' => ','
		));
		
		return $itemName ." download codes";
	}
}
