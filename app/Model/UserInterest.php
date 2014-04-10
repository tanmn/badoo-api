<?php
App::uses('AppModel', 'Model');
/**
 * UserInterest Model
 *
 * @property Usr $Usr
 * @property Interest $Interest
 */
class UserInterest extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'user_interest';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'usr_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'interest_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => array('User.deleted_flg' => FLAG_OFF),
			'fields' => '',
			'order' => ''
		),
		'Interest' => array(
			'className' => 'Interest',
			'foreignKey' => 'interest_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
