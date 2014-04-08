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
    public $components  = array('Auth');
    public $uses        = array('User');

    public $output = null;
    public $errors = null;

    public $paginate = array(
        'limit' => 10
    );

    public function beforeFilter($options = array()){
        if(function_exists('ob_start')) ob_start();

        // no need to call parent init
        // parent::beforeFilter($options);

        //locale
        if(!empty($this->request->query['locale'])){
            $locale = $this->request->query['locale'];

            Configure::write('Config.language', $locale);
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
                ),
            )
        );

        $this->Auth->allow('test', 'isLoggedIn', 'login', 'loginSocial', 'signup', 'nearBy');
    }

    public function beforeRender() {
        if(function_exists('ob_clean')) ob_clean();

        header('Content-type: text/json; charset=utf-8');

        if(!empty($this->errors)){
            $message = $this->errors;

            if(is_array($this->errors)){
                $errors = Set::flatten($this->errors);
                $message = array_shift($errors);
            }else{
                $message = $this->errors;
            }

            $this->output = array(
                'error' => true,
                'message' => $message
            );
        }

        // append sql logs
        if(Configure::read('debug') && empty($this->errors)){
            $data = $this->output;
            $this->output = array();
            $this->output['data'] = $data;
            $this->output['logs'] = $this->User->getDataSource()->getLog(false, false);
        }

        echo json_encode($this->output);

        if(function_exists('ob_end_flush')) ob_end_flush();

        exit;
    }

    /**
     * Signup, login and profile
     */

    public function login(){
        if(isset($this->request->data['mail'])){
            $this->request->data['User'] = array(
                'mail' => @$this->request->data['mail'],
                'password' => @$this->request->data['password']
            );

            $this->Auth->logout();

            if($this->Auth->login()){
                $this->output = $this->Auth->user();
            }else{
                $this->errors = __('Email and password are incorrect.');
            }

            return;
        }

        $this->errors = __('You are not logged in.');
    }

    public function loginSocial(){
        if(isset($this->request->data['sns_type']) && isset($this->request->data['sns_id'])){
            $user_id = $this->User->getUserIdBySns($this->request->data['sns_type'], $this->request->data['sns_id']);

            $this->_manualLogin($user_id);

            return;
        }

        $this->errors = __('Please provide SNS\'s type and id');
    }

    public function signup(){
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

        if(isset($this->request->data['mail']))
            $default['User']['mail'] = $this->request->data['mail'];

        if(isset($this->request->data['password']))
            $default['User']['password'] = $this->request->data['password'];

        if(isset($this->request->data['nickname']))
            $default['UserInfo']['nickname'] = $this->request->data['nickname'];

        if(isset($this->request->data['birthdate']))
            $default['UserInfo']['birthdate'] = $this->request->data['birthdate'];

        if(isset($this->request->data['search_purpose']))
            $default['SearchSetting']['search_purpose'] = $this->request->data['search_purpose'];

        if(isset($this->request->data['sns_type']) && isset($this->request->data['sns_id'])){
            $default['SnsInfo'] = array(
                'sns_type' => $this->request->data['sns_type'],
                'sns_id' => $this->request->data['sns_id']
            );

            if(isset($this->request->data['auth_token']))
                $default['SnsInfo']['auth_token'] = $this->request->data['auth_token'];

            if(isset($this->request->data['auth_token_secret']))
                $default['SnsInfo']['auth_token_secret'] = $this->request->data['auth_token_secret'];
        }

        $inputs = Set::merge($default, $this->request->data);

        $this->User->create();
        $result = $this->User->saveAssociated($inputs);

        if($result){
            $this->_manualLogin($this->User->id);
        }else if(!empty($this->User->validationErrors)){
            $this->errors = $this->User->validationErrors;
        }else{
            $this->errors = __('Cannot create new member.');
        }
    }

    protected function _manualLogin($user_id){
        if(!empty($user_id)){
            $user = $this->User->find(
                'first',
                array(
                    'conditions' => array(
                        $this->Auth->authenticate['Form']['scope'],
                        'User.id' => $user_id
                    ),
                    'contain' => $this->Auth->authenticate['Form']['contain']
                )
            );
        }

        if(!empty($user)){
            $user_info = $user['User'];

            unset($user['User']);
            unset($user_info['password']);

            $user = Set::merge($user_info, $user);

            $this->Auth->login($user);
            $this->output = $this->Auth->user();
        }else{
            $this->output = NULL;
        }
    }

    public function updateInfo(){
        $default = array();

        if(isset($this->request->data['nickname']) && !$this->Auth->user('UserInfo.nickname'))
            $default['UserInfo']['nickname'] = $this->request->data['nickname'];

        if(isset($this->request->data['first_name']))
            $default['UserInfo']['first_name'] = $this->request->data['first_name'];

        if(isset($this->request->data['last_name']))
            $default['UserInfo']['last_name'] = $this->request->data['last_name'];

        if(isset($this->request->data['gender']))
            $default['UserInfo']['gender'] = $this->request->data['gender'];

        if(isset($this->request->data['location']))
            $default['UserInfo']['location'] = $this->request->data['location'];

        if(isset($this->request->data['birthdate']))
            $default['UserInfo']['birthdate'] = $this->request->data['birthdate'];

        if(!empty($default)){
            $this->User->id = $this->Auth->user('id');
            $result = $this->User->updateInfo($default);

            if($result){
                $this->_manualLogin($this->Auth->user('id'));
            }else if(!empty($this->User->UserInfo->validationErrors)){
                $this->errors = $this->User->UserInfo->validationErrors;
            }else{
                $this->errors = __('Cannot save member info.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    public function isLoggedIn(){
        $this->output = $this->Auth->loggedIn();
    }

    public function me(){
        // $this->output = $this->Auth->user();
        $this->output = $this->Auth->user();
    }


    /**
     * Photo and album
     */

    public function uploadAvatar(){

    }

    public function deleteAvatar(){

    }

    public function albums($user_id = null){

    }

    public function photos($album_id = null){

    }

    public function createAlbum(){

    }

    public function deleteAlbum($album_id){

    }

    public function uploadPhoto($albumId = null){

    }

    public function deletePhoto($photo_id){

    }


    /**
     * Relation handlers
     */

    public function friendList(){
        $this->User->id = $this->Auth->user('id');
        $this->output = $this->User->find('friends');
    }

    public function friendRequests(){
        $this->User->FriendRequest->recursive = 0;
        $this->output = $this->User->FriendRequest->findAllByUserFriendId($this->Auth->user('id'));
    }

    public function addFriend($user_id){
        if(!$this->User->exists($user_id)){
            $this->errors = __('User %s does not exist.', $user_id);
            return;
        }

        if($this->Auth->user('id') == $user_id){
            $this->errors = __('You cannot add yourself as a friend.');
            return;
        }

        $result = $this->User->addFriend($this->Auth->user('id'), $user_id);

        if($result){
            $this->output = true;
        }else{
            $this->errors = __('Cannot send friend request.');
        }
    }

    public function removeFriend($user_id){
        if(empty($user_id) || $this->Auth->user('id') == $user_id){
            $this->errors = __('Friend\'s id is invalid.', $user_id);
            return;
        }

        $this->User->id = $this->Auth->user('id');
        $result = $this->User->removeFriend($user_id);

        if($result){
            $this->output = true;
        }else{
            $this->errors = __('Cannot send unfriend request.');
        }
    }

    public function followingList(){
        $this->User->id = $this->Auth->user('id');
    }

    public function followerList(){
        $this->User->id = $this->Auth->user('id');
    }

    public function followPeople($user_id){

    }

    public function unfollowPeople($user_id){

    }

    public function blockedList(){
        $this->User->id = $this->Auth->user('id');
    }

    public function blockPeople($user_id){

    }

    public function unblockPeople($user_id){

    }

    public function likedList(){
        $this->User->id = $this->Auth->user('id');
    }

    public function likePeople($target_id){

    }

    /**
     * Search filters and privacy
     */

    public function updateSearch(){
        if(isset($this->request->data['search_purpose']))
            $default['SearchSetting']['search_purpose'] = $this->request->data['search_purpose'];

        if(isset($this->request->data['search_gender']))
            $default['SearchSetting']['search_gender'] = $this->request->data['search_gender'];

        if(isset($this->request->data['search_age_from']))
            $default['SearchSetting']['search_age_from'] = $this->request->data['search_age_from'];

        if(isset($this->request->data['search_age_to']))
            $default['SearchSetting']['search_age_to'] = $this->request->data['search_age_to'];

        if(!empty($default)){
            $this->User->id = $this->Auth->user('id');
            $result = $this->User->updateSearchSettings($default);

            if($result){
                $this->_manualLogin($this->Auth->user('id'));
            }else if(!empty($this->User->SearchSetting->validationErrors)){
                $this->errors = $this->User->SearchSetting->validationErrors;
            }else{
                $this->errors = __('Cannot save search settings.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    public function updateNotificationSettings(){
        if(isset($this->request->data['new_message_flg']))
            $default['NotificationSetting']['new_message_flg'] = $this->request->data['new_message_flg'];

        if(isset($this->request->data['new_visitor_flg']))
            $default['NotificationSetting']['new_visitor_flg'] = $this->request->data['new_visitor_flg'];

        if(isset($this->request->data['new_liked_flg']))
            $default['NotificationSetting']['new_liked_flg'] = $this->request->data['new_liked_flg'];

        if(isset($this->request->data['new_fav_flg']))
            $default['NotificationSetting']['new_fav_flg'] = $this->request->data['new_fav_flg'];

        if(!empty($default)){
            $this->User->id = $this->Auth->user('id');
            $result = $this->User->updateNotificationSettings($default);

            if($result){
                $this->_manualLogin($this->Auth->user('id'));
            }else if(!empty($this->User->NotificationSetting->validationErrors)){
                $this->errors = $this->User->NotificationSetting->validationErrors;
            }else{
                $this->errors = __('Cannot save notification settings.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    public function updateProfileSettings(){
        if(isset($this->request->data['online_status_flg']))
            $default['ProfileSetting']['online_status_flg'] = $this->request->data['online_status_flg'];

        if(isset($this->request->data['location_flg']))
            $default['ProfileSetting']['location_flg'] = $this->request->data['location_flg'];

        if(isset($this->request->data['real_name_flg']))
            $default['ProfileSetting']['real_name_flg'] = $this->request->data['real_name_flg'];

        if(!empty($default)){
            $this->User->id = $this->Auth->user('id');
            $result = $this->User->updateProfileSettings($default);

            if($result){
                $this->_manualLogin($this->Auth->user('id'));
            }else if(!empty($this->User->ProfileSetting->validationErrors)){
                $this->errors = $this->User->ProfileSetting->validationErrors;
            }else{
                $this->errors = __('Cannot save profile settings.');
            }
            return;
        }

        $this->errors = __('Nothing to be updated.');
    }

    /**
     * Data search
     */

    public function nearBy($long = NULL, $lat = NULL){

    }

    public function encounter(){

    }

    /**
     * Test
     */

    public function test(){
        $this->output = $_SERVER;
        return;

        $this->User->create();
        $result = $this->User->saveAssociated(array(
            'User' => array(
                'mail' => 'tanmn@leverages.jp',
                'password' => '123456',
                'delete_flg' => FLAG_OFF
            ),
            'UserInfo' => array(
                'first_name' => 'Tan',
                'last_name' => 'Mai',
                'nickname' => 'shin',
                'location' => 'Ho Chi Minh',
                'gender' => 1,
                'birthdate' => '1990-04-13'
            ),
            'SearchSetting' => array(
                'search_purpose' => 1,
                'search_gender' => 2,
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
        ));

        if($result){
            $this->output['id'] = $this->User->id;
        }else{
            $this->errors = $this->User->validationErrors;
        }
    }
}