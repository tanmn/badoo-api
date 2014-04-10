<?php
App::uses('AppModel', 'Model');
/**
 * AlbumPhoto Model
 *
 * @property Album $Album
 */
class AlbumPhoto extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'album_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'photo' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
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
		'Album' => array(
			'className' => 'UserAlbum',
			'foreignKey' => 'album_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function checkOwner($owner_id){
        return (bool) $this->find('count', array(
            'conditions' => array(
                'Album.user_id' => $owner_id,
                $this->alias . '.id' => $this->id
            ),
            'recursive' => 0,
        ));
    }
}
