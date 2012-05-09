<?php
/**
 * Copyright 2011, PJ Hile (http://www.pjhile.com) 
 * 
 * Licensed under The MIT License 
 * Redistributions of files must retain the above copyright notice. 
 * 
 * @version    0.1 
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License 
 */

/**
 * A behavior that will json_encode (and json_decode) fields if they contain an array or specific pattern. 
 * 
 * Requres: PHP 5 >= 5.2.0 or PECL json >= 1.2.0 
 * 
 * This is a port of the Serializeable behavior by Matsimitsu (http://www.matsimitsu.nl) 
 */
 
/** 
 * Modified by Mark Scherer (http://www.dereuromark.de)
 * 
 * Now supports different input/output formats:
 * - "list" is useful as some kind of pseudo enums or simple lists
 * - "params" is useful for multiple key/value pairs
 * - can be used to create dynamic forms (and tables)
 * 
 * Also automatically cleans lists and works with custom separators etc
 * 
 * 2011-07-04 ms
 */
class JsonableBehavior extends ModelBehavior {

	public $decoded = null;
	public $settings = array();

	/**
	 * //TODO: json input/ouput directly, clean
	 * @access protected
	 */
	public $_defaultSettings = array(
		'fields' => array(), # empty => only works with array!!!
		'input' => 'array', # json, array, param, list (param/list only works with specific fields)
		'output' => 'array', # json, array, param, list (param/list only works with specific fields)
		'separator' => '|', # only for param or list
		'keyValueSeparator' => ':', # only for param
		'leftBound' =>  '{', # only for list
		'rightBound' => '}', # only for list
		'clean' => true, # only for param or list (autoclean values on insert)
		'sort' => false, # only for list
		'unique' => true, # only for list (autoclean values on insert),
		'map' => array(), # map on a different DB field
	);	

	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = Set::merge($this->_defaultSettings, $config);
		//extract ($this->settings[$Model->alias]);
		if (!is_array($this->settings[$Model->alias]['fields'])) {
			$this->settings[$Model->alias]['fields'] = (array)$this->settings[$Model->alias]['fields'];
		}
		if (!is_array($this->settings[$Model->alias]['map'])) {
			$this->settings[$Model->alias]['map'] = (array)$this->settings[$Model->alias]['map'];
		}
	}

	/**
	 * Decodes the fields 
	 * 
	 * @param object $Model 
	 * @param array $results 
	 * @return array 
	 * @access public 
	 */
	public function afterFind(Model $Model, $results) {
		$results = $this->decodeItems($Model, $results);
		return $results;
	}

	/**
	 * Decodes the fields of an array (if the value itself was encoded) 
	 * 
	 * @param array $arr 
	 * @return array 
	 * @access public 
	 */
	public function decodeItems(Model $Model, $arr) {
		foreach ($arr as $akey => $val) {
			if (!isset($val[$Model->alias])) {
				return $arr;
			}
			$fields = $this->settings[$Model->alias]['fields'];
		
			foreach ($val[$Model->alias] as $key => $v) {
				if (empty($fields) && !is_array($v) || !in_array($key, $fields)) {
					continue;
				}
				if ($this->isEncoded($Model, $v)) {
					if (!empty($this->settings[$Model->alias]['map'])) {
						$keys = array_keys($this->settings[$Model->alias]['fields'], $key);
						if (!empty($keys)) { 
							$key = $this->settings[$Model->alias]['map'][array_shift($keys)];
						}
					}
					
					$arr[$akey][$Model->alias][$key] = $this->decoded;
				}
			}
		}
		return $arr;
	}

	/**
	 * Saves all fields that do not belong to the current Model into 'with' helper model. 
	 * 
	 * @param object $Model 
	 * @access public 
	 */
	public function beforeSave(Model $Model) {
		$data = $Model->data[$Model->alias];
		$usedFields = $this->settings[$Model->alias]['fields'];
		$mappedFields = $this->settings[$Model->alias]['map'];
		if (empty($mappedFields)) {
			$mappedFields = $usedFields;
		}
		
		$fields = array();
		
		foreach ($mappedFields as $index => $map) {
			if (empty($map) || $map == $usedFields[$index]) {
				$fields[$usedFields[$index]] = $usedFields[$index];
				continue;
			}
			$fields[$map] = $usedFields[$index];
		}

		foreach ($data as $key => $val) {
			if (!empty($fields) && !array_key_exists($key, $fields)) {
				continue;
			}
			if (!empty($fields)) {
				$key = $fields[$key];
			}
			if (!empty($this->settings[$Model->alias]['fields']) || is_array($val)) {
				$Model->data[$Model->alias][$key] = $this->_encode($Model, $val);
			}
		}
		
		return true;
	}
	
	public function _encode(Model $Model, $val) {
		if (!empty($this->settings[$Model->alias]['fields'])) {
			if ($this->settings[$Model->alias]['input'] == 'param') {
				$val = $this->_fromParam($Model, $val);
			} elseif ($this->settings[$Model->alias]['input'] == 'list') {
				$val = $this->_fromList($Model, $val);
				if ($this->settings[$Model->alias]['unique']) {
					$val = array_unique($val);
				}
				if ($this->settings[$Model->alias]['sort']) {
					sort($val);
				}
			}
		}
		if (is_array($val)) {
			$val = json_encode($val);
		}
		return $val;
	}
	
	/**
	 * fields are absolutely necessary to function properly!
	 * 2011-06-18 ms
	 */
	public function _decode(Model $Model, $val) {
		$decoded = json_decode($val);
		if ($decoded === false) {
			return false;
		}
		$decoded = (array)$decoded;
		if ($this->settings[$Model->alias]['output'] == 'param') {
			$decoded = $this->_toParam($Model, $decoded);
		} elseif ($this->settings[$Model->alias]['output'] == 'list') {
			$decoded = $this->_toList($Model, $decoded);
		}
		return $decoded;
	}
	
	/**
	 * array() => param1:value1|param2:value2|...
	 */
	public function _toParam(Model $Model, $val) {
		$res = array();
		foreach ($val as $key => $v) {
			$res[] = $key.$this->settings[$Model->alias]['keyValueSeparator'].$v;
		}
		return implode($this->settings[$Model->alias]['separator'], $res);
	}
	
	public function _fromParam(Model $Model, $val) {
		$leftBound = $this->settings[$Model->alias]['leftBound'];
		$rightBound = $this->settings[$Model->alias]['rightBound'];
		$separator = $this->settings[$Model->alias]['separator'];
		
		$res = array();
		$pieces = String::tokenize($val, $separator, $leftBound, $rightBound);
		foreach ($pieces as $piece) {
			$subpieces = String::tokenize($piece, $this->settings[$Model->alias]['keyValueSeparator'], $leftBound, $rightBound);
			if (count($subpieces) < 2) {
				continue;
			}
			$res[$subpieces[0]] = $subpieces[1]; 
		}
		return $res;
	}
	
	/**
	 * array() => value1|value2|value3|...
	 */
	public function _toList(Model $Model, $val) {
		return implode($this->settings[$Model->alias]['separator'], $val);
	}
	
	public function _fromList(Model $Model, $val) {
		extract($this->settings[$Model->alias]);
		
		return String::tokenize($val, $separator, $leftBound, $rightBound);
	}

	/**
	 * Checks if string is encoded array/object 
	 * 
	 * @param string string to check 
	 * @access public 
	 * @return boolean 
	 */
	public function isEncoded(Model $Model, $str) {
		$this->decoded = $this->_decode($Model, $str);

		if ($this->decoded !== false) {
			return true;
		}
		return false;
	}
}
