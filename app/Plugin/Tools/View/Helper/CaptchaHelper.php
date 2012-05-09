<?php

if (!defined('BR')) {
	define('BR', '<br />');
}

/**
 * PHP5 / CakePHP2
 *
 * @author        Mark Scherer
 * @link          http://www.dereuromark.de
 * @package       tools plugin
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * works togehter with captcha behaviour/component
 * 2011-06-11 ms
 */
class CaptchaHelper extends AppHelper {
	
	public $helpers = array('Form');

	protected $defaults = array(
		'difficulty' => 1, # initial diff. level (@see operator: + = 0, +- = 1, +-* = 2)
		'raiseDifficulty' => 2, # number of failed trails, after the x. one the following one it will be more difficult
	);
	protected $settings = null;

	protected $numberConvert = null;
	protected $operatorConvert = null;

	public function __construct($View = null, $settings = array()) {
		parent::__construct($View, $settings);
		
		# First of all we are going to set up an array with the text equivalents of all the numbers we will be using.
		$this->numberConvert = array(0=>'zero', 1=>'one', 2=>'two', 3=>'three', 4=>'four', 5=>'five', 6=>'six', 7=>'seven', 8=>'eight', 9=>'nine', 10=>'ten');

		# Set up an array with the operators that we want to use. With difficulty=1 it is only subtraction and addition.
		$this->operatorConvert = array(0=>array('+',__('calcPlus')), 1=>array('-',__('calcMinus')), 2=>'*',__('calcTimes'));
		
		App::uses('CaptchaLib', 'Tools.Lib');
		$this->settings = array_merge(CaptchaLib::$defaults, $this->defaults);
		$settings = (array)Configure::read('Captcha');
		if (!empty($settings)) {
			$this->settings = array_merge($this->settings, $settings);
		}
	}


	/**
	 * //TODO: move to Lib
	 * shows the statusText of Relations
	 * @param int $difficulty: not build in yet
	 * 2008-12-12 ms
	 */
	protected function _generate($difficulty = null) {
		# Choose the first number randomly between 6 and 10. This is to stop the answer being negative.
		$numberOne = mt_rand(6, 10);
		# Choose the second number randomly between 0 and 5.
		$numberTwo = mt_rand(0, 5);
		# Choose the operator randomly from the array.
		$captchaOperatorSelection = $this->operatorConvert[mt_rand(0, 1)];
		$captchaOperator = $captchaOperatorSelection[mt_rand(0, 1)];

		# Get the equation in textual form to show to the user.
		$code = (mt_rand(0, 1) == 1 ? __($this->numberConvert[$numberOne]) : $numberOne) . ' ' . $captchaOperator . ' ' . (mt_rand(0, 1) == 1 ? __($this->numberConvert[$numberTwo]) : $numberTwo);

		# Evaluate the equation and get the result.
		eval('$result = ' . $numberOne . ' ' . $captchaOperatorSelection[0] . ' ' . $numberTwo . ';');

		return array('code'=>$code, 'result'=>$result);
	}

	/**
	 * main captcha output (usually called from $this->input() automatically)
	 * - hash-based
	 * - session-based (not impl.)
	 * - db-based (not impl.)
	 * 2009-12-18 ms
	 */
	public function captcha($modelName = null) {
		$captchaCode = $this->_generate();

		# Session-Way (only one form at a time) - must be a component then
	//$this->Session->write('Captcha.result', $result);

	# DB-Way (several forms possible, high security via IP-Based max limits)
	// the following should be done in a component and passed to the view/helper
	// $Captcha = ClassRegistry::init('Captcha');
	// $this->Captcha->new(); $this->Captcha->update(); etc

		# Timestamp-SessionID-Hash-Way (several forms possible, not as secure)
		$hash = $this->_buildHash($captchaCode);

		$return = '';

		if (in_array($this->settings['type'], array('active', 'both'))) {
			# //todo obscure html here?
			$fill = ''; //'<span></span>';
			$return .= '<span id="captchaCode">'.$fill.''.$captchaCode['code'].'</span>';
		}

		$field = $this->_fieldName($modelName);

		# add passive part on active forms as well
		$return .= '<div style="display:none">'.
			$this->Form->input($field.'_hash', array('value'=>$hash)).
			$this->Form->input($field.'_time', array('value'=>time())).
			$this->Form->input((!empty($modelName)?$modelName.'.':'').$this->settings['dummyField'], array('value'=>'')).
		'</div>';
		return $return;
	}


	/**
	 * active math captcha
	 * either combined with between=true (all in this one funtion)
	 * or seperated by =false (needs input(false) and captcha() calls then)
	 * @param bool between: [default: true]
	 * 2010-01-08 ms
	 */
	public function input($modelName = null, $options = array()) {
		$defaultOptions = array(
			'type'=>'text',
			'class'=>'captcha',
			'value'=>'',
			'maxlength'=>3,
			'label'=>__('Captcha').BR.__('captchaExplained'),
			'combined'=>true, 
			'autocomplete'=>'off',
			'after' => __('captchaTip'),
		);
		$options = array_merge($defaultOptions, $options);

		if ($options['combined'] === true) {
			$options['between'] = $this->captcha($modelName);
			if (in_array($this->settings['type'], array('active', 'both'))) {
				$options['between'] .= ' = ';
			}
		}
		unset($options['combined']);

		$field = $this->_fieldName($modelName);
		return $this->Form->input($field.'', $options); // TODO: rename: _code
	}

	/**
	 * passive captcha
	 * 2010-01-08 ms
	 */
	public function passive($modelName = null, $options = array()) {
		$tmp = $this->settings['type'];
		$this->settings['type'] = 'passive';
		$res = $this->captcha($modelName);
		$this->settings['type'] = $tmp;
		return $res;
	}

	/**
	 * active captcha
	 * (+ passive captcha right now)
	 * 2010-01-08 ms
	 */
	public function active($modelName = null, $options = array()) {
		return $this->input($modelName, $options);
	}

	/**
	 * @param array $captchaCode
	 */
	protected function _buildHash($data) {
		return CaptchaLib::buildHash($data, $this->settings, true);
	}


	protected function _fieldName($modelName = null) {
		$fieldName = 'captcha';
		if (isSet($modelName)) {
			$fieldName = $modelName.'.'.$fieldName;
		}
		return $fieldName;
	}
	



}