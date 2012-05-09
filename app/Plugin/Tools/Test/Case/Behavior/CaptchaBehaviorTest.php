<?php

App::uses('CaptchaBehavior', 'Tools.Model/Behavior');
App::uses('MyCakeTestCase', 'Tools.Lib');

class CaptchaBehaviorTest extends MyCakeTestCase {

	public $fixtures = array(
		'core.comment'
	);
	
	public $Comment;

	public function startTest() {
		
	}

	public function setUp() {
		$this->Comment = ClassRegistry::init('Comment');
		$this->Comment->Behaviors->attach('Tools.Captcha', array());
	}

	public function tearDown() {
		unset($this->Comment);
	}

	/**
	 * test if nothing has been
	 */
	public function testEmpty() {
		$is = $this->Comment->validates();
		debug($this->Comment->invalidFields());
		$this->assertFalse($is);
	}

	public function testWrong() {
		$data = array('title'=>'xyz', 'captcha'=>'x', 'captcha_hash'=>'y', 'captcha_time'=>'123');
		$this->Comment->set($data);
		$is = $this->Comment->validates();
		debug($this->Comment->invalidFields());
		$this->assertFalse($is);
		
		$data = array('title'=>'xyz', 'captcha'=>'x', 'homepage'=>'', 'captcha_hash'=>'y', 'captcha_time'=>'123');
		$this->Comment->set($data);
		$is = $this->Comment->validates();
		debug($this->Comment->invalidFields());
		$this->assertFalse($is);
	}
	
	public function testCorrect() {
		App::import('Lib', 'Tools.CaptchaLib');
		$Captcha = new CaptchaLib();
		$hash = $Captcha->buildHash(array('captcha'=>2, 'captcha_time'=>time()-10, ''), CaptchaLib::$defaults);
		
		$data = array('title'=>'xyz', 'captcha'=>'2', 'homepage'=>'', 'captcha_hash'=>$hash, 'captcha_time'=>time()-10);
		$this->Comment->set($data);
		$is = $this->Comment->validates();
		debug($this->Comment->invalidFields());
		$this->assertTrue($is);
		
	}

	//TODO

}


