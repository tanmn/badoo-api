<?php
App::uses('AppModel', 'Model');
/**
 * UserAlbum Model
 *
 * @property User $User
 */
class UserAlbum extends AppModel {
    public $actsAs = array('Containable');

    protected $_last_owner = NULL;


    const DEFAULT_NAME = '__default__';

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
		'album_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'public_type' => array(
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

    public $virtualFields = array(
        'original_album_name' => 'album_name'
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

    public $hasMany = array(
        'Photo' => array(
            'className' => 'AlbumPhoto',
            'foreignKey' => 'album_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
    );

    public function checkOwner($owner_id){
        return (bool) $this->find('count', array(
            'conditions' => array(
                $this->alias . '.user_id' => $owner_id,
                $this->alias . '.id' => $this->id
            )
        ));
    }

    public function afterFind($results, $primary = false) {
        foreach($results as $index => $album){
            if(!isset($album[$this->alias])) return;

            if($album[$this->alias]['album_name'] == self::DEFAULT_NAME){
                $results[$index][$this->alias]['album_name'] = __('Uncategorized');
                $results[$index][$this->alias]['default'] = true;
                break;
            }
        }

        return $results;
    }

    public function beforeDelete($cascade = true) {
        if($this->isDefault()) return false;

        $this->_last_owner = $this->field('user_id');
        return true;
    }

    public function afterDelete(){
        $deleted_id = $this->id;

        $this->Photo->updateAll(array('album_id' => $this->getDefaultAlbumId($this->_last_owner)), array('Photo.album_id' => $deleted_id));
        $this->_last_owner = NULL;

        $this->id = $deleted_id;
    }

    public function isDefault($id = NULL){
        if(!empty($id)){
            $this->id = $id;
        }

        return $this->field('original_album_name') == self::DEFAULT_NAME;
    }

    public function getDefaultAlbumId($user_id){
        $result = $this->find('first', array(
            'conditions' => array(
                'user_id' => $user_id,
                'album_name' => self::DEFAULT_NAME,
            ),
            'fields' => array('id'),
            'recursive' => -1
        ));

        return empty($result) ? $this->createDefaultAlbum($user_id) : $result[$this->alias]['id'];
    }

    public function createDefaultAlbum($user_id){
        $this->create();

        if($this->save(array(
            'user_id' => $user_id,
            'album_name' => self::DEFAULT_NAME,
        ), false)) return $this->id;

        return NULL;
    }
}
