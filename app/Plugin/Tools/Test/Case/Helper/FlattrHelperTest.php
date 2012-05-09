<?php

App::import('Helper', 'Tools.Flattr');
App::import('Helper', 'Html');
App::uses('MyCakeTestCase', 'Tools.Lib');
App::uses('View', 'View');

class FlattrHelperTest extends MyCakeTestCase {
	
	public $uid;
	
	public function startTest() {
		$this->Flattr = new FlattrHelper(new View(null));
		$this->Flattr->Html = new HtmlHelper(new View(null));
		
		$this->uid = '1234';
	}
	
	public function tearDown() {
		
	}
	
	public function testObject() {
		$this->assertTrue(is_a($this->Flattr, 'FlattrHelper'));
	}
		
	public function testBadge() {
		$res = $this->Flattr->badge($this->uid, array());
		echo $res;
		$this->assertTrue(!empty($res));
	}
	
	public function testBadgeWithOptions() {
		$options = array('dsc'=>'Eine Beschreibung', 'lng'=>'de_DE', 'tags'=>array('Spende', 'Geld', 'Hilfe'));
		
		$res = $this->Flattr->badge($this->uid, $options);
		echo $res;
		$this->assertTrue(!empty($res));
	}
}