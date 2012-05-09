<?php

/**
 * BitmaskedBehavior
 *
 * An implementation of bitwise masks for row-level operations.
 * You can submit/register flags in different ways. The easiest way is using a static model function.
 * It should contain the bits like so (starting with 1):
 * 	1 => w, 2 => x, 4 => y, 8 => z, ... (bits as keys - names as values)
 * The order doest't matter, as long as no bit is used twice.
 * 
 * The theoretical limit for a 64-bit integer would be 64 bits (2^64).
 * But if you actually seem to need more than a hand full you 
 * obviously do something wrong and should better use a joined table etc.
 *
 * @version 1.1
 * @author Mark Scherer
 * @cake 2.x
 * @license MIT
 * @uses ModelBehavior
 * 2012-02-24 ms
 */
class BitmaskedBehavior extends ModelBehavior {

	/**
	 * settings defaults
	 */
	protected $_defaults = array(
		'field' => 'status',
		'mappedField' => null, # NULL = same as above
		//'mask' => null,
		'bits' => null,
		'before' => 'validate', // on: save or validate
	);

	/**
	 * Behavior configuration
	 *
	 * @param Model $Model
	 * @param array $config
	 * @return void
	 */
	public function setup(Model $Model, $config = array()) {
		$config = array_merge($this->_defaults, $config);
		
		if (empty($config['bits'])) {
			$config['bits'] = Inflector::pluralize($config['field']);
		}
		if (is_callable($config['bits'])) {
			$config['bits'] = call_user_func($config['bits']);
		} elseif (is_string($config['bits']) && method_exists($Model, $config['bits'])) {
			$config['bits'] = $Model->{$config['bits']}();
		} elseif (!is_array($config['bits'])) {
			$config['bits'] = false;
		}
		if (empty($config['bits'])) {
			throw new InternalErrorException('Bits not found');
		}
		ksort($config['bits'], SORT_NUMERIC);

		$this->settings[$Model->alias] = $config;
	}
	
	public function beforeFind(Model $Model, $query) {
		$field = $this->settings[$Model->alias]['field'];
		
		if (isset($query['conditions']) && is_array($query['conditions'])) {
			$query['conditions'] = $this->encodeBitmaskConditions($Model, $query['conditions']);
		}
				
		return $query;
	}
	
	public function afterFind(Model $Model, $results, $primary) {
		$field = $this->settings[$Model->alias]['field'];
		if (!($mappedField = $this->settings[$Model->alias]['mappedField'])) {
			$mappedField = $field;
		}
		
		foreach ($results as $key => $result) {
			if (isset($result[$Model->alias][$field])) {
				$results[$key][$Model->alias][$mappedField] = $this->decodeBitmask($Model, $result[$Model->alias][$field]);
			}	
		}
		
		return $results;
	}
	
	public function beforeValidate(Model $Model) {
		if ($this->settings[$Model->alias]['before'] != 'validate') {
			return true;
		}
		$this->encodeBitmaskData($Model);
		return true;
	}
	
	public function beforeSave(Model $Model) {
		if ($this->settings[$Model->alias]['before'] != 'save') {
			return true;
		}
		$this->encodeBitmaskData($Model);
		return true;
	}
		
	
	/**
	 * @param int $bitmask
	 * @return array $bitmaskArray
	 * from DB to APP
	 */
	public function decodeBitmask(Model $Model, $value) {
		$res = array();
		$i = 0;
		$value = (int) $value;
		
		foreach ($this->settings[$Model->alias]['bits'] as $key => $val) {
			$val = (($value & pow(2, $i)) != 0) ? true : false;
			if ($val) {
				$res[] = $key;
			}
			$i++;
 		}
		
		return $res;
	}
	
	/**
	 * @param array $bitmaskArray
	 * @return int $bitmask
	 * from APP to DB
	 */
	public function encodeBitmask(Model $Model, $value) {
		$res = 0;
		if (empty($value)) {
			return null;
		}
		foreach ((array) $value as $key => $val) {
			$res |= (int) $val;
		}
		if ($res === 0) {
			return null; # make sure notEmpty validation rule triggers
		}
		return $res;
	}
	
	public function encodeBitmaskConditions(Model $Model, $conditions) {
		$field = $this->settings[$Model->alias]['field'];
		if (!($mappedField = $this->settings[$Model->alias]['mappedField'])) {
			$mappedField = $field;
		}

		foreach ($conditions as $key => $val) {
			if ($key === $mappedField) {
				$conditions[$field] = $this->encodeBitmask($Model, $val);
				if ($field != $mappedField) {
					unset($conditions[$mappedField]);
				}
				continue;
			} elseif ($key === $Model->alias . '.' . $mappedField) {
				$conditions[$Model->alias . '.' .$field] = $this->encodeBitmask($Model, $val);
				if ($field != $mappedField) {
					unset($conditions[$Model->alias . '.' .$mappedField]);
				}
				continue;
			}
			if (!is_array($val)) {
				continue;
			}
			$conditions[$key] = $this->encodeBitmaskConditions($Model, $val);
		}
		return $conditions;
	}
	
	public function encodeBitmaskData(Model $Model) {
		$field = $this->settings[$Model->alias]['field'];
		if (!($mappedField = $this->settings[$Model->alias]['mappedField'])) {
			$mappedField = $field;
		}
		
		if (isset($Model->data[$Model->alias][$mappedField])) {
			$Model->data[$Model->alias][$field] = $this->encodeBitmask($Model, $Model->data[$Model->alias][$mappedField]);
		}
		if ($field != $mappedField) {
			unset($Model->data[$Model->alias][$mappedField]);
		}
	}
	
	/**
	 * @param mixed bits (int, array)
	 * @return array $sqlSnippet
	 */
	public function isBit(Model $Model, $bits) {
		$bits = (array) $bits;
		$bitmask = $this->encodeBitmask($Model, $bits);
		
		$field = $this->settings[$Model->alias]['field'];
		return array($Model->alias.'.'.$field => $bitmask);
	}

	/**
	 * @param mixed bits (int, array)
	 * @return array $sqlSnippet
	 */
	public function isNotBit(Model $Model, $bits) {
		$bits = (array) $bits;
		$bitmask = $this->encodeBitmask($Model, $bits);
		
		$field = $this->settings[$Model->alias]['field'];
		return array('NOT' => array($Model->alias.'.'.$field => $bitmask));
	}
	
	/**
	 * @param mixed bits (int, array)
	 * @return array $sqlSnippet
	 */
	public function containsBit(Model $Model, $bits) {
		$bits = (array) $bits;
		$bitmask = $this->encodeBitmask($Model, $bits);
		
		$field = $this->settings[$Model->alias]['field'];
		return array('('.$Model->alias.'.'.$field.' & ? = ?)' => array($bitmask, $bitmask));
	}
	
	/**
	 * @param mixed bits (int, array)
	 * @return array $sqlSnippet
	 */
	public function containsNotBit(Model $Model, $bits) {
		$bits = (array) $bits;
		$bitmask = $this->encodeBitmask($Model, $bits);
		
		$field = $this->settings[$Model->alias]['field'];
		return array('('.$Model->alias.'.'.$field.' & ? != ?)' => array($bitmask, $bitmask));
	}
	
}