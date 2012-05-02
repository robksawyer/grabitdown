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
		'Uploader.FileValidation' => array(
			'fileName' => array(
						'required'	=> array(
										'value' => true,
										'error' => 'You must select a file first'
						),
						'extension'	=> array(
								'value' => array(
									'aif','aifc','aiff','au','kar','mid','midi','mp2','mp3',
									'mpga','ra','ram','rm','rpm','snd','tsi','wav',
									'wma','gz','gtar','z','tgz','zip','rar','rev','tar','7z'
								),
								'error' => 'You cannot upload this type of file.'
						),
						'filesize' => array(
										'value' => 5242880,
										'error' => 'This file is too large or small.'
						)
					)
		)
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
 * Validation parameters
 *
 * @var array
 */
	public $validate = array(
		'fileName' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'required' => true, 
				'allowEmpty' => false,
				'message' => 'Please select a file'
			)
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
