<?php

namespace app\modules\user\admin\controllers;
require_once __DIR__ . '/../../../../vendor/facebook/graph-sdk/src/Facebook/autoload.php';
/**
 * User Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class UserController extends \luya\admin\ngrest\base\Controller
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'app\modules\user\models\User';

    // disables the route based permissions checks
    public $disablePermissionCheck = true;
    // let the controller know that actionData returns data in API Format (json).
    public $apiResponseActions = ['data'];


    public function actionSyncDataGetResponse(){
        return $this->render('sync-data-get-response');
    }

    public function actionSyncDataCareSoft(){
        return $this->render('sync-data-care-soft');
    }

    public function actionSyncDataCareSoftToGetResponse(){
        return $this->render('sync-data-care-soft-get-response');
    }

    public function actionSyncDataGetResponseToCareSoft(){
        return $this->render('sync-data-get-response-to-care-soft');
    }

    // the api to send and retrieve data
    public function actionData()
    {
        $access_token = 'EAAAAUaZA8jlABAB7ugcQkPQRMIMXgvEhHkly7UaYXKF0vD0nPANfEFwy7KZC3bZBZCoNtC5tmEpPnlN6zpnc229ZCzV7vG556gyeZA7eJdP7mw4XOw3yUFR9MbpGjRflfqIsEKhautNHXrtYzOQ4xuhfeZB3o98S5vPJT2acP36UwZDZD';
        $responsez = file_get_contents("https://graph.facebook.com/YeuCuDem?access_token=".$access_token);
        $response  =  json_decode($responsez,true);
        print_r($response);die;
// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
//   $helper = $fb->getRedirectLoginHelper();
//   $helper = $fb->getJavaScriptHelper();
//   $helper = $fb->getCanvasHelper();
//   $helper = $fb->getPageTabHelper();

        try {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $fb->get('/me?fields=all_emails,mobile_phone,address', $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $me = $response->getGraphUser();
        echo 'Logged in as ' . $me->getName();
        die;
        return [
            'time' => time(),
        ];
    }
}