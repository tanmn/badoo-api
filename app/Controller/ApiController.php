<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package     app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class ApiController extends AppController {
    public $components = array('Auth');
    public $uses = array('User');

    public $output = null;
    public $errors = null;

    public $test_user = false;

    public $paginate = array('limit' => 10);

    public function beforeFilter($options = array()) {
        if (function_exists('ob_start'))
            ob_start();

        // no need to call parent init
        // parent::beforeFilter($options);

        //locale
        if (!empty($this->request->query['locale'])) {
            $locale = $this->request->query['locale'];

            Configure::write('Config.language', $locale);
        }

        if (!empty($this->request->query['test_user'])) {
            $this->test_user = $this->request->query['test_user'];
        }

        // test only
        $this->request->data = Set::merge($this->request->data, $this->request->query);

        // auth
        $this->Auth->loginAction = array(
            'controller' => 'api',
            'action' => 'login'
        );

        $this->Auth->authenticate = array(
            'Form' => array(
                'fields' => array(
                    'username' => 'mail',
                    'password' => 'password'
                ),
                'scope' => array(
                    'deleted_flg' => FLAG_OFF
                ),
                'userModel' => 'User',
                'recursive' => -1,
                'contain' => array(
                    'UserInfo' => array(
                        'nickname',
                        'birthdate',
                        'first_name',
                        'last_name',
                        'gender'
                    ),
                    'SearchSetting' => array(
                        'search_purpose',
                        'search_gender',
                        'search_age_from',
                        'search_age_to'
                    ),
                    'ProfileSetting' => array(
                        'online_status_flg',
                        'location_flg',
                        'real_name_flg'
                    ),
                    'NotificationSetting' => array(
                        'new_message_flg',
                        'new_visitor_flg',
                        'new_liked_flg',
                        'new_fav_flg'
                    )
                )
            )
        );

        $this->Auth->allow('test', 'isLoggedIn', 'hasMember', 'login', 'loginSocial', 'signup', 'nearBy', 'profile', 'encounter');

        if($this->test_user){
            $this->_manualLogin($this->test_user);
        }

        if ($this->Auth->loggedIn()) {
            $this->User->id = $this->Auth->user('id');
            $this->User->active();
        }
    }

    public function beforeRender() {
        if (function_exists('ob_clean'))
            ob_clean();

        header('Content-type: text/json; charset=utf-8');

        if (!empty($this->errors)) {
            $message = $this->errors;

            if (is_array($this->errors)) {
                $errors = Set::flatten($this->errors);
                $message = array_shift($errors);
            } else {
                $message = $this->errors;
            }

            $this->output = array(
                'error' => true,
                'message' => $message
            );
        }

        // append sql logs
        if (Configure::read('debug')) {
            if (empty($this->errors)) {
                $data = $this->output;
                $this->output = array();
                $this->output['data'] = $data;
            }

            $this->output['logs'] = $this->User->getDataSource()->getLog(false, false);
            if (isset($this->request->params['paging']))
                $this->output['paging'] = $this->request->params['paging'];
        }

        // paging
        if (!empty($this->request->params['paging'])) {
            if (!Configure::read('debug')) {
                $data = $this->output;
                $this->output = array();
                $this->output['data'] = $data;
            }

            $paging = array_shift($this->request->params['paging']);
            unset($paging['order']);
            unset($paging['options']);
            unset($paging['paramType']);
            $this->output['paging'] = $paging;
        }

        echo json_encode($this->output);

        if (function_exists('ob_end_flush'))
            ob_end_flush();

        exit;
    }

    /**
     * Signup, login and profile
     */

    public function login() {
        if (isset($this->request->data['mail'])) {
            $this->request->data['User'] = array(
                'mail' => @$this->request->data['mail'],
                'password' => @$this->request->data['password']
            );

            $this->Auth->logout();

            if ($this->Auth->login()) {
                $this->me();
            } else {
                $this->errors = __('Email and password are incorrect.');
            }

            return;
        }

        if ($this->Auth->loggedIn()) {
            $this->me();
            return;
        }

        $this->errors = __('You are not logged in.');
    }

    public function loginSocial() {
        if (isset($this->request->data['sns_type']) && isset($this->request->data['sns_id'])) {
            $user_id = $this->User->getUserIdBySns($this->request->data['sns_type'], $this->request->data['sns_id']);

            $this->_manualLogin($user_id);

            return;
        }

        $this->errors = __('Please provide SNS\'s type and id');
    }

    public function logout(){
        $this->Auth->logout();
        $this->Session->renew();

        $this->output = true;
    }

    public function signup() {
        $default = array(
            'User' => array(
                'mail' => null,
                'password' => null,
                'delete_flg' => FLAG_OFF
            ),
            'UserInfo' => array(
                'first_name' => null,
                'last_name' => null,
                'nickname' => null,
                'location' => null,
                'gender' => null,
                'birthdate' => null
            ),
            'SearchSetting' => array(
                'search_purpose' => null,
                'search_gender' => null,
                'search_age_from' => null,
                'search_age_to' => null
            ),
            'ProfileSetting' => array(
                'online_status_flg' => FLAG_ON,
                'location_flg' => FLAG_ON,
                'real_name_flg' => FLAG_ON
            ),
            'NotificationSetting' => array(
                'new_message_flg' => FLAG_ON,
                'new_visitor_flg' => FLAG_ON,
                'new_liked_flg' => FLAG_ON,
                'new_fav_flg' => FLAG_ON
            )
        );

        if (isset($this->request->data['mail']))
            $default['User']['mail'] = $this->request->data['mail'];

        if (isset($this->request->data['password']))
            $default['User']['password'] = $this->request->data['password'];

        if (isset($this->request->data['nickname']))
            $default['UserInfo']['nickname'] = $this->request->data['nickname'];

        if (isset($this->request->data['birthdate'])){
            $default['UserInfo']['birthdate'] = date('Y-m-d', strtotime($this->request->data['birthdate']));
        }

        if (isset($this->request->data['search_purpose']))
            $default['SearchSetting']['search_purpose'] = $this->request->data['search_purpose'];

        if (isset($this->request->data['sns_type']) && isset($this->request->data['sns_id'])) {
            $default['SnsInfo'] = array(
                'sns_type' => $this->request->data['sns_type'],
                'sns_id' => $this->request->data['sns_id']
            );

            if (isset($this->request->data['auth_token']))
                $default['SnsInfo']['auth_token'] = $this->request->data['auth_token'];

            if (isset($this->request->data['auth_token_secret']))
                $default['SnsInfo']['auth_token_secret'] = $this->request->data['auth_token_secret'];
        }

        $inputs = Set::merge($default, $this->request->data);

        $this->User->create();
        $result = $this->User->saveAssociated($inputs);

        if ($result) {
            $this->_manualLogin($this->User->id);
        } else if (!empty($this->User->validationErrors)) {
            $this->errors = $this->User->validationErrors;
        } else {
            $this->errors = __('Cannot create new member.');
        }
    }

    protected function _manualLogin($user_id) {
        if (!empty($user_id)) {
            $user = $this->User->find('first', array(
                'conditions' => array(
                    $this->Auth->authenticate['Form']['scope'],
                    'User.id' => $user_id
                ),
                'contain' => $this->Auth->authenticate['Form']['contain']
            ));
        }

        if (!empty($user)) {
            $user_info = $user['User'];

            unset($user['User']);
            unset($user_info['password']);

            $user = Set::merge($user_info, $user);

            $this->Auth->login($user);
            $this->output = $this->Auth->user();
        } else {
            $this->output = NULL;
        }
    }

    public function updateInfo() {
        $default = array();

        if (isset($this->request->data['nickname']) && !$this->Auth->user('UserInfo.nickname'))
            $default['UserInfo']['nickname'] = $this->request->data['nickname'];

        if (isset($this->request->data['first_name']))
            $default['UserInfo']['first_name'] = $this->request->data['first_name'];

        if (isset($this->request->data['last_name']))
            $default['UserInfo']['last_name'] = $this->request->data['last_name'];

        if (isset($this->request->data['gender']))
            $default['UserInfo']['gender'] = $this->request->data['gender'];

        if (isset($this->request->data['location']))
            $default['UserInfo']['location'] = $this->request->data['location'];

        if (isset($this->request->data['birthdate']))
            $default['UserInfo']['birthdate'] = $this->request->data['birthdate'];

        if (!empty($default)) {
            $result = $this->User->updateInfo($default);

            if ($result) {
                $this->_manualLogin($this->Auth->user('id'));
            } else if (!empty($this->User->UserInfo->validationErrors)) {
                $this->errors = $this->User->UserInfo->validationErrors;
            } else {
                $this->errors = __('Cannot save member info.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    public function isLoggedIn() {
        $this->output = $this->Auth->loggedIn();
    }

    public function hasMember(){
        $mail_or_nickname = @$this->request->query['find'];

        if(empty($mail_or_nickname)){
            $this->errors = __('Please enter email or nickname to check user.');
            return;
        }

        $count = $this->User->find('count', array(
            'conditions' => array(
                'OR' => array(
                    'UserInfo.nickname' => $mail_or_nickname,
                    'User.mail' => $mail_or_nickname
                )
            ),
            'recursive' => 0
        ));

        $this->output = ($count > 0);
    }

    public function me() {
        $current_user = $this->Auth->user();

        $this->User->FriendRequest->recursive = 0;
        $current_user['FriendRequest'] = $this->User->FriendRequest->find('count', array(
            'conditions' => array(
                'accepted_flag' => FALSE,
                'user_friend_id' => $this->Auth->user('id')
            )
        ));

        $this->output = $current_user;
    }

    public function profile($id = NULL) {
        if (empty($id))
            $id = $this->Auth->user('id');

        $profile = $this->User->find('first', array(
            'conditions' => array(
                $this->Auth->authenticate['Form']['scope'],
                'User.id' => $id
            ),
            'contain' => array(
                'UserInfo' => array(
                    'nickname',
                    'birthdate',
                    'first_name',
                    'last_name',
                    'gender'
                )
            )
        ));

        // perform visit action
        if ($this->Auth->loggedIn() && $profile) {
            $this->User->visit($id);
        }

        $this->output = $profile ? $profile : null;
    }


    /**
     * Photo and album
     */

    public function uploadAvatar() {
        if(!($this->request->is('post') && !empty($this->request->data['avatar']))){
            $this->errors = __('Invalid upload request.');
            return;
        }

        $avatar_file = $this->request->data['avatar'];
        $result = $this->User->saveAvatar($avatar_file);

        if($result === true){
            $avatar = $this->User->getAvatarURL();
            $this->Session->write('Auth.User.avatar', $avatar);
            $this->output = $avatar;
        }else{
            $this->errors = $result;
        }
    }

    public function deleteAvatar() {
        if($this->User->deleteAvatar() === true){
            $avatar = $this->User->getAvatarURL();
            $this->Session->write('Auth.User.avatar', $avatar);
            $this->output = $avatar;
        }else{
            $this->errors = __('Cannot delete avatar.');
        }
    }

    public function albums($user_id = null) {
        if(empty($user_id)){
            $user_id = $this->Auth->user('id');
        }

        $this->output = $this->User->Album->find(
            'all',
            array(
                'contain' => array('Photo'),
                'conditions' => array(
                    'Album.user_id' => $user_id
                )
            )
        );
    }

    public function album($album_id = null){
        if(empty($album_id)){
            $this->errors = __('Invalid album id.');
            return;
        }

        $this->User->Album->id = $album_id;


        // for testing

        App::uses('Folder', 'Utility');
        $folder = new Folder(WWW_ROOT . 'files' . DS . 'samples' . DS);
        $files = $folder->find('.*\.jpg');
        $random = array_rand($files, rand(1,6));

        $output = array();

        $default_id = $this->User->Album->getDefaultAlbumId($this->User->id);
        $this->User->Album->id = $default_id;
        $album = $this->User->Album->read();

        foreach($random as $index){
            $url = Router::url('/files/samples/' . $files[$index], true);
            $output[] = array(
                'id' => $index,
                'album_id' => $default_id,
                'photo' => $url,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            );
        }

        $this->output = array(
            'Album' => $album['Album'],
            'Photo' => $output
        );
    }

    public function photo($photo_id = null){

    }

    public function createAlbum() {
        $default = array(
            'album_name' => '',
            'public_type' => 0,
            'user_id' => $this->Auth->user('id')
        );

        if (isset($this->request->data['name']))
            $default['album_name'] = trim($this->request->data['name']);

        if (isset($this->request->data['type']))
            $default['public_type'] = $this->request->data['type'];

        $this->User->Album->create();
        $result = $this->User->Album->saveAssociated($default);

        if ($result) {
            $this->output = $this->User->Album->id;
        } else if (!empty($this->User->Album->validationErrors)) {
            $this->errors = $this->User->Album->validationErrors;
        } else {
            $this->errors = __('Cannot create new album.');
        }
    }

    public function deleteAlbum($album_id) {
        $this->User->Album->id = $album_id;

        if ($this->User->Album->checkOwner($this->Auth->user('id'))) {
            if ($this->User->Album->delete()) {
                $this->output = true;
            } else {
                $this->errors = __('Cannot delete the album.');
            }
        } else {
            $this->errors = __('You can only delete your albums.');
        }
    }

    public function uploadPhoto($albumId = null) {

    }

    public function deletePhoto($photo_id) {
        $this->User->Album->Photo->id = $photo_id;

        if ($this->User->Album->Photo->checkOwner($this->Auth->user('id'))) {
            if ($this->User->Album->Photo->delete()) {
                $this->output = true;
            } else {
                $this->errors = __('Cannot delete the photo.');
            }
        } else {
            $this->errors = __('You can only delete your photos.');
        }
    }


    /**
     * Relation handlers
     */

    public function friendList() {
        $this->paginate['findType'] = 'friends';
        $this->paginate['contain'] = array('UserInfo.nickname');
        $this->output = $this->paginate('User');
    }

    public function friendRequests() {
        $this->User->FriendRequest->recursive = 0;
        $this->output = $this->User->FriendRequest->find('all', array(
            'conditions' => array(
                'accepted_flag' => FALSE,
                'user_friend_id' => $this->Auth->user('id')
            )
        ));
    }

    public function addFriend($user_id = '') {
        if (empty($user_id) || !$this->User->exists($user_id)) {
            $this->errors = __('User %s does not exist.', $user_id);
            return;
        }

        if ($this->Auth->user('id') == $user_id) {
            $this->errors = __('You cannot add yourself as a friend.');
            return;
        }

        $result = $this->User->addFriend($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send friend request.');
        }
    }

    public function removeFriend($user_id = '') {
        if (empty($user_id) || $this->Auth->user('id') == $user_id) {
            $this->errors = __('Friend\'s id is invalid.', $user_id);
            return;
        }

        $result = $this->User->removeFriend($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send unfriend request.');
        }
    }

    public function followingList() {
        $this->paginate['findType'] = 'following';
        $this->output = $this->paginate('User');
    }

    public function followerList() {
        $this->paginate['findType'] = 'follower';
        $this->output = $this->paginate('User');
    }

    public function followPeople($user_id = '') {
        if ($this->Auth->user('id') == $user_id) {
            $this->errors = __('You cannot follow yourself.');
            return;
        }

        $result = $this->User->followPeople($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send follow request.');
        }
    }

    public function unfollowPeople($user_id = '') {
        $result = $this->User->unfollowPeople($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send unfollow request.');
        }
    }

    public function blockedList() {
        $this->paginate['findType'] = 'blocked';
        $this->output = $this->paginate('User');
    }

    public function blockPeople($user_id = '') {
        if ($this->Auth->user('id') == $user_id) {
            $this->errors = __('You cannot block yourself.');
            return;
        }

        $result = $this->User->blockPeople($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send block request.');
        }
    }

    public function unblockPeople($user_id = '') {
        if (empty($user_id) || $this->Auth->user('id') == $user_id) {
            $this->errors = __('User\'s id is invalid.', $user_id);
            return;
        }

        $result = $this->User->unblockPeople($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send unblock request.');
        }
    }

    public function likedList() {
        $this->paginate['findType'] = 'liked';
        $this->output = $this->paginate('User');
    }

    public function likePeople($user_id = '') {
        if ($this->Auth->user('id') == $user_id) {
            $this->errors = __('You cannot like yourself.');
            return;
        }

        $result = $this->User->likePeople($user_id);

        if ($result) {
            $this->output = true;
        } else {
            $this->errors = __('Cannot send like request.');
        }
    }

    public function visitorList() {
        $this->paginate['findType'] = 'visitors';
        $this->output = $this->paginate('User');
    }

    /**
     * Search filters and privacy
     */

    public function updateSearch() {
        if (isset($this->request->data['search_purpose']))
            $default['SearchSetting']['search_purpose'] = $this->request->data['search_purpose'];

        if (isset($this->request->data['search_gender']))
            $default['SearchSetting']['search_gender'] = $this->request->data['search_gender'];

        if (isset($this->request->data['search_age_from']))
            $default['SearchSetting']['search_age_from'] = $this->request->data['search_age_from'];

        if (isset($this->request->data['search_age_to']))
            $default['SearchSetting']['search_age_to'] = $this->request->data['search_age_to'];

        if (!empty($default)) {
            $result = $this->User->updateSearchSettings($default);

            if ($result) {
                $this->_manualLogin($this->Auth->user('id'));
            } else if (!empty($this->User->SearchSetting->validationErrors)) {
                $this->errors = $this->User->SearchSetting->validationErrors;
            } else {
                $this->errors = __('Cannot save search settings.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    public function updateNotificationSettings() {
        if (isset($this->request->data['new_message_flg']))
            $default['NotificationSetting']['new_message_flg'] = $this->request->data['new_message_flg'];

        if (isset($this->request->data['new_visitor_flg']))
            $default['NotificationSetting']['new_visitor_flg'] = $this->request->data['new_visitor_flg'];

        if (isset($this->request->data['new_liked_flg']))
            $default['NotificationSetting']['new_liked_flg'] = $this->request->data['new_liked_flg'];

        if (isset($this->request->data['new_fav_flg']))
            $default['NotificationSetting']['new_fav_flg'] = $this->request->data['new_fav_flg'];

        if (!empty($default)) {
            $result = $this->User->updateNotificationSettings($default);

            if ($result) {
                $this->_manualLogin($this->Auth->user('id'));
            } else if (!empty($this->User->NotificationSetting->validationErrors)) {
                $this->errors = $this->User->NotificationSetting->validationErrors;
            } else {
                $this->errors = __('Cannot save notification settings.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    public function updateProfileSettings() {
        if (isset($this->request->data['online_status_flg']))
            $default['ProfileSetting']['online_status_flg'] = $this->request->data['online_status_flg'];

        if (isset($this->request->data['location_flg']))
            $default['ProfileSetting']['location_flg'] = $this->request->data['location_flg'];

        if (isset($this->request->data['real_name_flg']))
            $default['ProfileSetting']['real_name_flg'] = $this->request->data['real_name_flg'];

        if (!empty($default)) {
            $result = $this->User->updateProfileSettings($default);

            if ($result) {
                $this->_manualLogin($this->Auth->user('id'));
            } else if (!empty($this->User->ProfileSetting->validationErrors)) {
                $this->errors = $this->User->ProfileSetting->validationErrors;
            } else {
                $this->errors = __('Cannot save profile settings.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    /**
     * Data search
     */

    public function nearBy($long = NULL, $lat = NULL) {
        $this->output = $this->paginate('User');
    }

    public function encounter() {
        $this->paginate['findType'] = 'encounter';
        $this->paginate['order'] = 'rand()';
        $this->paginate['contain'] = array(
            'UserInfo' => array(
                'nickname',
                'birthdate',
                'first_name',
                'last_name',
                'gender'
            )
        );
        $this->output = $this->paginate('User');
    }

    /**
     * Test
     */

    public function test() {
        // test all users
        // $this->User->create();
        // $this->output = $this->User->find('all');

        // test if user is blocked
        // $this->output = $this->User->isBlocked(1);

        // test default album
        // $this->output = $this->User->Album->getDefaultAlbumId($this->Auth->user('id'));
        // $this->output = $this->User->Album->findByUserId($this->Auth->user('id'));
        return;

        $names = explode('|', 'Aaliyah|Aaron|Abigail|Adam|Addison|Adrian|Aiden|Alex|Alexa|Alexander|Alexandra|Alexis|Allison|Alyssa|Amelia|Andrea|Andrew|Angel|Anna|Annabelle|Anthony|Aria|Ariana|Arianna|Ashley|Aubree|Aubrey|Audrey|Austin|Autumn|Ava|Avery|Ayden|Bailey|Bella|Benjamin|Bentley|Blake|Brandon|Brayden|Brianna|Brody|Brooklyn|Bryson|Caleb|Cameron|Camila|Carlos|Caroline|Carson|Carter|Charles|Charlotte|Chase|Chloe|Christian|Christopher|Claire|Colton|Connor|Cooper|Damian|Daniel|David|Dominic|Dylan|Easton|Eli|Elijah|Elizabeth|Ella|Ellie|Emily|Emma|Ethan|Eva|Evan|Evelyn|Faith|Gabriel|Gabriella|Gavin|Genesis|Gianna|Grace|Grayson|Hailey|Hannah|Harper|Henry|Hudson|Hunter|Ian|Isaac|Isabella|Isaiah|Jace|Jack|Jackson|Jacob|James|Jasmine|Jason|Jaxon|Jayden|Jeremiah|Jocelyn|John|Jonathan|Jordan|Jose|Joseph|Joshua|Josiah|Juan|Julia|Julian|Justin|Katherine|Kayden|Kayla|Kaylee|Kennedy|Kevin|Khloe|Kimberly|Kylie|Landon|Lauren|Layla|Leah|Levi|Liam|Lillian|Lily|Logan|London|Lucas|Lucy|Luis|Luke|Lydia|Mackenzie|Madeline|Madelyn|Madison|Makayla|Mason|Matthew|Maya|Melanie|Mia|Michael|Molly|Morgan|Naomi|Natalie|Nathan|Nathaniel|Nevaeh|Nicholas|Noah|Nolan|Oliver|Olivia|Owen|Parker|Peyton|Piper|Reagan|Riley|Robert|Ryan|Ryder|Samantha|Samuel|Sarah|Savannah|Scarlett|Sebastian|Serenity|Skylar|Sofia|Sophia|Sophie|Stella|Sydney|Taylor|Thomas|Trinity|Tristan|Tyler|Victoria|Violet|William|Wyatt|Xavier|Zachary|Zoe|Zoey');

        $num_user = 100;

        $this->User->getDataSource()->begin();

        for($i = 0; $i < $num_user; $i++){
            $rand = array_rand($names, 2);
            $first_name = $names[$rand[0]];
            $last_name = $names[$rand[1]];
            $mail = strtolower("{$first_name}.{$last_name}@test.local");
            $nickname = strtolower("{$first_name}_{$last_name}");
            $gender = rand(0,2);
            $search_purpose = rand(1, 3);
            $birthdate = date('Y-m-d', rand(strtotime('1980-01-01'), strtotime('2000-12-31')));

            $user = array(
                'User' => array(
                    'mail' => $mail,
                    'password' => '123456',
                    'delete_flg' => FLAG_OFF
                ),
                'UserInfo' => array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'nickname' => $nickname,
                    'location' => 'Ho Chi Minh',
                    'gender' => $gender,
                    'birthdate' => $birthdate
                ),
                'SearchSetting' => array(
                    'search_purpose' => $search_purpose,
                    'search_gender' => rand(0,2),
                    'search_age_from' => 18,
                    'search_age_to' => 24
                ),
                'ProfileSetting' => array(
                    'online_status_flg' => FLAG_ON,
                    'location_flg' => FLAG_ON,
                    'real_name_flg' => FLAG_ON
                ),
                'NotificationSetting' => array(
                    'new_message_flg' => FLAG_ON,
                    'new_visitor_flg' => FLAG_ON,
                    'new_liked_flg' => FLAG_ON,
                    'new_fav_flg' => FLAG_ON
                )
            );

            $this->User->create();
            $result = $this->User->saveAssociated($user);
            // $result = true;

            if ($result) {
                $user['id'] = $this->User->id;
                $this->output[] = $user;
            } else {
                $this->errors = $this->User->validationErrors;
                $this->User->getDataSource()->rollback();
                return;
            }
        }
        $this->User->getDataSource()->commit();
    }
}