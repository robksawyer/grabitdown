<?php

App::import('Helper', 'Tools.TextExt');
App::uses('MyCakeTestCase', 'Tools.Lib');

class TextExtHelperTest extends MyCakeTestCase {

	public $Text;
	
	public function setUp() {
		$this->Text = new TextExtHelper(new View(null));
	}
	
	
	public function testObject() {
		$this->assertTrue(is_a($this->Text, 'TextExtHelper'));
	}
	
	
	public function testAutoLinkEmails() {
		$text = 'Text with a url euro@euro.de and more';
		$expected = 'Text with a url <a href="mailto:euro@euro.de">euro@euro.de</a> and more';
		$result = $this->Text->autoLinkEmails($text, array());
		$this->assertEquals($result, $expected);	
		
		$text = 'Text with a url euro@euro.de and more';
		$expected = 'Text with a url <script language=javascript><!--
	document.write(\'<a\'+ \' hre\'+ \'f="ma\'+ \'ilto:\'+ \'eu\'+ \'ro@\'+ \'euro\'+ \'.d\'+ \'e"\'+ \' t\'+ \'itle\'+ \'="\'+ \'F�r \'+ \'den\'+ \' G\'+ \'ebra\'+ \'uch\'+ \' eines\'+ \' exte\'+ \'rn\'+ \'en E-\'+ \'Mail-P\'+ \'rogra\'+ \'mms"\'+ \' cl\'+ \'ass="e\'+ \'mail"\'+ \'>\');
	//--></script>
		e&#117;&#x72;o<span>@</span>e&#x75;&#x72;&#111;&#x2e;&#x64;&#x65;

	<script language=javascript><!--
	document.write(\'</a>\');
	//--></script> and more\'';
		$result = $this->Text->autoLinkEmails($text, array('obfuscate'=>true));
		pr($text);
		echo $result;
		pr(h($result));
		$this->assertNotEqual($result, $text);	
	
	}

	public function testAutoLinkEmailsWithHtmlOrDangerousStrings() {
		$text = 'Text <i>with a email</i> euro@euro.de and more';
		$expected = 'Text &lt;i&gt;with a email&lt;/i&gt; <a href="mailto:euro@euro.de">euro@euro.de</a> and more';
		$result = $this->Text->autoLinkEmails($text);
		//pr(h($text));
		$this->assertEquals($result, $expected);
	}	
	
		
	public function testStripProtocol() {
		$urls = array(
			'http://www.cakephp.org/bla/bla' => 'www.cakephp.org/bla/bla',
			'www.cakephp.org' => 'www.cakephp.org'
		);
		
		foreach ($urls as $url => $expected) {
			$is = $this->Text->stripProtocol($url);
			$this->assertEquals($is, $expected);
		}
	}
	
	public function testAutoLinkUrls() {
		$texts = array(
			'text http://www.cakephp.org/bla/bla some more text' => '',
			'This is a test text with URL http://www.cakephp.org\tand some more text' => 'This is a test text with URL http://www.cakephp.org\tand some more text'
		);
		
		foreach ($texts as $text => $expected) {
			//$is = $this->Text->stripProtocol($url);
			//$this->assertEquals($is, $expected);
		}
		
		
		$text = 'Text with a url www.cot.ag/cuIb2Q/eruierieriu-erjekr and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q/eruierieriu-erjekr">www.cot.ag/c...</a> and more';
		$result = $this->Text->autoLinkUrls($text, array('maxLength'=>12));
		$this->assertEquals($result, $expected);
		
		$text = 'Text with a url http://www.cot.ag/cuIb2Q/eru and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q/eru">www.cot.ag/cuIb2Q/eru</a> and more';
		$result = $this->Text->autoLinkUrls($text, array('stripProtocol'=>true));
		$this->assertEquals($result, $expected);
		
		$text = 'Text with a url http://www.cot.ag/cuIb2Q/eruierieriu-erjekr and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q/eruierieriu-erjekr">http://www.cot.ag/cuIb2Q/eruierieriu-erjekr</a> and more';
		$result = $this->Text->autoLinkUrls($text, array('stripProtocol'=>false, 'maxLength'=>0));
		$this->assertEquals($result, $expected);
	
		
		$text = 'Text with a url www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-werwer-werwerwe-werwerwer-werwerdfrffsd-werwer and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-werwer-werwerwe-werwerwer-werwerdfrffsd-werwer">www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-w...</a> and more';
		$result = $this->Text->autoLinkUrls($text);
		$this->assertEquals($result, $expected);
			
	}
	
	public function testAutoLinkUrlsWithEscapeFalse() {
		$text = 'Text with a url www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-werwer and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-werwer">www.cot.ag/cuIb2Q/er...</a> and more';
		$result = $this->Text->autoLinkUrls($text, array('maxLength'=>20), array('escape'=>false));
		$this->assertEquals($result, $expected);
		
		# not yet working
		/*
		$text = 'Text with a url www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-werwer and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q/eruierieriu-erjekrwerweuwrweir-werwer">www.cot.ag/cuIb2Q/er&hellip;</a> and more';
		$result = $this->Text->autoLinkUrls($text, array('maxLength'=>20), array('escape'=>false, 'html'=>true));
		$this->assertEquals($result, $expected);
		*/
		
		$text = '<h3>google<h3> a http://maps.google.de/maps?f=d&source=s_d&saddr=m%C3%BCnchen&daddr=Berlin&hl=de&geocode=FXaL3gIdGrOwACnZX4yj-XWeRzF9mLF9SrgMAQ%3BFY1xIQMdSKTMACkBWQM_N06oRzFwO15bRiAhBA&mra=ls&sll=52.532932,13.41156&sspn=0.77021,2.348328&g=berlin&ie=UTF8&t=h&z=6 link';
		$expected = '&lt;h3&gt;google&lt;h3&gt; a <a href="http://maps.google.de/maps?f=d&amp;source=s_d&amp;saddr=m%C3%BCnchen&amp;daddr=Berlin&amp;hl=de&amp;geocode=FXaL3gIdGrOwACnZX4yj-XWeRzF9mLF9SrgMAQ%3BFY1xIQMdSKTMACkBWQM_N06oRzFwO15bRiAhBA&amp;mra=ls&amp;sll=52.532932,13.41156&amp;sspn=0.77021,2.348328&amp;g=berlin&amp;ie=UTF8&amp;t=h&amp;z=6">maps.google.de/maps?f=d&amp;source...</a> link';
		$result = $this->Text->autoLinkUrls($text, array('maxLength'=>30));
		$this->assertEquals($result, $expected);
		
	}
	
	public function testAutoLinkUrlsWithHtmlOrDangerousStrings() {
		$text = 'Text <i>with a url</i> www.cot.ag?id=2&sub=3 and more';
		$expected = 'Text &lt;i&gt;with a url&lt;/i&gt; <a href="http://www.cot.ag?id=2&amp;sub=3">www.cot.ag?id=2&amp;sub=3</a> and more';
		$result = $this->Text->autoLinkUrls($text);
		//pr(h($text));
		$this->assertEquals($result, $expected);
	}
	
	/**
	 * combined (emails + urls)
	 * 2011-04-03 ms
	 */
	public function testAutoLink() {
		$text = 'Text <i>with a url</i> www.cot.ag?id=2&sub=3 and some email@domain.com more';
		$expected = 'Text &lt;i&gt;with a url&lt;/i&gt; <a href="http://www.cot.ag?id=2&amp;sub=3">www.cot.ag?id=2&amp;sub=3</a> and some <a href="mailto:email@domain.com">email@domain.com</a> more';
		$result = $this->Text->autoLink($text);
		pr(h($text));
		$this->assertEquals($result, $expected);
	}
	
/* from cake */

	/**
	 * test invalid email addresses.
	 *
	 * @return void
	 */
	public function testAutoLinkEmailInvalid() {
		$result = $this->Text->autoLinkEmails('this is a myaddress@gmx-de test');
		$expected = 'this is a myaddress@gmx-de test';
		$this->assertEquals($expected, $result);
		
		$result = $this->Text->autoLink('this is a myaddress@gmx-de test');
		$expected = 'this is a myaddress@gmx-de test';
		$this->assertEquals($expected, $result);
	}


	
	public function testAutoLinkUrlsWithCakeTests() {
		$text = 'This is a test text';
		$expected = 'This is a test text';
		$result = $this->Text->autoLinkUrls($text);
		$this->assertEquals($result, $expected);

		$text = 'This is a test that includes (www.cakephp.org)';
		$expected = 'This is a test that includes (<a href="http://www.cakephp.org">www.cakephp.org</a>)';
		$result = $this->Text->autoLinkUrls($text);
		$this->assertEquals($result, $expected);

		$text = 'Text with a partial www.cakephp.org URL';
		$expected = 'Text with a partial <a href="http://www.cakephp.org"\s*>www.cakephp.org</a> URL';
		$result = $this->Text->autoLinkUrls($text);
		$this->assertRegExp('#^' . $expected . '$#', $result);

		$text = 'Text with a partial www.cakephp.org URL';
		$expected = 'Text with a partial <a href="http://www.cakephp.org" \s*class="link">www.cakephp.org</a> URL';
		$result = $this->Text->autoLinkUrls($text, array(), array('class' => 'link'));
		$this->assertRegExp('#^' . $expected . '$#', $result);

		$text = 'Text with a partial WWW.cakephp.org URL';
		$expected = 'Text with a partial <a href="http://WWW.cakephp.org"\s*>WWW.cakephp.org</a> URL';
		$result = $this->Text->autoLinkUrls($text);
		$this->assertRegExp('#^' . $expected . '$#', $result);

		$text = 'Text with a partial WWW.cakephp.org &copy; URL';
		$expected = 'Text with a partial <a href="http://WWW.cakephp.org"\s*>WWW.cakephp.org</a> &copy; URL';
		$result = $this->Text->autoLinkUrls($text, array('escape' => false), array('escape' => false));
		$this->assertRegExp('#^' . $expected . '$#', $result);

		$text = 'Text with a url www.cot.ag/cuIb2Q and more';
		$expected = 'Text with a url <a href="http://www.cot.ag/cuIb2Q">www.cot.ag/cuIb2Q</a> and more';
		$result = $this->Text->autoLinkUrls($text);
		$this->assertEquals($result, $expected);
	}
	
}