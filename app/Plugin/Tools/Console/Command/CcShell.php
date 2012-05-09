<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

if (!defined('LF')) {
	define('LF', PHP_EOL); # use PHP to detect default linebreak
}

/**
 * Code Completion Shell
 * Workes perfectly with PHPDesigner - but should also work with most other IDEs out of the box
 * 
 * @version 1.1
 * @cakephp 2.0
 * @author Mark Scherer
 * @license MIT
 * 2011-11-24 ms
 */
class CcShell extends AppShell {
	public $uses = array();

	protected $plugins = null;
	protected $content = '';

	public function main() {
		$this->out('Code Completion Dump - customized for PHPDesigner');

		$this->filename = APP.'CodeCompletion.php';

		# get classes
		$this->models();
		$this->behaviors();

		$this->controller();
		$this->helpers();

		# write to file
		$this->_dump();

		$this->out('...done');
	}



	public function models() {
		$files = $this->_getFiles('Model');

		$content = LF;
		$content .= '/*** model start ***/'.LF;
		$content .= 'class AppModel extends Model {'.LF;
		if (!empty($files)) {
			$content .= $this->_prepModels($files);
		}

		$content .= '}'.LF;
		$content .= '/*** model end ***/'.LF;

		$this->content .= $content;
	}

	public function behaviors() {
		$files = $this->_getFiles('Model/Behavior');

		$content = LF;
		$content .= '/*** behavior start ***/'.LF;
		$content .= 'class AppModel extends Model {'.LF;
		if (!empty($files)) {
			$content .= $this->_prepBehaviors($files);
		}
		$content .= '}'.LF;
		$content .= '/*** behavior end ***/'.LF;


		$content .= '/*** model start ***/'.LF;

		$this->content .= $content;
	}

	/**
	 * components + models
	 */
	public function controller() {
		$content = LF;
		$content .= '/*** component start ***/'.LF;
		$content .= 'class AppController extends Controller {'.LF;
		
		$files = $this->_getFiles('Controller/Component');
		if (!empty($files)) {
			$content .= $this->_prepComponents($files);
		}
		
		$content .= LF.LF;
		
		$files = $this->_getFiles('Model');
		if (!empty($files)) {
			$content .= $this->_prepModels($files);
		}
		
		$content .= '}'.LF;
		$content .= '/*** component end ***/'.LF;

		$this->content .= $content;
	}

	public function helpers() {
		$files = $this->_getFiles('View/Helper');
		$content = LF;
		$content .= '/*** helper start ***/'.LF;
		$content .= 'class AppHelper extends Helper {'.LF;
		if (!empty($files)) {
			$content .= $this->_prepHelpers($files);
		}
		$content .= '}'.LF;
		$content .= '/*** helper end ***/'.LF;


		$this->content .= $content;
	}

	protected function _prepModels($files) {
		$res = '';
		foreach ($files as $name) {


			$res .= '
	/**
	* '.$name.'
	*
	* @var '.$name.'
	*/
	public $'.$name.';
'.LF;
		}

		$res .= '	public function __construct() {';

		foreach ($files as $name) {
			$res .= '
		$this->'.$name.' = new '.$name.'();';
		}

		$res .= LF.'	}'.LF;
		return $res;
	}

	protected function _prepBehaviors($files) {
		$res = '';
		foreach ($files as $name) {
			if (!($varName = $this->_varName($name, 'Behavior'))) {
				continue;
			}
			$res .= '
	/**
	* '.$name.'Behavior
	*
	* @var '.$varName.'
	*/
	public $'.$varName.';
'.LF;
		}

		$res .= '	public function __construct() {';

		foreach ($files as $name) {
			if (!($varName = $this->_varName($name, 'Behavior'))) {
				continue;
			}
			$res .= '
		$this->'.$varName.' = new '.$name.'();';
		}

		$res .= LF.'	}'.LF;
		return $res;
	}

	/**
	 * check on correctness to avoid duplicates
	 */
	protected function _varName($name, $type) {
		if (($pos = strrpos($name, $type)) === false) {
			return '';
			//return $name;
		}
		return substr($name, 0, $pos);
	}


	protected function _prepComponents($files) {
		$res = '';
		foreach ($files as $name) {
			if (!($varName = $this->_varName($name, 'Component'))) {
				continue;
			}
			$res .= '
	/**
	* '.$name.'
	*
	* @var '.$varName.'
	*/
	public $'.$varName.';
'.LF;
		}

		$res .= '	public function __construct() {';

		foreach ($files as $name) {
			if (!($varName = $this->_varName($name, 'Component'))) {
				continue;
			}
			$res .= '
		$this->'.$varName.' = new '.$name.'();';
		}

		$res .= LF.'	}'.LF;
		return $res;
	}

	protected function _prepHelpers($files) {
		# new ones
		$res = '';

		foreach ($files as $name) {
			if (!($varName = $this->_varName($name, 'Helper'))) {
				continue;
			}
			$res .= '
	/**
	* '.$name.'
	*
	* @var '.$varName.'
	*/
	public $'.$varName.';
'.LF;
		}

		$res .= '	public function __construct() {';

		foreach ($files as $name) {
			if (!($varName = $this->_varName($name, 'Helper'))) {
				continue;
			}
			$res .= '
		$this->'.$varName.' = new '.$name.'();';
		}

		$res .= LF.'	}'.LF;

		return $res;
	}


	protected function _dump() {
		//$File = new File($this->filename, true);

		$content = '<?php exit();'.PHP_EOL.PHP_EOL;
		$content .= 'class CodeCompletion {'.PHP_EOL;
		$content .= '}'.PHP_EOL.PHP_EOL;
		$content .= '//Printed: '.date('d.m.Y, H:i:s').PHP_EOL;
		$content .= $this->content;
		
		//return $File->write($content);
		file_put_contents($this->filename, $content);
	}
	

	protected function _getFiles($type) {
		$files = App::objects($type, null, false);
		$corePath = App::core($type);
		$coreFiles = App::objects($type, $corePath, false);
		$files = am($coreFiles, $files);
		//$paths = (array)App::path($type.'s');
		//$libFiles = App::objects($type, $paths[0] . 'lib' . DS, false);
	
		if (!isset($this->plugins)) {
			$this->plugins = App::objects('plugin');
		}
	
		if (!empty($this->plugins)) {
			foreach ($this->plugins as $plugin) {
				$pluginType = $plugin.'.'.$type;
					$pluginFiles = App::objects($pluginType, null, false);
					if (!empty($pluginFiles)) {
						foreach ($pluginFiles as $t) {
							$files[] = $t;
						}
					}
			}
		}
		$files = array_unique($files);
		sort($files);
			$appIndex = array_search('App', $files);
			if ($appIndex !== false) {
				unset($files[$appIndex]);
			}
	
			# no test/tmp files etc (helper.test.php or helper.OLD.php)
		foreach ($files as $key => $file) {
				if (strpos($file, '.') !== false || !preg_match('/^[\da-zA-Z_]+$/', $file)) {
					unset($files[$key]);
				}
			}
		return $files;
	}

}


