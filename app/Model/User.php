<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @property UserAlbum $UserAlbum
 * @property UserBlockedList $UserBlockedList
 * @property UserFavList $UserFavList
 * @property UserFriendList $UserFriendList
 * @property UserInfo $UserInfo
 * @property UserLikedList $UserLikedList
 * @property UserNotificationSetting $UserNotificationSetting
 * @property UserProfileSetting $UserProfileSetting
 * @property UserSearchSetting $UserSearchSetting
 * @property UserSnsInfo $UserSnsInfo
 * @property UserVisitorList $UserVisitorList
 */
class User extends AppModel {
    public $actsAs = array('Containable');
    public $findMethods = array(
        'friends' => true,
        'follower' => true,
        'following' => true,
        'liked' => true,
        'blocked' => true,
        'visitors' => true
    );

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'mail' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter email address',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
            'valid' => array(
                'rule' => array('email'),
                'message' => 'Your email is not valid',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'The email was used by another member',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                // 'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
		),
        'password' => array(
            'length' => array(
                'rule' => array('between', 6, 32),
                'message' => 'Your password should have at least 6 characters',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
		'deleted_flg' => array(
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

    public $hasOne = array(
        'UserInfo' => array(
            'className' => 'UserInfo',
            'foreignKey' => 'user_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProfileSetting' => array(
            'className' => 'UserProfileSetting',
            'foreignKey' => 'user_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'SearchSetting' => array(
            'className' => 'UserSearchSetting',
            'foreignKey' => 'user_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'NotificationSetting' => array(
            'className' => 'UserNotificationSetting',
            'foreignKey' => 'user_id',
            'dependent' => true,
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

	public $hasMany = array(
		'Album' => array(
			'className' => 'UserAlbum',
			'foreignKey' => 'user_id',
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
		'SnsInfo' => array(
			'className' => 'UserSnsInfo',
			'foreignKey' => 'user_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
        'FriendRequest' => array(
            'className' => 'UserFriendList',
            'foreignKey' => 'user_friend_id',
            'dependent' => false,
            'conditions' => array(
                'FriendRequest.accepted_flg' => FLAG_OFF
            ),
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
	);

    public $hasAndBelongsToMany = array(
        'Friend' => array(
            'className' => 'User',
            'joinTable' => NULL,
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'user_friend_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserFriendList'
        ),
        'Favorite' => array(
            'className' => 'User',
            'joinTable' => NULL,
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'fav_user_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserFavList'
        ),
        'Follower' => array(
            'className' => 'User',
            'joinTable' => NULL,
            'foreignKey' => 'fav_user_id',
            'associationForeignKey' => 'user_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserFavList'
        ),
        'Liked' => array(
            'className' => 'User',
            'joinTable' => NULL,
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'user_like_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserLikedList'
        ),
        'Blocked' => array(
            'className' => 'User',
            'joinTable' => NULL,
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'user_blocked_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserBlockedList'
        ),
        'Visitor' => array(
            'className' => 'User',
            'joinTable' => NULL,
            'foreignKey' => 'user_visitor_id',
            'associationForeignKey' => 'user_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserVisitorList'
        ),
    );

    public function beforeSave($options = array()) {
        if(!empty($this->data[$this->alias]['password'])){
            $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], null, true);
        }

        return parent::beforeSave($options);
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as $i => $user){
            $avatar = 'avatars/' . $user[$this->alias]['id'] . '.jpg';

            if(file_exists(IMAGES . $avatar)){
                $avatar = Router::url('/' . IMAGES_URL . $avatar, true);
            }else{
                $avatar = Router::url('/' . IMAGES_URL . 'noavatar.jpg', true);
            }

            $results[$i][$this->alias]['avatar'] = $avatar;
            unset($results[$i][$this->alias]['password']);
        }

        return $results;
    }

    public function getUserIdBySns($sns_type, $sns_id){
        $result = $this->SnsInfo->find('first', array(
            'conditions' => array(
                'sns_type' => $sns_type,
                'sns_id' => $sns_id
            ),
            'fields' => array('user_id')
        ));

        return empty($result) ? null : $result['SnsInfo']['user_id'];
    }

    public function updateInfo($data, $id = NULL){
        return $this->_updateSingle('UserInfo', $data, $id);
    }

    public function updateProfileSettings($data, $id = NULL){
        return $this->_updateSingle('ProfileSetting', $data, $id);
    }

    public function updateNotificationSettings($data, $id = NULL){
        return $this->_updateSingle('NotificationSetting', $data, $id);
    }

    public function updateSearchSettings($data, $id = NULL){
        return $this->_updateSingle('SearchSetting', $data, $id);
    }

    protected function _updateSingle($model_class, $data, $user_id = NULL){
        if((!isset($this, $model_class)) || (!is_a($this->{$model_class}, 'Model'))){
            return FALSE;
        }

        if(!empty($id)){
            $this->create();
            $this->id = $id;
        }

        $setting = $this->{$model_class}->findByUserId($this->id);
        $data = Set::merge($setting, $data);

        return $this->{$model_class}->save($data);
    }

    public function addFriend($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        $request = $this->FriendRequest->findByUserFriendId($from_id);

        if(!empty($request)){
            return $this->FriendRequest->acceptRequest($request['FriendRequest']['id']);
        }

        $this->create();
        $this->data['User']['id'] = $from_id;
        $this->data['Friend']['id'] = $target_id;

        return $this->saveAssociated($this->data);
    }

    public function removeFriend($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $this->FriendRequest->deleteAll(array(
            'OR' => array(
                array(
                    'user_id' => $target_id,
                    'user_friend_id' => $from_id
                ),
                array(
                    'user_id' => $from_id,
                    'user_friend_id' => $target_id
                ),
            )
        ), false);
    }

    public function visit($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        $this->create();
        $this->data['Visitor']['id'] = $from_id;
        $this->data['User']['id']    = $target_id;

        return $this->saveAssociated($this->data);
    }

    public function blockPeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        $this->create();
        $this->data['Blocked']['id'] = $target_id;
        $this->data['User']['id']    = $from_id;

        return $this->saveAssociated($this->data);
    }

    public function unblockPeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $this->FriendRequest->deleteAll(array(
            'user_id' => $from_id,
            'user_blocked_id' => $target_id
        ), false);
    }

    /**
     * Custom find methods
     */

    protected function _findFriends($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $friend_ids = array();

                $friends = $this->FriendRequest->find('list', array(
                    'conditions' => array(
                        'FriendRequest.accepted_flg' => FLAG_ON,
                        'OR' => array(
                            'FriendRequest.user_id' => $my_id,
                            'FriendRequest.user_friend_id' => $my_id
                        )
                    ),
                    'fields' => array('user_id', 'user_friend_id'),
                ));

                if(!empty($friends)){
                    $friend_ids = array();

                    foreach($friends as $user1 => $user2){
                        $friend_ids[] = $user1;
                        $friend_ids[] = $user2;
                    }

                    $friend_ids = array_unique($friend_ids);
                }

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $friend_ids,
                    'User.deleted_flg' => FLAG_OFF
                );
            }

            return $query;
        }

        return $results;
    }

    protected function _findVisitors($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->UserVisitorList->find('list', array(
                    'conditions' => array(
                        'UserVisitorList.user_visitor_id' => $my_id
                    ),
                    'fields' => array('id', 'user_id'),
                ));

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $user_ids,
                    'User.deleted_flg' => FLAG_OFF
                );
            }

