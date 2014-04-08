<?php
App::uses('AppModel', 'Model');
/**
 * UserFriendList Model
 *
 * @property User $User
 * @property UserFriend $UserFriend
 */
class UserFriendList extends AppModel {

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
		'user_friend_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
        'accepted_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
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
	// public $belongsTo = array(
	// 	'User' => array(
	// 		'className' => 'User',
	// 		'foreignKey' => 'user_id',
	// 		'conditions' => array('User.deleted_flg' => FLAG_OFF),
	// 		'fields' => '',
	// 		'order' => ''
	// 	),
	// 	'RequestedUser' => array(
	// 		'className' => 'User',
	// 		'foreignKey' => 'user_friend_id',
	// 		'conditions' => array('RequestedUser.deleted_flg' => FLAG_OFF),
	// 		'fields' => '',
	// 		'order' => ''
	// 	)
	// );
}
