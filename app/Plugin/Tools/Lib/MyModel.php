<?php
App::uses('Model', 'Model');
App::uses('Utility', 'Tools.Utility');

/**
 * Model enhancements for Cake2
 * 
 * @author Mark Scherer
 * @license MIT
 * 2012-02-27 ms
 */
class MyModel extends Model {

	public $recursive = -1;
	public $actsAs = array('Containable');


/** Specific Stuff **/

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		# enable caching
		if (!Configure::read('Cache.disable') && Cache::config('sql') === false) {
			Cache::config('sql', array(
				'engine' 	=> 'File',
				'serialize' => true,
				'prefix'	=> '',
				'path' 		=> CACHE .'sql'. DS,
				'duration'	=> '+1 day'
			));
			if (!file_exists(CACHE .'sql')) {
				mkdir(CACHE .'sql'. DS, CHOWN_PUBLIC);
			}
		}

		# avoiding AppModel instances instead of real Models - testing - 2011-04-03 ms
		if (defined('HTTP_HOST') && HTTP_HOST && !is_a($this, $this->name) && $this->displayField !== 'id' && !Configure::read('Core.disableModelInstanceNotice')) {
			trigger_error('AppModel instance! Expected: ' . $this->name);
		}
	}

	/**
	 * The main method for any enumeration, should be called statically
	 * Now also supports reordering/filtering
	 * 
	 * @link http://www.dereuromark.de/2010/06/24/static-enums-or-semihardcoded-attributes/
	 * @param string $value or array $keys or NULL for complete array result
	 * @param array $options (actual data)
	 * @return mixed string/array
	 * static enums
	 * 2009-11-05 ms
	 */
	public static function enum($value, $options, $default = '') {
		if ($value !== null && !is_array($value)) {
			if (array_key_exists($value, $options)) {
				return $options[$value];
			}
			return $default;
		} elseif ($value !== null) {
			$newOptions = array();
			foreach ($value as $v) {
				$newOptions[$v] = $options[$v];
			}
			return $newOptions;
		}
		return $options;
	}

	/**
	 * Catch database errors before it’s too late
	 * //TODO: testing
	 * 2010-11-04 ms
	 */
	public function onError() {
		$err = $this->lastError();
		if (!empty($err)) {
			$this->log($err, 'sql_error');
		} else {
			$this->log('unknown error', 'sql_error');
		}
		if (!empty($this->data)) {
			$data = $this->data;
		} elseif ($this->id) {
			$data = 'id ' . $this->id;
		} else {
			$data = 'no data';
		}
		$data .= ' (' . env('REDIRECT_URL') . ')';
		$this->log($data, 'sql_error');
	}

	/**
	 * @return string Error message with error number
	 * 2010-11-06 ms
	 */
	public function lastError() {
		$db = $this->getDataSource();
		return $db->lastError();
	}

	/**
	 * combine virtual fields with fields values of find()
	 * USAGE:
	 * $this->Model->find('all', array('fields' => $this->Model->virtualFields('full_name')));
	 * Also adds the field to the virtualFields array of the model (for correct result)
	 * TODO: adding of fields only temperory!
	 * @param array $virtualFields to include
	 * 2011-10-13 ms
	 */
	public function virtualFields($fields = array()) {
		$res = array();
		foreach ((array)$fields as $field => $sql) {
			if (is_int($field)) {
				$field = $sql;
				$sql = null;
			}
			$plugin = $model = null;
			if (($pos = strrpos($field, '.')) !== false) {
				$model = substr($field, 0, $pos);
				$field = substr($field, $pos+1);

				if (($pos = strrpos($model, '.')) !== false) {
					list($plugin, $model) = pluginSplit($model);
				}
			}
			if (empty($model)) {
				$model = $this->alias;
				if ($sql === null) {
					$sql = $this->virtualFields[$field];
				} else {
					$this->virtualFields[$field] = $sql;
				}
			} else {
				if (!isset($this->$model)) {
					$fullModelName = ($plugin ? $plugin.'.' : '') . $model;
					$this->$model = ClassRegistry::init($fullModelName);
				}
				if ($sql === null) {
					$sql = $this->$model->virtualFields[$field];
				} else {
					$this->$model->virtualFields[$field] = $sql;
				}
			}
			$res[] = $sql.' AS '.$model.'__'.$field;
		}
		return $res;
	}


	/**
	 * HIGHLY EXPERIMENTAL
	 * manually escape value for updateAll() etc
	 * 2011-06-27 ms
	 */
	public function escapeValue($value) {
		if (is_numeric($value)) {
			return $value;
		}
		if (is_bool($value)) {
			return (int)$value;
		}
		return "'" . $value . "'";
	}

	/**
	 * HIGHLY EXPERIMENTAL
	 * @see http://cakephp.lighthouseapp.com/projects/42648/tickets/1799-model-should-have-escapefield-method
	 * 2011-07-05 ms
	 */
	public function value($content) {
		$db = $this->getDatasource();
		return $db->value($content);
	}


	/**
	 * TODO: move to behavior (Incremental)
	 * @param mixed id (single string)
	 * @param options:
	 * - step (defaults to 1)
	 * - current (if none it will get it from db)
	 * - reset (if true, it will be set to 0)
	 * - field (defaults to 'count')
	 * - modify (if true if will affect modified timestamp)
	 * - timestampField (if provided it will be filled with NOW())
	 * 2010-06-08 ms
	 */
	public function up($id, $customOptions = array()) {
		$step = 1;
		if (isset($customOptions['step'])) {
				$step = $customOptions['step'];
		}
		$field = 'count';
		if (isset($customOptions['field'])) {
				$field = $customOptions['field'];
		}
		
		if (isset($customOptions['reset'])) {
			$currentValue = $step = 0;
		} elseif (!isset($customOptions['current'])) {
			$currentValue = $this->field($field, array($this->alias.'.id'=>$id));
			if ($currentValue === false) {
				return false;
			}
		} else {
			$currentValue = $customOptions['current'];
		}

		$value = (int)$currentValue + (int)$step;
		$data = array($field=>$value);
		if (empty($customOptions['modify'])) {
			$data['modified'] = false;
		}
		if (!empty($customOptions['timestampField'])) {
			$data[$customOptions['timestampField']] = date(FORMAT_DB_DATETIME);
		}
		$this->id = $id;
		return $this->save($data, false);
	}


	/**
	 * improve paginate count for "normal queries"
	 * 2011-04-11 ms
	 */
	public function _paginateCount($conditions = null, $recursive = -1, $extra = array()) {
		$conditions = compact('conditions');
		if ($recursive != $this->recursive) {
			$conditions['recursive'] = $recursive;
		}
		if ($recursive == -1) {
			$extra['contain'] = array();
		}
		return $this->find('count', array_merge($conditions, $extra));
	}


	/**
	 * Retourne le prochain id auto-increment d'une table
	 * UUIDs will return false
	 * @return int next auto increment value or False on failure
	 */
	public function getNextAutoIncrement() {
		$next_increment = 0;
		$query = "SHOW TABLE STATUS WHERE name = '" . $this->tablePrefix . $this->table . "'";
		$result = $this->query($query);
		if (!isset($result[0]['TABLES']['Auto_increment'])) {
			return false;
		}
		$next_increment = (int)$result[0]['TABLES']['Auto_increment'];
		return $next_increment;
	}

	/**
	 * workaround for a cake bug which sets empty fields to NULL in Model::set()
	 * we cannot use if (isset() && empty()) statements without this fix
	 * @param array $fields (which are supposed to be present in $this->data[$this->alias])
	 * @param bool $force (if init should be forced, otherwise only if array_key exists)
	 * 2011-03-06 ms
	 */
	public function init($fields = array(), $force = false) {
		foreach ($fields as $field) {
			if ($force || array_key_exists($field, $this->data[$this->alias])) {
				if (!isset($this->data[$this->alias][$field])) {
					$this->data[$this->alias][$field] = '';
				}
			}
		}
	}

	public function saveAll($data = null, $options = array()) {
		$options['atomic'] = false;
		return parent::saveAll($data, $options);
	}

	/**
	 * enables HABTM-Validation
	 * e.g. with
	 * 'rule' => array('multiple',array('min' => 2))
	 * 2010-01-14 ms
	 */
	public function beforeValidate($options = array()) {
		foreach ($this->hasAndBelongsToMany as $k => $v) {
			if (isset($this->data[$k][$k])) {
				$this->data[$this->alias][$k] = $this->data[$k][$k];
			}
		}
		parent::beforeValidate($options);
	}


	/**
	 * @param params
	 * - key: functioName or other key used
	 * @static
	 * 2010-12-02 ms
	 */
	public function deleteCache($key) {
		//extract($params);
		$key = Inflector::underscore($key);
		if (!empty($key)) {
			Cache::delete(strtolower(Inflector::underscore($this->alias)) . '__' . $key, 'sql');
		} else {
			Cache::clear(false, 'sql');
		}
		return true;
	}

	/**
	 * Makes a subquery
	 * @link http://bakery.cakephp.org/articles/lucaswxp/2011/02/11/easy_and_simple_subquery_cakephp
	 *
	 * @param string|array $type The type o the query (only available 'count') or the $options
	 * @param string|array $options The options array or $alias in case of $type be a array
	 * @param string $alias You can use this intead of $options['alias'] if you want
	 * @return string $result sql snippet of the query to run
	 * 2011-07-05 ms
	 */
	public function subquery($type, $options = null, $alias = null) {
		$fields = array();
		if (is_string($type)) {
			$isString = true;
		} else {
			$alias = $options;
			$options = $type;
		}

		if ($alias === null) {
			$alias = 'Sub' . $this->alias . '';
		}

		if (isset($isString)) {
			switch ($type) {
				case 'count':
					$fields = array('COUNT(*)');
					break;
			}
		}

		$dbo = $this->getDataSource();

		$default = array(
			'fields' => $fields,
			'table' => $dbo->fullTableName($this),
			'alias' => $alias,
			'limit' => null,
			'offset' => null,
			'joins' => array(),
			'conditions' => array(),
			'order' => null,
			'group' => null
		);
		$params = array_merge($default, $options);
		$subQuery = $dbo->buildStatement($params, $this);
		return $subQuery;
	}

	/**
	 * Wrapper find() to cache sql queries.
	 *
	 * @access public
	 * @param array $conditions
	 * @param array $fields
	 * @param string $order
	 * @param string $recursive
	 * @return array
	 * 2010-12-02 ms
	 */
	public function find($type = null, $query = array()) {
		# reset/delete
		if (!empty($query['reset'])) {
			if (!empty($query['cache'])) {
				if (is_array($query['cache'])) {
					$key = $query['cache'][0];
				} else {
					$key = $query['cache'];
					if ($key === true) {
						$backtrace = debug_backtrace();
						$key = $backtrace[1]['function'];
					}
				}
				$this->deleteCache($key);
			}
		}

		# custom fixes
		if (is_string($type)) {
			switch ($type) {
				case 'count':
					if (isset($query['fields'])) {
						unset($query['fields']);
					}
					break;
				default:
			}
		}

		# having and group clauses enhancement
		if (is_array($query) && !empty($query['having']) && !empty($query['group'])) {
			if (!is_array($query['group'])) {
				$query['group'] = array($query['group']);
			}
			$ds = $this->getDataSource();
			$having = $ds->conditions($query['having'], true, false);
			$query['group'][count($query['group']) - 1] .= " HAVING $having";
		} elseif (is_array($query) && !empty($query['having'])) {
			$ds = $this->getDataSource();
			$having = $ds->conditions($query['having'], true, false);
			$query['conditions'][] = '1=1 HAVING '.$having;
		}

		# find
		if (!Configure::read('Cache.disable') && Configure::read('Cache.check') && !empty($query['cache'])) {
			if (is_array($query['cache'])) {
				$key = $query['cache'][0];
				$expires = DAY;
				if (!empty($query['cache'][1])) {
					$expires = $query['cache'][1];
				}
			} else {
				$key = $query['cache'];
				if ($key === true) {
					$backtrace = debug_backtrace();
					$key = $backtrace[1]['function'];
				}
				$expires = DAY;
			}

			$options = array('prefix' => strtolower(Inflector::underscore($this->alias)) . '__', );
			if (!empty($expires)) {
				$options['duration'] = $expires;
			}
			if (!Configure::read('Cache.disable')) {
				Cache::config('sql', $options);

				$key = Inflector::underscore($key);
				$results = Cache::read($key, 'sql');
			}

			if ($results === null) {
				$results = parent::find($type, $query);
				Cache::write($key, $results, 'sql');
			}
			return $results;
		}

		# Without caching
		return parent::find($type, $query);
	}

	/*
	public function _findCount($state, $query, $results = array()) {
		if (isset($query['fields'])) {
			unset($query['fields']);
		}
		pr($results);
		return parent::_findCount($state, $query, $results = array());
	}
	*/


	/**
	 * This code will add formatted list functionallity to find you can easy replace the $this->Model->find('list'); with $this->Model->find('formattedlist', array('fields' => array('Model.id', 'Model.field1', 'Model.field2', 'Model.field3'), 'format' => '%s-%s %s')); and get option tag output of: Model.field1-Model.field2 Model.field3. Even better part is being able to setup your own format for the output!
	 * @see http://bakery.cakephp.org/articles/view/add-formatted-lists-to-your-appmodel
	 * @deprecated
	 * added Caching
	 * 2009-12-27 ms
	 */
	protected function _find($type, $options = array()) {
		$res = false; // $this->_getCachedResults($type, $options);
		if ($res === false) {
			if (isset($options['cache'])) {
				unset($options['cache']);
			}
			if (!isset($options['recursive'])) {
				//$options['recursive'] = -1;
			}

			switch ($type) {
					# @see http://bakery.cakephp.org/deu/articles/nate/2010/10/10/quick-tipp_-_doing_ad-hoc-joins_bei_model_find
				case 'matches':
					if (!isset($options['joins'])) {
						$options['joins'] = array();
					}

					if (!isset($options['model']) || !isset($options['scope'])) {
						break;
					}
					$assoc = $this->hasAndBelongsToMany[$options['model']];
					$bind = "{$assoc['with']}.{$assoc['foreignKey']} = {$this->alias}.{$this->primaryKey}";

					$options['joins'][] = array(
						'table' => $assoc['joinTable'],
						'alias' => $assoc['with'],
						'type' => 'inner',
						'foreignKey' => false,
						'conditions'=> array($bind)
					);

					$bind = $options['model'] . '.' . $this->{$options['model']}->primaryKey . ' = ';
					$bind .= "{$assoc['with']}.{$assoc['associationForeignKey']}";

					$options['joins'][] = array(
						'table' => $this->{$options['model']}->table,
						'alias' => $options['model'],
						'type' => 'inner',
						'foreignKey' => false,
						'conditions'=> array($bind) + (array)$options['scope'],
					);
					unset($options['model'], $options['scope']);
					$type = 'all';
					break;
					# probably deprecated since "virtual fields" in 1.3
				case 'formattedlist':
					if (!isset($options['fields']) || count($options['fields']) < 3) {
						$res = parent::find('list', $options);
						break;
					}

					$this->recursive = -1;
					//setup formating
					$format = '';
					if (!isset($options['format'])) {
						for ($i = 0; $i < (count($options['fields']) - 1); $i++) $format .= '%s ';

						$format = substr($format, 0, -1);
					} else {
						$format = $options['format'];
					}
					//get data
					$list = parent::find('all', $options);
					// remove model alias from strings to only get field names
					$tmpPath2[] = $format;
					for ($i = 1; $i <= (count($options['fields']) - 1); $i++) {
						$field[$i] = str_replace($this->alias . '.', '', $options['fields'][$i]);
						$tmpPath2[] = '{n}.' . $this->alias . '.' . $field[$i];
					}
					//do the magic?? read the code...
					$res = Set::combine($list, '{n}.' . $this->alias . '.' . $this->primaryKey, $tmpPath2);
					break;
				default:
					$res = parent::find($type, $options);
					break;
			}
			if (!empty($this->useCache)) {
				Cache::write($this->cacheName, $res, $this->cacheConfig);
				if (Configure::read('debug') > 0) {
					$this->log('WRITE (' . $this->cacheConfig . '): ' . $this->cacheName, 'cache');
				}
			}
		} else {
			if (Configure::read('debug') > 0) {
				$this->log('READ (' . $this->cacheConfig . '): ' . $this->cacheName, 'cache');
			}
		}
		return $res;
	}
	/*
	USAGE of formattetlist:
	$this->Model->find('formattedlist',
	array(
	'fields'=>array(
	'Model.id', // allows start with the value="" tags field
	'Model.field1', // then put them in order of how you want the format to output.
	'Model.field2',
	'Model.field3',
	'Model.field4',
	'Model.field5',
	),
	'format'=>'%s-%s%s %s%s'
	)
	);
	*/

	/**
	 * @deprecated
	 */
	public function _getCachedResults($type, $options) {
		$this->useCache = true;
		if (!is_array($options) || empty($options['cache']) || Configure::read('debug') > 0 && !(Configure::read('Debug.override'))) {
			$this->useCache = false;
			return false;
		}

		if ($options['cache'] === true) {
			$this->cacheName = $this->alias . '_' . sha1($type . serialize($options));
		} else {
			/*
			if (!isset($options['cache']['name'])) {
			return false;
			}
			*/
			$this->cacheName = $this->alias . '_' . sha1($type . serialize($options));
			$this->cacheConfig = $options['cache'];
			//$this->cacheName = $this->alias . '_' . $type . '_' . $options['cache'];
			//$this->cacheConfig = isset($options['cache']['config']) ? $options['cache']['config'] : 'default';
		}

		$results = Cache::read($this->cacheName, $this->cacheConfig);
		return $results;
	}


	/*
	neighbor find problem:
	This means it will sort the results on Model.created ASC and DESC.
	However, in certain situations you would like to order on more than one
	field. For example, on a rating and a uploaddate. Requirements could look
	like: Get next en previous record of a certain Model based on the top
	rated. When the rating is equal those should be ordered on creation date.
	I suggest something similar to:

	$this->Movie->find('neighbors', array(
	'scope' => array(
	array(
	'field' => 'rating',
	'order' => 'DESC',
	'value' => 4.85
	),
	array(
	'field' => 'created',
	'order' => 'DESC',
	'value' => '2009-05-26 06:20:03'
	)
	)
	'conditions' => array(
	'approved' => true,
	'processed' => true
	)
	*/
	/**
	 * core-fix for multiple sort orders
	 * @param addiotional 'scope'=>array(field,order) - value is retrieved by (submitted) primary key
	 * 2009-07-25 ms
	 * TODO: fix it
	 * TODO: rename it to just find() or integrate it there
	 */
	public function findNeighbors($type, $options = array()) {
		if ($type == 'neighbors' && isset($options['scope'])) {
			$type == 'neighborsTry';
		}

		switch ($type) {
			case 'neighborsTry': # use own implementation

				return $xxx; # TODO: implement
				break;

			default:
				return parent::find($type, $options);
				break;
		}
	}


	/**
	 * @param mixed $id: id only, or request array
	 * @param array $options
	 * - filter: open/closed/none
	 * - field (sortField, if not id)
	 * - reverse: sortDirection (0=normalAsc/1=reverseDesc)
	 * - displayField: ($this->displayField, if empty)
	 * @param array qryOptions
	 * - recursive (defaults to -1)
	 * TODO: try to use core function, TRY TO ALLOW MULTIPLE SORT FIELDS
	 */
	public function neighbors($id = null, $options = array(), $qryOptions = array()) {
		$sortField = (!empty($options['field']) ? $options['field'] : 'created');
		$normalDirection = (!empty($options['reverse']) ? false : true);
		$sortDirWord = $normalDirection ? array('ASC', 'DESC') : array('DESC', 'ASC');
		$sortDirSymb = $normalDirection ? array('>=', '<=') : array('<=', '>=');

		$displayField = (!empty($options['displayField']) ? $options['displayField'] : $this->displayField);

		if (is_array($id)) {
			$data = $id;
			$id = $data[$this->alias]['id'];
		} elseif ($id === null) {
			$id = $this->id;
		}
		if (!empty($id)) {
			$data = $this->find('first', array('conditions' => array('id' => $id), 'contain' => array()));
		}

		if (empty($id) || empty($data) || empty($data[$this->alias][$sortField])) {
			return false;
		} else {
			$field = $data[$this->alias][$sortField];
		}
		$findOptions = array('recursive' => -1);
		if (isset($qryOptions['recursive'])) {
			$findOptions['recursive'] = $qryOptions['recursive'];
		}
		if (isset($qryOptions['contain'])) {
			$findOptions['contain'] = $qryOptions['contain'];
		}

		$findOptions['fields'] = array($this->alias . '.id', $this->alias . '.' . $displayField);
		$findOptions['conditions'][$this->alias . '.id !='] = $id;

		# //TODO: take out
		if (!empty($options['filter']) && $options['filter'] == REQUEST_STATUS_FILTER_OPEN) {
			$findOptions['conditions'][$this->alias . '.status <'] = REQUEST_STATUS_DECLINED;
		} elseif (!empty($options['filter']) && $options['filter'] == REQUEST_STATUS_FILTER_CLOSED) {
			$findOptions['conditions'][$this->alias . '.status >='] = REQUEST_STATUS_DECLINED;
		}

		$return = array();

		if (!empty($qryOptions['conditions'])) {
			$findOptions['conditions'] = Set::merge($findOptions['conditions'], $qryOptions['conditions']);
		}

		$options = $findOptions;
		$options['conditions'] = Set::merge($options['conditions'], array($this->alias . '.' . $sortField . ' ' . $sortDirSymb[1] => $field));
		$options['order'] = array($this->alias . '.' . $sortField . '' => $sortDirWord[1]);
		$this->id = $id;
		$return['prev'] = $this->find('first', $options);

		$options = $findOptions;
		$options['conditions'] = Set::merge($options['conditions'], array($this->alias . '.' . $sortField . ' ' . $sortDirSymb[0] => $field));
		$options['order'] = array($this->alias . '.' . $sortField . '' => $sortDirWord[0]); // ??? why 0 instead of 1
		$this->id = $id;
		$return['next'] = $this->find('first', $options);

		return $return;
	}

