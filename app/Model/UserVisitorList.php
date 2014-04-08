<?php
App::uses('AppModel', 'Model');
/**
 * UserVisitorList Model
 *
 * @property User $User
 * @property UserVisitor $UserVisitor
 */
class UserVisitorList extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_visitor_id' => array(
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
		'Visitor' => array(
			'className' => 'User',
			'foreignKey' => 'user_visitor_id',
			'conditions' => array('Visitor.deleted_flg' => FLAG_OFF),
			'fields' => '',
			'order' => ''
		)
	);
}