            return $query;
        }

        return $results;
    }

    protected function _findFollower($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->UserFavList->find('list', array(
                    'conditions' => array(
                        'UserFavList.fav_user_id' => $my_id
                    ),
                    'fields' => array('id', 'user_id'),
                ));

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $user_ids,
                    'User.deleted_flg' => FLAG_OFF
                );
            }

            return $query;
        }

        return $results;
    }

    protected function _findFollowing($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->UserFavList->find('list', array(
                    'conditions' => array(
                        'UserFavList.user_id' => $my_id
                    ),
                    'fields' => array('id', 'fav_user_id'),
                ));

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $user_ids,
                    'User.deleted_flg' => FLAG_OFF
                );
            }

            return $query;
        }

        return $results;
    }

    protected function _findBlocked($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->UserBlockedList->find('list', array(
                    'conditions' => array(
                        'UserBlockedList.user_id' => $my_id
                    ),
                    'fields' => array('id', 'user_blocked_id'),
                ));

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $user_ids,
                    'User.deleted_flg' => FLAG_OFF
                );
            }

            return $query;
        }

        return $results;
    }

    protected function _findLiked($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->UserLikedList->find('list', array(
                    'conditions' => array(
                        'UserLikedList.user_id' => $my_id
                    ),
                    'fields' => array('id', 'user_like_id'),
                ));

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $user_ids,
                    'User.deleted_flg' => FLAG_OFF
                );
            }

            return $query;
        }

        return $results;
    }
}