/** Validation Functions **/


	/**
	 * validates a primary or foreign key depending on the current schema data for this field
	 * recognizes uuid (char36) and aiid (int10 unsigned) - not yet mixed (varchar36)
	 * more useful than using numeric or notEmpty which are type specific
	 * @param array $data
	 * @param array $options
	 * - allowEmpty
	 * 2011-06-21 ms
	 */
	public function validateKey($data = array(), $options = array()) {
		$key = array_shift(array_keys($data));
		$value = array_shift($data);

		$schema = $this->schema($key);
		if (!$schema) {
			return true;
		}

		$defaults = array(
			'allowEmpty' => false,
		);
		$options = am($defaults, $options);

		if ($schema['type'] != 'integer') {
			if ($options['allowEmpty'] && $value === '') {
				return true;
			}
			return Validation::uuid($value);
		}
		if ($options['allowEmpty'] && $value === 0) {
			return true;
		}
		return is_numeric($value) && (int)$value == $value && $value > 0;
	}

	/**
	 * checks if the passed enum value is valid
	 * 2010-02-09 ms
	 */
	public function validateEnum($field = array(), $enum = null, $additionalKeys = array()) {
		$valueKey = array_shift(array_keys($field)); # auto-retrieve
		$value = $field[$valueKey];
		$keys = array();
		if ($enum === true) {
			$enum = $valueKey;
		}
		if ($enum !== null) {
			if (!method_exists($this, $enum)) {
				trigger_error('Enum method \'' . $enum . '()\' not exists', E_USER_ERROR);
				return false;
			}
			//TODO: make static
			$keys = $this->{$enum}();
		}
		$keys = array_merge($additionalKeys, array_keys($keys));
		if (!empty($keys) && in_array($value, $keys)) {
			return true;
		}
		return false;
	}


	/**
	 * checks if the content of 2 fields are equal
	 * Does not check on empty fields! Return TRUE even if both are empty (secure against empty in another rule)!
	 * 2009-01-22 ms
	 */
	public function validateIdentical($data = array(), $compareWith = null, $options = array()) {
		if (is_array($data)) {
			$value = array_shift($data);
		} else {
			$value = $data;
		}
		$compareValue = $this->data[$this->alias][$compareWith];

		$matching = array('string' => 'string', 'int' => 'integer', 'float' => 'float', 'bool' => 'boolean');
		if (!empty($options['cast']) && array_key_exists($options['cast'], $matching)) {
			# cast values to string/int/float/bool if desired
			settype($compareValue, $matching[$options['cast']]);
			settype($value, $matching[$options['cast']]);
		}
		return ($compareValue === $value);
	}


	/**
	 * checks a record, if it is unique - depending on other fields in this table (transfered as array)
	 * example in model: 'rule' => array ('validateUnique',array('belongs_to_table_id','some_id','user_id')),
	 * if all keys (of the array transferred) match a record, return false, otherwise true
	 * @param ARRAY other fields
	 * TODO: add possibity of deep nested validation (User -> Comment -> CommentCategory: UNIQUE comment_id, Comment.user_id)
	 * 2010-01-30 ms
	 */
	public function validateUnique($data, $fields = array(), $options = array()) {
		$id = (!empty($this->data[$this->alias]['id']) ? $this->data[$this->alias]['id'] : 0);

		foreach ($data as $key => $value) {
			$fieldName = $key;
			$fieldValue = $value; // equals: $this->data[$this->alias][$fieldName]
		}

		if (empty($fieldName) || empty($fieldValue)) { // return true, if nothing is transfered (check on that first)
			return true;
		}

		$conditions = array($this->alias . '.' . $fieldName => $fieldValue, // Model.field => $this->data['Model']['field']
			$this->alias . '.id !=' => $id, );

		# careful, if fields is not manually filled, the options will be the second param!!! big problem...
		foreach ((array )$fields as $dependingField) {
			if (isset($this->data[$this->alias][$dependingField])) { // add ONLY if some content is transfered (check on that first!)
				$conditions[$this->alias . '.' . $dependingField] = $this->data[$this->alias][$dependingField];

			} elseif (!empty($this->data['Validation'][$dependingField])) { // add ONLY if some content is transfered (check on that first!
				$conditions[$this->alias . '.' . $dependingField] = $this->data['Validation'][$dependingField];

			} elseif (!empty($id)) {
				# manual query! (only possible on edit)
				$res = $this->find('first', array('fields' => array($this->alias.'.'.$dependingField), 'conditions' => array($this->alias.'.id' => $this->data[$this->alias]['id'])));
				if (!empty($res)) {
					$conditions[$this->alias . '.' . $dependingField] = $res[$this->alias][$dependingField];
				}
			}
		}

		$this->recursive = -1;
		if (count($conditions) > 2) {
			$this->recursive = 0;
		}
		$res = $this->find('first', array('fields' => array($this->alias . '.id'), 'conditions' => $conditions));
		if (!empty($res)) {
			return false;
		}

		return true;
	}

	/**
	 * @param array $data
	 * @param array $options
	 * - scope (array of other fields as scope - isUnique dependent on other fields of the table)
	 * - batch (defaults to true, remembers previous values in order to validate batch imports)
	 * example in model: 'rule' => array ('validateUniqueExt', array('scope'=>array('belongs_to_table_id','some_id','user_id'))),
	 * http://groups.google.com/group/cake-php/browse_thread/thread/880ee963456739ec
	 * //TODO: test!!!
	 * 2011-03-27 ms
	 */
	public function validateUniqueExt($data, $options = array()) {
		foreach ($data as $key => $value) {
			$fieldName = $key;
			$fieldValue = $value;
		}
		$defaults = array('batch' => true, 'scope' => array());
		$options = array_merge($defaults, $options);

		# for batch
		if ($options['batch'] !== false && !empty($this->batchRecords)) {
			if (array_key_exists($value, $this->batchRecords[$fieldName])) {
				return $options['scope'] === $this->batchRecords[$fieldName][$value];
			}
		}

		# continue with validation
		if (!$this->validateUnique($data, $options['scope'])) {
			return false;
		}

		# for batch
		if ($options['batch'] !== false) {
			if (!isset($this->batchRecords)) {
				$this->batchRecords = array();
			}
			$this->batchRecords[$fieldName][$value] = $scope;
		}
		return true;
	}


	/**
	 * checks if a url is valid AND accessable (returns false otherwise)
	 * @param array/string $data: full url(!) starting with http://...
	 * @options
	 * - allowEmpty TRUE/FALSE (TRUE: if empty => return TRUE)
	 * - required TRUE/FALSE (TRUE: overrides allowEmpty)
	 * - autoComplete (default: TRUE)
	 * - deep (default: TRUE)
	 * 2010-10-18 ms
	 */
	public function validateUrl($data, $options = array()) {
		//$arguments = func_get_args();

		if (is_array($data)) {
			$url = array_shift($data);
		} else {
			$url = $data;
		}

		if (empty($url)) {
			if (!empty($options['allowEmpty']) && empty($options['required'])) {
				return true;
			}
			return false;
		}
		if (!isset($options['autoComplete']) || $options['autoComplete'] !== false) {
			$url = $this->_autoCompleteUrl($url);
		}

		if (!isset($options['strict']) || $options['strict'] !== false) {
			$options['strict'] = true;
		}

		# validation
		if (!Validation::url($url, $options['strict']) && env('REMOTE_ADDR') != '127.0.0.1') {
			return false;
		}
		# same domain?
		if (!empty($options['sameDomain']) && !empty($_SERVER['HTTP_HOST'])) {
			$is = parse_url($url, PHP_URL_HOST);
			$expected = $_SERVER['HTTP_HOST'];
			if (mb_strtolower($is) !== mb_strtolower($expected)) {
				return false;
			}
		}

		if (isset($options['deep']) && $options['deep'] === false) {
			return true;
		}
		return $this->_validUrl($url);
	}

	public function _autoCompleteUrl($url) {
		if (mb_strpos($url, '://') === false && mb_strpos($url, 'www.') === 0) {
			$url = 'http://' . $url;
		} elseif (mb_strpos($url, '/') === 0) {
			$url = Router::url($url, true);
		}
		return $url;
	}


	/**
	 * checks if a url is valid
	 * @param string url
	 * 2009-02-27 ms
	 */
	public function _validUrl($url = null) {
		App::import('Component', 'Tools.Common');
		$headers = Utility::getHeaderFromUrl($url);
		if ($headers !== false) {

			$headers = implode("\n", $headers);

			return ((bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers) && !(bool)preg_match('#^HTTP/.*\s+[(404|999)]+\s#i', $headers));
		}
		return false;
	}


	/**
	 * Validation of DateTime Fields (both Date and Time together)
	 * @param options
	 * - dateFormat (defaults to 'ymd')
	 * - allowEmpty
	 * - after/before (fieldName to validate against)
	 * - min/max (defaults to >= 1 - at least 1 minute apart)
	 * 2011-03-02 ms
	 */
	public function validateDateTime($data, $options = array()) {
		$format = !empty($options['dateFormat']) ? $options['dateFormat'] : 'ymd';

		if (is_array($data)) {
			$value = array_shift($data);
		} else {
			$value = $data;
		}

		$dateTime = explode(' ', trim($value), 2);
		$date = $dateTime[0];
		$time = (!empty($dateTime[1]) ? $dateTime[1] : '');

		if (!empty($options['allowEmpty']) && (empty($date) && empty($time) || $date == DEFAULT_DATE && $time == DEFAULT_TIME || $date == DEFAULT_DATE && empty($time))) {
			return true;
		}
		/*
		if ($this->validateDate($date, $options) && $this->validateTime($time, $options)) {
			return true;
		}
		*/
		if (Validation::date($date, $format) && Validation::time($time)) {
			# after/before?
			$minutes = isset($options['min']) ? $options['min'] : 1;
			if (!empty($options['after']) && isset($this->data[$this->alias][$options['after']])) {
				if (strtotime($this->data[$this->alias][$options['after']]) > strtotime($value) - $minutes) {
					return false;
				}
			}
			if (!empty($options['before']) && isset($this->data[$this->alias][$options['before']])) {
				if (strtotime($this->data[$this->alias][$options['before']]) < strtotime($value) + $minutes) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Validation of Date Fields (as the core one is buggy!!!)
	 * @param options
	 * - dateFormat (defaults to 'ymd')
	 * - allowEmpty
	 * - after/before (fieldName to validate against)
	 * - min (defaults to 0 - equal is OK too)
	 * 2011-03-02 ms
	 */
	public function validateDate($data, $options = array()) {
		$format = !empty($options['format']) ? $options['format'] : 'ymd';
		if (is_array($data)) {
			$value = array_shift($data);
		} else {
			$value = $data;
		}

		$dateTime = explode(' ', trim($value), 2);
		$date = $dateTime[0];

		if (!empty($options['allowEmpty']) && (empty($date) || $date == DEFAULT_DATE)) {
			return true;
		}
		if (Validation::date($date, $format)) {
			# after/before?
			$days = !empty($options['min']) ? $options['min'] : 0;
			if (!empty($options['after']) && isset($this->data[$this->alias][$options['after']])) {
				if ($this->data[$this->alias][$options['after']] > date(FORMAT_DB_DATE, strtotime($date) - $days * DAY)) {
					return false;
				}
			}
			if (!empty($options['before']) && isset($this->data[$this->alias][$options['before']])) {
				if ($this->data[$this->alias][$options['before']] < date(FORMAT_DB_DATE, strtotime($date) + $days * DAY)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * @param options
	 * - timeFormat (defaults to 'hms')
	 * - allowEmpty
	 * - after/before (fieldName to validate against)
	 * - min/max (defaults to >= 1 - at least 1 minute apart)
	 * 2011-03-02 ms
	 */
	public function validateTime($data, $options = array()) {
		if (is_array($data)) {
			$value = array_shift($data);
		} else {
			$value = $data;
		}

		$dateTime = explode(' ', trim($value), 2);
		$value = array_pop($dateTime);

		if (Validation::time($value)) {
			# after/before?
			if (!empty($options['after']) && isset($this->data[$this->alias][$options['after']])) {
				if ($this->data[$this->alias][$options['after']] >= $value) {
					return false;
				}
			}
			if (!empty($options['before']) && isset($this->data[$this->alias][$options['before']])) {
				if ($this->data[$this->alias][$options['before']] <= $value) {
					return false;
				}
			}
			return true;
		}
		return false;
	}


	//TODO
	/**
	 * Validation of Date Fields (>= minDate && <= maxDate)
	 * @param options
	 * - min/max (TODO!!)
	 * 2010-01-20 ms
	 */
	public function validateDateRange($data, $options = array()) {

	}

	//TODO
	/**
	 * Validation of Time Fields (>= minTime && <= maxTime)
	 * @param options
	 * - min/max (TODO!!)
	 * 2010-01-20 ms
	 */
	public function validateTimeRange($data, $options = array()) {

	}


	/**
	 * model validation rule for email addresses
	 * 2010-01-14 ms
	 */
	public function validateUndisposable($data, $proceed = false) {
		$email = array_shift($data);
		if (empty($email)) {
			return true;
		}
		return $this->isUndisposableEmail($email, false, $proceed);
	}

	/**
	 * NOW: can be set to work offline only (if server is down etc)
	 *
	 * checks if a email is not from a garbige hoster
	 * @param string email (neccessary)
	 * @return boolean true if valid, else false
	 * 2009-03-09 ms
	 */
	public function isUndisposableEmail($email, $onlineMode = false, $proceed = false) {
		if (!isset($this->UndisposableEmail)) {
			App::import('Vendor', 'undisposable/undisposable');
			$this->UndisposableEmail = new UndisposableEmail();
		}
		if (!$onlineMode) {
			# crashed with white screen of death otherwise... (if foreign page is 404)
			$this->UndisposableEmail->useOnlineList(false);
		}
		if (!class_exists('Validation')) {
			App::uses('Validation', 'Utility');
		}
		if (!Validation::email($email)) {
			return false;
		}
		if ($this->UndisposableEmail->isUndisposableEmail($email) === false) {
			# trigger log
			$this->log('Disposable Email detected: ' . h($email).' (IP '.env('REMOTE_ADDR').')', 'undisposable');
			if ($proceed === true) {
				return true;
			}
			return false;
		}
		return true;
	}


	/**
	 * //TODO: move outside of MyModel? use more generic "blocked" plugin!
	 * is blocked email?
	 * 2009-12-22 ms
	 */
	public function validateNotBlocked($params) {
		foreach ($params as $key => $value) {
			$email = $value;
		}
		if (!isset($this->BlockedEmail)) {
			if (false && App::import('Model', 'Tools.BlockedEmail')) {
				$this->BlockedEmail = ClassRegistry::init('Tools.BlockedEmail');
			} elseif (App::import('Model', 'Tools.Blacklist')) {
				$this->BlockedEmail = ClassRegistry::init('Tools.Blacklist');
			} else {
				trigger_error('Model Tools.Blacklist not available');
				return true;
			}

		}
		if ($this->BlockedEmail->isBlocked($email)) {
			return false;
		}
		return true;
	}


	/**
	 * Overrides the Core invalidate function from the Model class
	 * with the addition to use internationalization (I18n and L10n)
	 * @param string $field Name of the table column
	 * @param mixed $value The message or value which should be returned
	 * @param bool $translate If translation should be done here
	 * 2010-01-22 ms
	 */
	public function invalidate($field, $value = null, $translate = true) {
		if (!is_array($this->validationErrors)) {
			$this->validationErrors = array();
		}
		if (empty($value)) {
			$value = true;
		} else {
			$value = (array)$value;
		}

		if (is_array($value)) {
			$value[0] = $translate ? __($value[0]) : $value[0];

			$args = array_slice($value, 1);
			$value = vsprintf($value[0], $args);
		}
		$this->validationErrors[$field] = $value;
	}




/** General Model Functions **/

	/**
	 * CAREFUL: use LIMIT due to Starker Serverlastigkeit! or CACHE it!
	 *
	 * e.g.: 'ORDER BY ".$this->umlautsOrderFix('User.nic')." ASC'
	 *
	 * @param string variable (to be correctly ordered)
	 */
	public function umlautsOrderFix($var) {
		return "REPLACE( REPLACE( REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(".$var.", 'Ä', 'Ae'), 'Ö', 'Oe'), 'Ü', 'Ue'), 'ä', 'ae'), 'ö', 'oe'), 'ü','ue'), 'ß', 'ss')";
	}


	/**
	 * set + guaranteeFields!
	 * extends the core set function (only using data!!!)
	 * 2010-03-11 ms
	 */
	public function set($data, $data2 = null, $requiredFields = array(), $fieldList = array()) {
		if (!empty($requiredFields)) {
			$data = $this->guaranteeFields($requiredFields, $data);
		}
		if (!empty($fieldList)) {
			$data = $this->whitelist($fieldList, $data);
		}
		return parent::set($data, $data2);
	}

	/**
	 * 2011-06-01 ms
	 */
	public function whitelist($fieldList, $data = null) {
		$model = $this->alias;
		foreach ($data[$model] as $key => $val) {
			if (!in_array($key, $fieldList)) {
				unset($data[$model][$key]);
			}
		}
		return $data;
	}


	/**
	 * make sure required fields exists - in order to properly validate them
	 * @param array: field1, field2 - or field1, Model2.field1 etc
	 * @param array: data (optional, otherwise the array with the required fields will be returned)
	 * @return array
	 * 2010-03-11 ms
	 */
	public function guaranteeFields($requiredFields, $data = null) {
		$guaranteedFields = array();
		foreach ($requiredFields as $column) {
			if (strpos($column, '.') !== false) {
				list($model, $column) = explode('.', $column, 2);
			} else {
				$model = $this->alias;
			}
			$guaranteedFields[$model][$column] = ''; # now field exists in any case!
		}
		if ($data === null) {
			return $guaranteedFields;
		}
		if (!empty($guaranteedFields)) {
			$data = Set::merge($guaranteedFields, $data);
		}
		return $data;
	}
	
	/**
	 * make certain fields a requirement for the form to validate
	 * (they must only be present - can still be empty, though!)
	 * 
	 * @param array $fieldList
	 * @param bool $allowEmpty (or NULL to not touch already set elements)
	 * @return void
	 * 2012-02-20 ms
	 */
	public function requireFields($requiredFields, $allowEmpty = null) {
		if ($allowEmpty === null) {
			$setAllowEmpty = true;
		} else {
			$setAllowEmpty = $allowEmpty;
		}
		
		foreach ($requiredFields as $column) {
			if (strpos($column, '.') !== false) {
				list($model, $column) = explode('.', $column, 2);
			} else {
				$model = $this->alias;
			}
			
			if ($model === $this->alias) {
				if (empty($this->validate[$column])) {
					$this->validate[$column]['notEmpty'] = array('rule'=>'notEmpty', 'required'=>true, 'allowEmpty' => $setAllowEmpty, 'message' => 'valErrMandatoryField');
				}	else {
					$keys = array_keys($this->validate[$column]);
					if (!in_array('rule', $keys)) {
						$key = array_shift($keys);
						$this->validate[$column][$key]['required'] = true;
						if (!isset($this->validate[$column][$key]['allowEmpty'])) {
							$this->validate[$column][$key]['allowEmpty'] = $setAllowEmpty;
						}
					} else {
						$keys['required'] = true;
						if (!isset($keys['allowEmpty'])) {
							$keys['allowEmpty'] = $setAllowEmpty;
						}
						$this->validate[$column] = $keys;
					}
				}
			}
			
		}
	}


	/**
	 * instead of whitelisting
	 * @param array $blackList
	 * - array: fields to blacklist
	 * - boolean TRUE: removes all foreign_keys (_id and _key)
	 * note: one-dimensional
	 * 2009-06-19 ms
	 */
	public function blacklist($blackList = array()) {
		if ($blackList === true) {
			//TODO
		}
		return array_diff(array_keys($this->schema()), (array )$blackList);
	}


	/**
	 * find a specific entry
	 * @param id
	 * @param fields
	 * @param contain
	 * 2009-11-14 ms
	 */
	public function get($id, $fields = array(), $contain = array()) {
		if (is_array($id)) {
			$column = $id[0];
			$value = $id[1];
		} else {
			$column = 'id';
			$value = $id;
		}

		if ($fields == '*') {
			$fields = $this->alias . '.*';
		} elseif (empty($fields)) {
			//$fields = array();
			//$fields = $this->alias .'.*';
		} else {
			foreach ($fields as $row => $field) {
				if (strpos($field, '.') !== false) {
					continue;
				}
				$fields[$row] = $this->alias . '.' . $field;
			}
		}

		$options = array(
			'conditions' => array($this->alias .'.'. $column => $value),
			'fields' => $fields,
		);
		if (!empty($contain)) {
			$options['contain'] = $contain;
		}
		return $this->find('first', $options);
	}

	/**
	 * Update a row with certain fields (dont use "Model" as super-key)
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function update($id, $data, $validate = false) {
		$this->id = $id;
		return $this->save($data, $validate, array_keys($data));
	}


	/**
	 * automagic increasing of a field with e.g.:
	 * $this->id = ID; $this->inc('weight',3);
	 * @deprecated use updateAll() instead!
	 * @param string fieldname
	 * @param int factor: defaults to 1 (could be negative as well - if field is signed and can be < 0)
	 */
	public function inc($field, $factor = 1) {
		$value = Set::extract($this->read($field), $this->alias . '.' . $field);
		$value += $factor;
		return $this->saveField($field, $value);
	}

	/**
	 * Toggles Field (Important etc)
	 * @param STRING fieldName
	 * @param INT id (cleaned!)
	 * @return ARRAY record: [Model][values],...
	 * AJAX?
	 * 2008-11-06 ms
	 */
	public function toggleField($fieldName, $id) {
		$record = $this->get($id, array('id', $fieldName));

		if (!empty($record) && !empty($fieldName) && $this->hasField($fieldName)) {
			$record[$this->alias][$fieldName] = ($record[$this->alias][$fieldName] == 1 ? 0 : 1);
			$this->id = $id;
			$this->saveField($fieldName, $record[$this->alias][$fieldName]);
		}
		return $record;
	}

	/**
	 * truncate TABLE (already validated, that table exists)
	 * @param string table [default:FALSE = current model table]
	 */
	public function truncate($table = null) {
		if (empty($table)) {
			$table = $this->table;
		}
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$res = $db->truncate($table);
		return $res;
	}


/** Deep Lists **/

	/**
	 * recursive Dropdown Lists
	 * NEEDS tree behavior, NEEDS lft, rght, parent_id (!)
	 * //FIXME
	 * 2008-01-02 ms
	 */
	public function recursiveSelect($conditions = array(), $attachTree = false, $spacer = '-- ') {
		if ($attachTree) {
			$this->Behaviors->attach('Tree');
		}
		$data = $this->generateTreeList($conditions, null, null, $spacer);
		return $data;
	}

	/**
	 * from http://othy.wordpress.com/2006/06/03/generatenestedlist/
	 * NEEDS parent_id
	 * //TODO refactor for 1.2
	 * 2009-08-12 ms
	 */
	public function generateNestedList($conditions = null, $indent = '- - ') {
		$cats = $this->find('threaded', array('conditions'=>$conditions, 'fields'=>array($this->alias.'.id', $this->alias.'.'.$this->displayField, $this->alias.'.parent_id')));
		$glist = $this->_generateNestedList($cats, $indent);
		return $glist;
	}

	/**
	 * from http://othy.wordpress.com/2006/06/03/generatenestedlist/
	 * @protected
	 * 2009-08-12 ms
	 */
	public function _generateNestedList($cats, $indent, $level = 0) {
		static $list = array();
		for ($i = 0, $c = count($cats); $i < $c; $i++) {
			$list[$cats[$i][$this->alias]['id']] = str_repeat($indent, $level) . $cats[$i][$this->alias][$this->displayField];
			if (isset($cats[$i]['children']) && !empty($cats[$i]['children'])) {
				$this->_generateNestedList($cats[$i]['children'], $indent, $level + 1);
			}
		}
		return $list;
	}

}
