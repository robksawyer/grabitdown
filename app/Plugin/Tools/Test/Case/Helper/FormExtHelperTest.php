<?php

App::import('Helper', 'Tools.FormExt');
App::uses('MyCakeTestCase', 'Tools.Lib');
App::uses('View', 'View');

class FormExtHelperTest extends MyCakeTestCase {
	
	public function setUp() {
		$this->FormExt = new FormExtHelper(new View(null));
	}
	
	public function tearDown() {
		
	}
	
	public function testObject() {
		$this->assertTrue(is_a($this->FormExt, 'FormExtHelper'));
	}
		
	public function testX() {
		//TODO
	}
	
}