<?php
App::uses('AppModel', 'Model');
/**
 * UserFavList Model
 *
 * @property User $User
 * @property FavUser $FavUser
 */
class UserFavList extends AppModel {

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
		'fav_user_id' => array(
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
		'FavUser' => array(
			'className' => 'User',
			'foreignKey' => 'fav_user_id',
			'conditions' => array('FavUser.deleted_flg' => FLAG_OFF),
			'fields' => '',
			'order' => ''
		)
	);
}
