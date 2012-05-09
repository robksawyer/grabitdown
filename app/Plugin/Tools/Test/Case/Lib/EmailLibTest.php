<?php

App::uses('MyCakeTestCase', 'Tools.Lib');
App::uses('EmailLib', 'Tools.Lib');

//Configure::write('Config.admin_email', '...');

class EmailLibTest extends MyCakeTestCase {

	public $Email;

	public function startTest() {
		$this->Email = new EmailLib();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->Email));
		$this->assertTrue(is_a($this->Email, 'EmailLib'));
;
	}
	
	public function testSendDefault() {
		# start
		$this->Email->to(Configure::read('Config.admin_email'), Configure::read('Config.admin_emailname'));
		$this->Email->subject('Test Subject');
		
		$res = $this->Email->send('xyz xyz');
		# end
		if ($error = $this->Email->getError()) {
			$this->out($error);
		}
		$this->assertEquals('', $this->Email->getError());
		$this->assertTrue($res);
		
		
		$this->Email->resetAndSet();
		# start
		$this->Email->to(Configure::read('Config.admin_email'), Configure::read('Config.admin_emailname'));
		$this->Email->subject('Test Subject 2');
		$this->Email->template('default', 'internal');
		$this->Email->viewVars(array('x'=>'y', 'xx'=>'yy', 'text'=>''));
		$this->Email->addAttachments(array(APP.'webroot'.DS.'img'.DS.'icons'.DS.'edit.gif'));
		
		$res = $this->Email->send('xyz');
		# end
		if ($error = $this->Email->getError()) {
			$this->out($error);
		}
		$this->assertEquals('', $this->Email->getError());
		$this->assertTrue($res);
	}

	public function testSendFast() {
		//$this->Email->resetAndSet();
		//$this->Email->from(Configure::read('Config.admin_email'), Configure::read('Config.admin_emailname'));
		$res = EmailLib::systemEmail('system-mail test', 'some fast email to admin test');
		//debug($res);
		$this->assertTrue($res);
	}
	
	
	public function _testAddAttachment() {
		$file = CakePlugin::path('Tools').'Test'.DS.'test_files'.DS.'img'.DS.'hotel.png';
		$this->assertTrue(file_exists($file));
		Configure::write('debug', 0);
		
		$this->Email->to(Configure::read('Config.admin_email'));
		$this->Email->addAttachment($file);
		$res = $this->Email->send('test_default', 'internal');
		if ($error = $this->Email->getError()) {
			$this->out($error);
		}
		$this->assertEquals('', $this->Email->getError());
		$this->assertTrue($res);
		
		$this->Email->resetAndSet();
		$this->Email->to(Configure::read('Config.admin_email'));
		$this->Email->addAttachment($file, 'x.jpg');
		$res = $this->Email->send('test_custom_filename');
		
		Configure::write('debug', 2);
		$this->assertEquals('', $this->Email->getError());
		$this->assertTrue($res);
	}
	
	/**
	 * html email
	 */
	public function testAddEmbeddedAttachment() {
		$file = CakePlugin::path('Tools').'Test'.DS.'test_files'.DS.'img'.DS.'hotel.png';
		$this->assertTrue(file_exists($file));
		
		Configure::write('debug', 0);
		$this->Email = new EmailLib();
		$this->Email->emailFormat('both');
		$this->Email->to(Configure::read('Config.admin_email'));
		$cid = $this->Email->addEmbeddedAttachment($file);
		
		$this->assertContains('@'.env('HTTP_HOST'), $cid);

			
		$html = '<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="ohyeah" />

	<title>Untitled 6</title>
</head>
<body>
test_embedded_default äöü <img src="cid:'.$cid.'" /> end
html-part
</body>
</html>';
		$text = trim(strip_tags($html));	
		$this->Email->viewVars(compact('text', 'html'));
		
		$res = $this->Email->send();
		Configure::write('debug', 2);
		if ($error = $this->Email->getError()) {
			$this->out($error);
		}
		$this->assertEquals('', $this->Email->getError());
		$this->assertTrue($res);
	}
	
	/**
	 * html email
	 */
	public function testAddEmbeddedBlobAttachment() {
		$file = CakePlugin::path('Tools').'Test'.DS.'test_files'.DS.'img'.DS.'hotel.png';
		$this->assertTrue(file_exists($file));
		
		Configure::write('debug', 0);
		$this->Email = new EmailLib();
		$this->Email->emailFormat('both');
		$this->Email->to(Configure::read('Config.admin_email'));
		$cid = $this->Email->addEmbeddedBlobAttachment(file_get_contents($file), 'my_hotel.png', 'png');
		
		$this->assertContains('@'.env('HTTP_HOST'), $cid);

			
		$html = '<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="ohyeah" />

	<title>Untitled 6</title>
</head>
<body>
test_embedded_blob_default äöü <img src="cid:'.$cid.'" /> end
html-part
</body>
</html>';
		$text = trim(strip_tags($html));	
		$this->Email->viewVars(compact('text', 'html'));
		
		$res = $this->Email->send();
		Configure::write('debug', 2);
		if ($error = $this->Email->getError()) {
			$this->out($error);
		}
		$this->assertEquals('', $this->Email->getError());
		$this->assertTrue($res);
	}
		
	
	
	public function _testComplexeHtmlWithEmbeddedImages() {
		$file = CakePlugin::path('Tools').'Test'.DS.'test_files'.DS.'img'.DS.'hotel.png';
		$this->assertTrue(file_exists($file));
		
		
		
	}


}