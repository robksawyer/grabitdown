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
	public function generateCodes($upload_id = null, $codeCount = 0) {
		if ($codeCount > 0) {
			//Check to make sure that the upload doesn't already have the total amount codes allowed for it.
			$upload = $this->Upload->read(null,$upload_id);
			if(count($upload['Code']) < 1){
				//No codes exist
				for($i=0;$i<$codeCount;$i++){
					$postData[$i][$this->alias]['upload_id'] = $upload_id;
					$postData[$i][$this->alias]['token'] = $this->generateToken(6);
					$postData[$i][$this->alias]['active'] = 1;
				}
				if($this->saveAll($postData)){
					return true;
				}
			}else{
				//There are some codes already added for this upload
				$totalCodesAdded = count($upload['Code']);
				$diff = abs($upload['Upload']['total_codes'] - $totalCodesAdded);
			}
		}
			
		return false;
	}
		
/**
 * Generate token used by the user registration system
 *
 * @param int $length Token length
 * @return string the random token
 */
	public function generateToken($length = 5) {
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
