<?php
App::uses('AppModel', 'Model');
/**
 * Upload Model
 *
 * @property User $User
 * @property Code $Code
 */
class Upload extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Utils.Sluggable' => array(
			'label' => 'name',
			'method' => 'multibyteSlug'
		),
		'Uploader.FileValidation'
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Code' => array(
			'className' => 'Code',
			'foreignKey' => 'upload_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
