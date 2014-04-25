<?php
App::uses('AppModel', 'Model');
/**
 * UserInfo Model
 *
 * @property User $User
 */
class UserInfo extends AppModel {

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
		'nickname' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please choose a nickname',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
            'format' => array(
                'rule' => '/^[A-Za-z0-9]+[A-Za-z0-9_\.]*$/',
                'message' => 'Only characters 0-9, a-z and underscores can be used in nickname',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                // 'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'That nickname was used by another member',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                // 'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'length' => array(
                'rule' => array('between', 4, 20),
                'message' => 'Your nickname should contain 4 ~ 20 characters',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
		),
		'birthdate' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => 'Your birthday is not a valid date',
				'allowEmpty' => false,
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
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
