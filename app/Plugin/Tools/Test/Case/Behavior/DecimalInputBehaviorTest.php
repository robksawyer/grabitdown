<?php

App::import('Behavior', 'Tools.DecimalInput');
App::import('Model', 'App');
App::uses('MyCakeTestCase', 'Tools.Lib');


class DecimalInputBehaviorTest extends MyCakeTestCase {

	public function startTest() {
		//$this->Comment = ClassRegistry::init('Comment');
		$this->Comment = new TestModel();
		$this->Comment->Behaviors->attach('Tools.DecimalInput', array('fields'=>array('rel_rate', 'set_rate'), 'output'=>true));
	}

	public function setUp() {

	}

	public function tearDown() {

	}

	public function testObject() {
		$this->assertTrue(is_a($this->Comment->Behaviors->DecimalInput, 'DecimalInputBehavior'));
	}


	public function testBasic() {
		echo $this->_header(__FUNCTION__);
		// accuracy >= 5
		$data = array(
			'name' => 'some Name',
			'set_rate' => '0,1',
			'rel_rate' => '-0,02',
		);
		$this->Comment->set($data);
		$res = $this->Comment->validates();
		$this->assertTrue($res);

		$res = $this->Comment->data;
		echo returns($res);
		$this->assertSame($res['TestModel']['set_rate'], 0.1);
		$this->assertSame($res['TestModel']['rel_rate'], -0.02);
	}

	public function testValidates() {
		echo $this->_header(__FUNCTION__);
		// accuracy >= 5
		$data = array(
			'name' => 'some Name',
			'set_rate' => '0,1',
			'rel_rate' => '-0,02',
		);
		$this->Comment->set($data);
		$res = $this->Comment->validates();
		$this->assertTrue($res);

		$res = $this->Comment->data;
		echo returns($res);
		$this->assertSame($res['TestModel']['set_rate'], 0.1);
		$this->assertSame($res['TestModel']['rel_rate'], -0.02);
	}

	public function testFind() {
		echo $this->_header(__FUNCTION__);
		$res = $this->Comment->find('all', array());
		$this->assertTrue(!empty($res));
		echo returns($res);
		$this->assertSame($res[0]['TestModel']['set_rate'], '0,1');
		$this->assertSame($res[0]['TestModel']['rel_rate'], '-0,02');

		echo BR.BR;

		$res = $this->Comment->find('first', array());
		$this->assertTrue(!empty($res));
		echo returns($res);
		$this->assertSame($res['TestModel']['set_rate'], '0,1');
		$this->assertSame($res['TestModel']['rel_rate'], '-0,02');

		$res = $this->Comment->find('count', array());
		echo returns($res);
		$this->assertSame($res[0][0]['count'], 2);

	}

}

/** other files **/

class TestModel extends AppModel {


	public $alias = 'TestModel';
	public $useTable = false;
	public $displayField = 'title';

	public function find($type = null, $options = array(), $customData = null) {
		$data = array(
			'name' => 'some Name',
			'set_rate' => 0.1,
			'rel_rate' => -0.02,
		);
		if ($customData !== null) {
			$data = $customData;
		}
		if ($type == 'count') {
			$results = array(0=>array(0=>array('count'=>2)));
		} else {
			$results = array(0=>array($this->alias=>$data));
		}

		$results = $this->_filterResults($results);
		if ($type == 'first') {
			$results = $this->_findFirst('after', null, $results);
		}
		return $results;
	}
}
