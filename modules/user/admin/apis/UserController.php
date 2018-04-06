<?php

namespace app\modules\user\admin\apis;
use app\modules\user\models\User;
use Exception;
/**
 * User Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class UserController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'app\modules\user\models\User';

    /**
     * Sync data from CareSoft to Get Response
     */
    public function actionSyncDataCareSoftToGetResponse(){
        set_time_limit(0);
        $apikey= 'b7bae5852f9a9aa053b73510aceda86c';

        $data = true;
        $page = 1;
        $created = 0;
        $exists = 0;

        while($data) {
            // Get all tickets
            $apiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets?created_since=2018-03-27T00:00:00Z&count=500&page=".$page."&order_by=created_at&order_type=desc";
            $tickets = $this->httpCareSoft($apiUrl);
            $tickets = json_decode($tickets['data'],true);
            if(is_array($tickets['tickets']) && count($tickets['tickets']) > 0){
                foreach ($tickets['tickets'] as $item){
                    $ticketDetailsUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets/" . $item['ticket_id'];
                    $ticketsDetails = $this->httpCareSoft($ticketDetailsUrl);
                    $ticketsDetails = json_decode($ticketsDetails['data'],true);
                    if(!empty($ticketsDetails)){
                        foreach ($ticketsDetails as $ticket){
                            $campaignid = '4HpSj';
                            // Set custom_field
                            if(isset($ticket['custom_filed'])){
                                foreach ($ticket['custom_filed'] as $itemCustomField){
                                    // 1330 is Tinh Trang ticket - TN (20738) / KH da den CN (21566)
                                    if($itemCustomField['id'] == 1330){
                                        //chot_tin_nhan
                                        if($itemCustomField['value'] == 20738){
                                            $campaignid = '4HpJ6';
                                        }
                                        //den_chi_nhanh
                                        if($itemCustomField['value'] == 21566){
                                            $campaignid = '4HpeD';
                                        }
                                        break;
                                    }
                                }
                            }
                            if(isset($ticket['requester'])){
                                // Get contact details
                                $contactDetails = $ticket['requester'];
                                if(!empty($contactDetails) || !empty($contactDetails)) {
                                    if (!empty($contactDetails['email'])) {
                                        //Check exists on Get Response
                                        $result = $this->getContactGetResponse($apikey, strtolower(trim($contactDetails['email'])), $campaignid);
                                        $result = json_decode($result['data'],true);
                                        if (empty($result)) {
                                            $createContact = $this->postContactToGetResponse($apikey, $campaignid, ucwords(strtolower($contactDetails['username'])), strtolower(trim($contactDetails['email'])));
                                            $created++;
                                            continue;
                                        } else {
                                            $exists++;
                                            continue;
                                        }
                                    }else{
                                        continue;
                                    }
                                }
                            }else{
                                continue;
                            }

                        }

                    }
                }
                $page++;
            }else{
                $data = false;
                break;
            }
        }
        return [
            'created' => $created,
            'exists' => $exists
        ];
    }

    protected function httpCareSoft($url) {
        $timeout = 30;
        $connectTimeout = 30;
        $sslVerifyPeer = false;

        $response = array();
        $ci       = curl_init();

        /* Curl settings */
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer 8IQwZ6_shBeMuh0"));
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $sslVerifyPeer);
        curl_setopt($ci, CURLOPT_URL, $url);

        $response['http_code'] = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $response['api_call']  = $url;
        $response['data']      = curl_exec($ci);

        curl_close ($ci);

        return $response;
    }

    /**
     * Sync data from Get Response to here
     */
    public function actionSyncDataGetResponse(){
        set_time_limit(0);
        $apikey= 'b7bae5852f9a9aa053b73510aceda86c';
        $data = true;
        $page = 1;
        $count = 0;
        while($data){
            $result = $this->getContactGetResponse($apikey, '', '', $page);
            $response  =  json_decode($result['data'],true);
            if(!empty($response)){
                // Insert contact to user table
                foreach ($response as $item){
                    $checkEmail = User::find()->where(['email' => strtolower(trim($item['email']))])->asArray()->all();
                    if(is_array($checkEmail) && count($checkEmail) > 0){
                        continue;
                    }
                    $user = new User;
                    $user->username = ucwords(strtolower($item['name']));
                    $user->email = strtolower(trim($item['email']));
                    $user->created_at = $item['createdOn'];
                    $user->updated_at = $item['changedOn'];
                    if(isset($item['campaign']['campaignId'])){
                        $user->custom_value = $item['campaign']['campaignId'];
                    }
                    $user->source = 1;
                    try{
                        $user->save();
                        $count++;
                    }catch(Exception $ex){
                        continue;
                    }
                }
            }else{
                $data = false;
                break;
            }
            $page++;

        }
        return ['created' => $count];
    }

    /**
     * Sync data from Get Response to here
     */
    public function actionSyncDataCareSoft(){
        set_time_limit(0);
        $data = true;
        $page = 1;
        $count = 0;
        while($data){
            $apiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets?created_since=2018-03-25T00:00:00Z&count=1000&page=".$page."&order_by=created_at&order_type=desc";
            $result = $this->httpCareSoft($apiUrl);
            $response = json_decode($result['data'],true);
            if(!empty($response) && isset($response['tickets']) && !empty($response['tickets'])){
                // Insert contact to user table
                foreach ($response['tickets'] as $item){
                    $ticketDetailsUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets/" . $item['ticket_id'];
                    $ticketsDetails = $this->httpCareSoft($ticketDetailsUrl);
                    $ticketsDetails = json_decode($ticketsDetails['data'],true);
                    if(!empty($ticketsDetails)){
                        foreach ($ticketsDetails as $ticket){
                            $campaignid = '4HpSj';
                            // Set custom_field
                            if(isset($ticket['custom_filed'])){
                                foreach ($ticket['custom_filed'] as $itemCustomField){
                                    // 1330 is Tinh Trang ticket - TN (20738) / KH da den CN (21566)
                                    if($itemCustomField['id'] == 1330){
                                        //chot_tin_nhan
                                        if($itemCustomField['value'] == 20738){
                                            $campaignid = '4HpJ6';
                                        }
                                        //den_chi_nhanh
                                        if($itemCustomField['value'] == 21566){
                                            $campaignid = '4HpeD';
                                        }
                                        break;
                                    }
                                }
                            }
                            if(isset($ticket['requester']['id'])){
                                // Get contact details
                                $contactDetails = $ticket['requester'];
                                $contactApiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/contacts/". $contactDetails['id'];
                                $contactResult = $this->httpCareSoft($contactApiUrl);
                                $contactResponse = json_decode($contactResult['data'],true);
                                $contact = $contactResponse['contact'];
                                if(empty($contact['phone_no']) && empty($contact['email'])){
                                    continue;
                                }

                                $user = new User;
                                $user->username = ucwords(strtolower($contact['username']));
                                if(isset($contact['phone_no']) && !empty($contact['phone_no'])){
                                    $user->phone = $contact['phone_no'];
                                    $checkPhone = User::find()->where(['phone' => $contact['phone_no']])->asArray()->all();
                                    if(is_array($checkPhone) && count($checkPhone) > 0){
                                        continue;
                                    }
                                }
                                if(isset($contact['email']) && !empty($contact['email'])){
                                    $user->email = strtolower(trim($contact['email']));
                                    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
                                    if (preg_match($pattern, strtolower(trim($contact['email']))) === 1) {
                                        $checkEmail = User::find()->where(['email' => strtolower(trim($contact['email']))])->asArray()->all();
                                        if(is_array($checkEmail) && count($checkEmail) > 0){
                                            continue;
                                        }
                                    }else{
                                        continue;
                                    }

                                }
                                $user->created_at = $contact['created_at'];
                                $user->updated_at = $contact['updated_at'];
                                $user->custom_value = $campaignid;
                                $user->facebook = $contact['facebook'];
                                $user->source = 2;
                                try{
                                    $user->save();
                                    $count++;
                                }catch(Exception $ex){
                                    continue;
                                }
                            }else{
                                continue;
                            }

                        }

                    }
                }
            }else{
                $data = false;
                break;
            }
            $page++;

        }
        return ['created' => $count];
    }

    /**
     * Get data from Get Response
     * @param $apikey
     * @param string $email
     * @param string $campaignid
     * @return array
     */
    protected function getContactGetResponse($apikey, $email = '', $campaignid = '', $page){
        $timeout = 30;
        $connectTimeout = 30;
        $sslVerifyPeer = false;

        $response = array();
        if(!empty($email) && !empty($campaignid)){
            $ci = curl_init('https://api.getresponse.com/v3/contacts?query[email]='.$email.'&query[campaignId]='.$campaignid);
        }
        else{
            $ci = curl_init('https://api.getresponse.com/v3/contacts?page=' . $page . '&perPage=30');
        }
        /* Curl settings */
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Auth-Token: api-key '.$apikey,
            )
        );
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $sslVerifyPeer);

        $response['http_code'] = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $response['data']      = curl_exec($ci);

        curl_close ($ci);

        return $response;
    }


    protected function postContactToGetResponse($apikey, $campaignid, $fullname, $email){
        $data = array (
            'name' => $fullname,
            'email' => $email,
            'campaign' => array('campaignId'=>$campaignid),
            'ipAddress'=>  $_SERVER['REMOTE_ADDR'],
        );
        $timeout = 30;
        $connectTimeout = 30;
        $sslVerifyPeer = false;

        $data_string = json_encode($data);
        $ch = curl_init('https://api.getresponse.com/v3/contacts/');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Auth-Token: api-key '.$apikey,
            )
        );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerifyPeer);

        $response['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response['data']      = curl_exec($ch);

        curl_close ($ch);

        $result = json_decode($response['data']);

    }

}