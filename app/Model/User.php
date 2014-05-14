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
        'visitors' => true,
        'encounter' => true
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
        'Interest' => array(
            'className' => 'Interest',
            'joinTable' => NULL,
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'interest_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'with' => 'UserInterest'
        ),
    );

    public function beforeSave($options = array()) {
        // hash password
        if(!empty($this->data[$this->alias]['password'])){
            $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], null, true);
        }

        return parent::beforeSave($options);
    }

    public function beforeFind($query) {
        $my_id = isset($query['for']) ? $query['for'] : $this->id;

        // do not show blocked user
        if(!empty($my_id) && (empty($query['type']) || $query['type'] != 'blocked')){
            $blocked_ids = $this->Blocked->UserBlockedList->find('list', array(
                'conditions' => array(
                    'UserBlockedList.user_id' => $my_id
                ),
                'fields' => array('id', 'user_blocked_id'),
            ));

            if(!empty($blocked_ids)){
                $query['conditions'][] = array(
                    $this->alias . '.id NOT' => $blocked_ids
                );
            }
        }

        return $query;
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as $i => $user){
            if(empty($user[$this->alias]['id'])) continue;

            $results[$i][$this->alias]['avatar'] = $this->getAvatarURL($user[$this->alias]['id']);
        }

        return $results;
    }

    public function getAvatarURL($user_id = NULL){
        if(empty($user_id)){
            $user_id = $this->id;
        }

        $avatar = 'avatars/' . $user_id . '.jpg';

        if(file_exists(IMAGES . $avatar)){
            // for better caching
            $file_date = filemtime(IMAGES . $avatar);
            $avatar = Router::url('/' . IMAGES_URL . $avatar . '?' . $file_date, true);
        }else{
            $avatar = Router::url('/' . IMAGES_URL . 'noavatar.jpg', true);
        }

        return $avatar;
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

    public function isFriend($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return (!$from_id) || (bool) $this->UserFriendList->find('count', array(
            'conditions' => array(
                'UserFriendList.user_id' => $from_id,
                'UserFriendList.user_friend_id' => $target_id,
            )
        ));
    }

    public function addFriend($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        if(!$this->exists($target_id) || $this->isBlocked($target_id, $from_id)){
            return false;
        }

        $request = $this->FriendRequest->find('first', array(
            'conditions' => array(
                'user_friend_id' => $from_id,
                'user_id' => $target_id
            )
        ));

        if(!empty($request)){
            return $this->FriendRequest->acceptRequest($request['FriendRequest']['id']);
        }

        $invite = $this->FriendRequest->find('first', array(
            'conditions' => array(
                'user_friend_id' => $target_id,
                'user_id' => $from_id
            )
        ));

        if(!empty($invite)){
            return TRUE;
        }

        $this->FriendRequest->create();
        return $this->FriendRequest->save(array(
            'user_friend_id' => $target_id,
            'user_id' => $from_id,
            'accepted_flg' => FLAG_OFF
        ));
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

    public function visited($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $from_id && (bool) $this->Visitor->UserVisitorList->find('count', array(
            'conditions' => array(
                'UserVisitorList.user_id' => $target_id,
                'UserVisitorList.user_visitor_id' => $from_id,
            )
        ));
    }

    public function visit($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        if(!$this->exists($target_id)){
            return false;
        }

        $exist = $this->Visitor->UserVisitorList->find('first', array(
            'conditions' => array(
                'user_id' => $target_id,
                'user_visitor_id' => $from_id
            )
        ));

        if(!empty($exist)){
            return TRUE;
        }

        $this->Visitor->UserVisitorList->create();
        return $this->Visitor->UserVisitorList->save(array(
            'user_id' => $target_id,
            'user_visitor_id' => $from_id
        ));
    }

    public function isBlocked($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return (!$from_id) || (bool) $this->Blocked->UserBlockedList->find('count', array(
            'conditions' => array(
                'UserBlockedList.user_id' => $from_id,
                'UserBlockedList.user_blocked_id' => $target_id,
            )
        ));
    }

    public function blockPeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        if(!$this->exists($target_id) || $this->isBlocked($target_id, $from_id)){
            return false;
        }

        $exist = $this->Blocked->UserBlockedList->find('first', array(
            'conditions' => array(
                'user_blocked_id' => $target_id,
                'user_id' => $from_id
            )
        ));

        if(!empty($exist)){
            return TRUE;
        }

        $this->Blocked->UserBlockedList->create();
        return $this->Blocked->UserBlockedList->save(array(
            'user_blocked_id' => $target_id,
            'user_id' => $from_id
        ));
    }

    public function unblockPeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $this->Blocked->UserBlockedList->deleteAll(array(
            'user_id' => $from_id,
            'user_blocked_id' => $target_id
        ), false);
    }

    public function isLiked($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $from_id && (bool) $this->Liked->UserLikedList->find('count', array(
            'conditions' => array(
                'UserLikedList.user_id' => $from_id,
                'UserLikedList.user_like_id' => $target_id,
            )
        ));
    }

    public function likePeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        if(!$this->exists($target_id) || $this->isBlocked($target_id, $from_id)){
            return false;
        }

        $exist = $this->Liked->UserLikedList->find('first', array(
            'conditions' => array(
                'user_like_id' => $target_id,
                'user_id' => $from_id
            )
        ));

        if(!empty($exist)){
            return TRUE;
        }

        $this->Liked->UserLikedList->create();
        return $this->Liked->UserLikedList->save(array(
            'user_like_id' => $target_id,
            'user_id' => $from_id
        ));
    }

    public function unlikePeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $this->Liked->UserLikedList->deleteAll(array(
            'user_id' => $from_id,
            'user_like_id' => $target_id
        ), false);
    }

    public function isFollowing($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $from_id && (bool) $this->Favorite->UserFavList->find('count', array(
            'conditions' => array(
                'UserFavList.user_id' => $from_id,
                'UserFavList.fav_user_id' => $target_id,
            )
        ));
    }

    public function isFollowerOf($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $from_id && (bool) $this->Favorite->UserFavList->find('count', array(
            'conditions' => array(
                'UserFavList.user_id' => $target_id,
                'UserFavList.fav_user_id' => $from_id,
            )
        ));
    }

    public function followPeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        if(!$this->exists($target_id) || $this->isBlocked($target_id, $from_id)){
            return false;
        }

        $exist = $this->Favorite->UserFavList->find('first', array(
            'conditions' => array(
                'user_id' => $from_id,
                'fav_user_id' => $target_id
            )
        ));

        if(!empty($exist)){
            return TRUE;
        }

        $this->Favorite->UserFavList->create();
        return $this->Favorite->UserFavList->save(array(
            'user_id' => $from_id,
            'fav_user_id' => $target_id
        ));
    }

    public function unfollowPeople($target_id, $from_id = NULL){
        if(empty($from_id)){
            $from_id = $this->id;
        }

        return $this->Favorite->UserFavList->deleteAll(array(
            'user_id' => $from_id,
            'user_like_id' => $target_id
        ), false);
    }

    public function saveAvatar($file_data = array()){
        if(empty($this->id)){
            return __('User is not set.');
        }

        $result = $this->validateAvatar($file_data);

        if($result === true){
            try{
                $destination = IMAGES . 'avatars/' . $this->id . '.jpg';

                App::import('Lib', 'ImageResizer');

                $tool = new ImageResizer();
                try {
                    $tool->prepare($file_data['tmp_name']);
                    $tool->resize(180, 180, 220, 220, 220);
                    $tool->save($destination, 70);
                }
                catch (Exception $e) {
                    CakeLog::write('upload', 'Cannot save user avatar.' . $e->getMessage());
                }

                @unlink($file_data['tmp_name']);

                return true;
            }catch(Exception $e){
                CakeLog::write('upload', $e->getMessage());

                return __('Cannot upload avatar.');
            }
        }

        return $result;
    }

    public function deleteAvatar(){
        if(empty($this->id)){
            return __('User is not set.');
        }

        try{
            unlink(IMAGES . 'avatars/' . $this->id . '.jpg');

            return true;
        }catch(Exception $e){
            CakeLog::write('upload', $e->getMessage());

            return __('Cannot delete avatar.');
        }
    }

    protected function validateAvatar($file_data){
        if (empty($file_data['tmp_name']))
            return __('Uploaded file is broken.');

        if ($file_data['size'] > USER_AVATAR_ALLOWED_SIZE)
            return __('The file size must be under %s.', CakeNumber::toReadableSize(USER_AVATAR_ALLOWED_SIZE));

        if (!preg_match('/image\/(png|gif|jpeg)/i', $file_data['type']))
            return __('File type is not allowed.');

        return true;
    }

    /**
     * Custom find methods
     */

    protected function _findFriends($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $friend_ids = array();

                $friends = $this->FriendRequest->find('all', array(
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

                    foreach($friends as $item){
                        $friend_ids[] = $item['FriendRequest']['user_id'];
                        $friend_ids[] = $item['FriendRequest']['user_friend_id'];
                    }

                    $friend_ids = array_unique($friend_ids);
                }

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id' => $friend_ids,
                    'User.deleted_flg' => FLAG_OFF
                );

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }

    protected function _findVisitors($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->Visitor->UserVisitorList->find('list', array(
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

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }

    protected function _findFollower($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->Favorite->UserFavList->find('list', array(
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

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }

    protected function _findFollowing($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->Favorite->UserFavList->find('list', array(
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

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }

    protected function _findBlocked($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->Blocked->UserBlockedList->find('list', array(
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

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }

    protected function _findLiked($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->Liked->UserLikedList->find('list', array(
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

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }

    protected function _findEncounter($state, $query, $results = array()) {
        if ($state === 'before') {
            $my_id = isset($query['for']) ? $query['for'] : $this->id;

            if(!empty($my_id)){
                $user_ids = $this->Liked->UserLikedList->find('list', array(
                    'conditions' => array(
                        'UserLikedList.user_id' => $my_id
                    ),
                    'fields' => array('id', 'user_like_id'),
                ));

                $query['conditions'][] = array(
                    'User.id <>' => $my_id,
                    'User.id NOT' => $user_ids,
                    'User.deleted_flg' => FLAG_OFF
                );

                $query['order'] = 'rand()';

                $query['for'] = $my_id;
            }

            return $query;
        }

        return $results;
    }
}
