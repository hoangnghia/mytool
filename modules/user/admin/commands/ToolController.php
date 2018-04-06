<?php

namespace app\modules\user\admin\commands;

use Yii;
use yii\console\Exception;
use app\modules\user\models\User;
use PHPExcel;
use PHPExcel_IOFactory;

/**
 *
 * ```php
 * ./vendor/bin/luya user/tool/{action}
 * ```
 * @author Nghia Hoang <hoangnghiagll@gmail.com>
 * @since 1.0.0
 */
class ToolController extends \luya\console\Command
{
    public $access_token = 'EAAAAUaZA8jlABAEHHOh3pESwpuPrck4q2jC9Gm9JPIsF7zpHuZBYl7WQr2x5IZCi9xExgHSmnAx5PtZADlUlA3ihEBrTElBkZC7MTGNdsBPTOUlyDKXerWzrPZAbAWWZCimVtUFmoHlVvQoR4LXZB2D6DXa71vSNN67CI8RIZB1FnoVu8jvEd4e24cgICnZAJY8lvyhM95pO9OY8NgufhXAa5N';
    public $uid = '';
    // the api to send and retrieve data
    public function actionGetLike()
    {
        set_time_limit(0);
        if (empty($this->uid)) {
            $uid = $this->prompt('Pls input  UID or User Name:', ['required' => true]);
        }
//1003855726425577&id=782860448525107&aid=1073741834
        $url = str_replace(' ', '', "https://graph.facebook.com/1086555044822311?access_token=" . $this->access_token);
        $responsez = $this->http(strip_tags($url));
        $response = json_decode($responsez['data'], true);
        print_r($response);die;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
            ->setSize(10);
        $objPHPExcel->getActiveSheet()->setTitle($uid);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'User Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Email');
        $objPHPExcel->setActiveSheetIndex(0);


        $results = [];
        $count = 0;

        $this->outputInfo('Processing.........');
        $url = str_replace(' ', '', "https://graph.facebook.com/v1.0/" . trim($uid) . "/posts?limit=30&access_token=" . $this->access_token);
        $responsez = $this->http(strip_tags($url));
        $response = json_decode($responsez['data'], true);
        $paging = $response['paging'];

        $i = 2;
        $this->outputInfo('Found : ' . count($response['data']) . ' posts');
        print_r($response['data']);die;
        foreach ($response['data'] as $post) {
            if (isset($post['comments']['data'])) {
                $this->outputInfo('Found : ' . count($post['comments']['data']) . ' comments');
                foreach ($post['comments']['data'] as $data) {
                    if ($uid == $data['from']['id'] || in_array($data['from']['id'], $results) )
                        continue;
                    $results[] = $data['from']['id'];
                    $urlFb = str_replace(' ', '', "https://graph.facebook.com/" . trim($data['from']['id']) . "?access_token=" . $this->access_token);
                    $fb = $this->http(strip_tags($urlFb));
                    $response = json_decode($fb['data'], true);
                    if (!empty($response['name']) && !empty($response['email'])) {
                        /** Simply set value of cell */
                        $objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($i), $response['id']);
                        $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i), $response['name']);
//                            $objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i), $response['phone']);
                        $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i), $response['email']);

                        $this->outputInfo('Profile '.$response['name'].' have email: ' . $response['email']);
                        $count++;
                    } else {
                        $this->outputError('Profile '.$response['name'].' is not public email or phone number');
                        continue;
                    }
                    $i++;
                }

            } else {
                continue;
            }
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $a = $uid . '-' . date("Y-m-d-H-i") . '.xlsx';
        $objWriter->save(Yii::getAlias('@runtime/') . '/files/' . $a);

        $this->outputSuccess('Added emails: ' . $count);
        return $results;
    }

    /**
     * @return int
     * @throws \yii\db\Exception
     */
    public function actionSyncContact()
    {
        set_time_limit(0);
        $count = 0;
        $time = microtime(true);
        $i = 0;
        $connection = \Yii::$app->db;
        $command = $connection->createCommand('SELECT * from ticket_detail where status = 0');
        $result = $command->queryAll();
        if (is_array($result) && count($result) > 0) {
            $this->outputInfo('++++++ Running...... ++++++');
            foreach ($result as $item) {
                $contactApiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/contacts/" . $item['requester_id'];
                $contactResult = $this->httpCareSoft($contactApiUrl);
                $contactResponse = json_decode($contactResult['data'], true);

                if (!empty($contactResponse) && isset($contactResponse['contact']) && !empty($contactResponse['contact'])) {
                    $contact = $contactResponse['contact'];

                    $connection->createCommand()->insert('contact',
                        [
                            'contact_id' => $contact['id'],
                            'username' => ucwords($contact['username']),
                            'email' => strtolower(trim($contact['email'])),
                            'phone_no' => $contact['phone_no'],
                            'facebook' => $contact['facebook'],
                            'gender' => $contact['gender'],
                            'custom_filed' => $item['custom_filed'],
                            'created_at' => $contact['created_at'],
                            'updated_at' => $contact['updated_at'],
                        ])
                        ->execute();

                    Yii::$app->db->createCommand("UPDATE ticket_detail SET status = 1 WHERE requester_id=:requester_id")
                        ->bindValue(':requester_id', $item['requester_id'])
                        ->execute();
                    $this->outputInfo('++++++ Added email ' . $contact['email']);
                    $count++;

                }
                $i++;
                if ($i >= 9000) {
                    break;
                }
            }

        }
        return $this->outputSuccess("Sync contact from CareSoft is finished. Contact added: " . $count);
    }

    /**
     * Sync ticket from CareSoft
     * @return int
     * @throws \yii\db\Exception
     */
    public function actionSyncTicket()
    {
        set_time_limit(0);
        $data = true;
        $page = 1;
        $count = 0;
        $exists = 0;
        while ($data) {
            $apiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets?created_since=2018-03-25T00:00:00Z&count=1000&page=" . $page . "&order_by=created_at&order_type=desc";
            $result = $this->httpCareSoft($apiUrl);
            $response = json_decode($result['data'], true);
            $this->outputInfo("================= PAGE " . $page . " ====================");
            if (!empty($response) && isset($response['tickets']) && !empty($response['tickets'])) {
                // Insert contact to user table
                foreach ($response['tickets'] as $item) {
                    $connection = Yii::$app->db;

                    $command = $connection->createCommand("SELECT * from ticket WHERE ticket_id = '" . $item['ticket_id'] . "'");
                    $result = $command->queryAll();
                    if (!empty($result)) {
                        continue;
                    }

                    $connection->createCommand()->insert('ticket',
                        [
                            'ticket_id' => $item['ticket_id'],
                            'requester_id' => $item['requester_id'],
                        ])
                        ->execute();
                    $this->outputInfo('++++++ Insert ticket ID ' . $item['ticket_id']);
                    $count++;
                }
                $page++;
            } else {
                if (isset($response['message'])) {
                    $this->outputError('Error message ' . $response['message']);
                }
                $data = false;
                break;
            }
        }
        return $this->outputSuccess("Sync data from Care Soft is finished. User added: " . $count);
    }

    /**
     * Sync ticket details from CareSoft
     * @return int
     * @throws \yii\db\Exception
     */
    public function actionSyncTicketDetail()
    {
        set_time_limit(0);
        $count = 0;
        $time = microtime(true);
        $i = 0;
        $connection = \Yii::$app->db;
        $command = $connection->createCommand('SELECT * from ticket WHERE status = 0');
        $result = $command->queryAll();
        if (is_array($result) && count($result) > 0) {
            $this->outputInfo('++++++ Running...... ++++++');
            foreach ($result as $item) {
                $apiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets/" . $item['ticket_id'];
                $result = $this->httpCareSoft($apiUrl);
                $response = json_decode($result['data'], true);
                if (!empty($response) && isset($response['ticket']) && !empty($response['ticket'])) {
                    $ticket = $response['ticket'];
                    $connection = Yii::$app->db;
                    $campaignid = '4HpSj';
                    // Set custom_field
                    if (isset($ticket['custom_filed'])) {
                        foreach ($ticket['custom_filed'] as $itemCustomField) {
                            // 1330 is Tinh Trang ticket - TN (20738) / KH da den CN (21566)
                            if ($itemCustomField['id'] == 1330) {
                                //chot_tin_nhan
                                if ($itemCustomField['value'] == 20738) {
                                    $campaignid = '4HpJ6';
                                }
                                //den_chi_nhanh
                                if ($itemCustomField['value'] == 21566) {
                                    $campaignid = '4HpeD';
                                }
                                break;
                            }
                        }
                    }
                    $connection->createCommand()->insert('ticket_detail',
                        [
                            'ticket_id' => $ticket['ticket_id'],
                            'requester_id' => $ticket['requester_id'],
                            'custom_filed' => $campaignid
                        ])
                        ->execute();

                    \Yii::$app->db->createCommand("UPDATE ticket SET status = 1 WHERE ticket_id=:ticket_id")
                        ->bindValue(':ticket_id', $ticket['ticket_id'])
                        ->execute();

                    $this->outputInfo('Added ticket: ' . $ticket['ticket_id']);
                    $count++;
                    $i++;

                } else {
                    $i++;
                    continue;
                }
                if ($i >= 9900) {
                    break;
//                    $this->outputInfo('++++++ Sleep ++++++');
//                    $i = 0;
//                    // if it hasn't reached 60 seconds yet, sleep.
//                    $sleep = microtime(true) - $time;
//                    if ($sleep < 500) {
//                        sleep(500 - $sleep);
//                    }
//                    $time = microtime(true);
                }
            }

        }
        return $this->outputSuccess("Sync ticket from CareSoft is finished. Ticket added: " . $count);
    }


    /**
     * Sync data from Get Response
     * @return int
     */
    public function actionSyncDataGetResponse()
    {
        set_time_limit(0);
        $apikey = 'b7bae5852f9a9aa053b73510aceda86c';
        $data = true;
        $page = 1;
        $count = 0;
        $exists = 0;
        while ($data) {
            $this->outputInfo("================= PAGE " . $page . " ====================");
            $result = $this->getContactGetResponse($apikey, '', '', $page);
            $response = json_decode($result['data'], true);
            if (!empty($response)) {
                // Insert contact to user table
                foreach ($response as $item) {
                    if (!isset($item['email']) && empty($item['email'])) {
                        $this->outputError('+++++++ Email  is empty');
                        continue;
                    }
                    $checkEmail = User::find()->where(['email' => strtolower(trim($item['email']))])->asArray()->all();
                    if (is_array($checkEmail) && count($checkEmail) > 0) {
                        $this->outputError('+++++++ Email ' . $item['email'] . ' is exists');
                        $exists++;
                        continue;
                    }
                    $user = new User;
                    $user->username = ucwords($item['name']);
                    $user->email = strtolower(trim($item['email']));
                    $user->created_at = $item['createdOn'];
                    $user->updated_at = $item['changedOn'];
                    if (isset($item['campaign']['campaignId'])) {
                        $user->custom_value = $item['campaign']['campaignId'];
                    }
                    $user->source = 1;
                    try {
                        $user->save();
                        $this->outputSuccess('Added ' . $item['email']);
                        $count++;
                    } catch (Exception $ex) {
                        $this->outputError('User save error');
                        continue;
                    }
                }
            } else {
                $data = false;
                break;
            }
            $page++;

        }
        return $this->outputSuccess("Sync data from CareSoft is finished. User added: " . $count . '. User exists: ' . $exists);
    }

    /**
     * Sync data from CareSoft to local
     * @return int
     */
    public function actionSyncDataCareSoft()
    {
        set_time_limit(0);
        $data = true;
        $page = 1;
        $count = 0;
        $exists = 0;
//        $rateLimit = $this->rateLimiter(700, 700);
        while ($data) {
            $apiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets?created_since=2015-01-01T00:00:00Z&count=200&page=" . $page . "&order_by=created_at&order_type=desc";
            $result = $this->httpCareSoft($apiUrl);
            $response = json_decode($result['data'], true);
            $this->outputInfo("================= PAGE " . $page . " ====================");
            if (!empty($response) && isset($response['tickets']) && !empty($response['tickets'])) {
//                $rateLimit(700);
                $this->outputInfo('++++++ Found ' . count($response['tickets']) . ' tickets');
                // Insert contact to user table
                foreach ($response['tickets'] as $item) {
                    $ticketDetailsUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets/" . $item['ticket_id'];
                    $ticketsDetails = $this->httpCareSoft($ticketDetailsUrl);
                    $ticketsDetails = json_decode($ticketsDetails['data'], true);
                    if (!empty($ticketsDetails)) {
                        foreach ($ticketsDetails as $ticket) {
                            $campaignid = '4HpSj';
                            // Set custom_field
                            if (isset($ticket['custom_filed'])) {
                                foreach ($ticket['custom_filed'] as $itemCustomField) {
                                    // 1330 is Tinh Trang ticket - TN (20738) / KH da den CN (21566)
                                    if ($itemCustomField['id'] == 1330) {
                                        //chot_tin_nhan
                                        if ($itemCustomField['value'] == 20738) {
                                            $campaignid = '4HpJ6';
                                        }
                                        //den_chi_nhanh
                                        if ($itemCustomField['value'] == 21566) {
                                            $campaignid = '4HpeD';
                                        }
                                        break;
                                    }
                                }
                            }
                            if (isset($ticket['requester']['id'])) {
                                // Get contact details
                                $contactDetails = $ticket['requester'];
                                $contactApiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/contacts/" . $contactDetails['id'];
                                $contactResult = $this->httpCareSoft($contactApiUrl);
                                $contactResponse = json_decode($contactResult['data'], true);
                                $contact = $contactResponse['contact'];
                                if (empty($contact['phone_no']) && empty($contact['email'])) {
                                    continue;
                                }

                                $user = new User;
                                $user->username = ucwords(strtolower($contact['username']));
                                if (isset($contact['phone_no']) && !empty($contact['phone_no'])) {
                                    $user->phone = $contact['phone_no'];
                                    $checkPhone = User::find()->where(['phone' => $contact['phone_no']])->asArray()->all();
                                    if (is_array($checkPhone) && count($checkPhone) > 0) {
                                        $this->outputError('+++++++ Phone ' . $contact['phone_no'] . ' is exists');
                                        $exists++;
                                        continue;
                                    }
                                }
                                if (isset($contact['email']) && !empty($contact['email'])) {
                                    $user->email = strtolower(trim($contact['email']));
                                    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
                                    if (preg_match($pattern, strtolower(trim($contact['email']))) === 1) {
                                        $checkEmail = User::find()->where(['email' => strtolower(trim($contact['email']))])->asArray()->all();
                                        if (is_array($checkEmail) && count($checkEmail) > 0) {
                                            $this->outputError('+++++++ Email ' . $contact['email'] . ' is exists');
                                            $exists++;
                                            continue;
                                        }
                                    } else {
                                        $this->outputError('+++++++ Email ' . $contact['email'] . ' is invalid');
                                        continue;
                                    }

                                }
                                $user->created_at = $contact['created_at'];
                                $user->updated_at = $contact['updated_at'];
                                $user->custom_value = $campaignid;
                                $user->facebook = $contact['facebook'];
                                $user->source = 2;
                                try {
                                    $user->save();
                                    $info = !empty($contact['email']) ? $contact['email'] : $contact['phone_no'];
                                    $this->outputSuccess('Added ' . $info);
                                    $count++;
                                } catch (Exception $ex) {
                                    $this->outputError('User save error');
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                }
            } else {
                if (isset($response['message'])) {
                    $this->outputError('Error message ' . $response['message']);
                }
                $data = false;
                break;
            }
            $page++;

        }
        return $this->outputSuccess("Sync data from Get Response is finished. User added: " . $count . '. User exists: ' . $exists);
    }

    /**
     * Sync data from CareSoft to Get Response
     */
    public function actionSyncDataCareSoftToGetResponse()
    {
        set_time_limit(0);
        $apikey = 'b7bae5852f9a9aa053b73510aceda86c';

        $data = true;
        $page = 1;
        $created = 0;
        $exists = 0;

        while ($data) {
            // Get all tickets
            $apiUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets?created_since=2018-03-25T00:00:00Z&count=500&page=" . $page . "&order_by=created_at&order_type=desc";
            $tickets = $this->httpCareSoft($apiUrl);
            $tickets = json_decode($tickets['data'], true);
            if (is_array($tickets['tickets']) && count($tickets['tickets']) > 0) {
                foreach ($tickets['tickets'] as $item) {
                    $ticketDetailsUrl = "https://api.caresoft.vn/tmvngocdung/api/v1/tickets/" . $item['ticket_id'];
                    $ticketsDetails = $this->httpCareSoft($ticketDetailsUrl);
                    $ticketsDetails = json_decode($ticketsDetails['data'], true);
                    if (!empty($ticketsDetails)) {
                        foreach ($ticketsDetails as $ticket) {
                            $campaignid = '4HpSj';
                            // Set custom_field
                            if (isset($ticket['custom_filed'])) {
                                foreach ($ticket['custom_filed'] as $itemCustomField) {
                                    // 1330 is Tinh Trang ticket - TN (20738) / KH da den CN (21566)
                                    if ($itemCustomField['id'] == 1330) {
                                        //chot_tin_nhan
                                        if ($itemCustomField['value'] == 20738) {
                                            $campaignid = '4HpJ6';
                                        }
                                        //den_chi_nhanh
                                        if ($itemCustomField['value'] == 21566) {
                                            $campaignid = '4HpeD';
                                        }
                                        break;
                                    }
                                }
                            }
                            if (isset($ticket['requester'])) {
                                // Get contact details
                                $contactDetails = $ticket['requester'];
                                if (!empty($contactDetails) || !empty($contactDetails)) {
                                    if (!empty($contactDetails['email'])) {
                                        //Check exists on Get Response
                                        $result = $this->getContactGetResponse($apikey, strtolower(trim($contactDetails['email'])), $campaignid, 1);
                                        $result = json_decode($result['data'], true);
                                        if (empty($result)) {
                                            $createContact = $this->postContactToGetResponse($apikey, $campaignid, ucwords(strtolower($contactDetails['username'])), strtolower(trim($contactDetails['email'])));
                                            $created++;
                                            continue;
                                        } else {
                                            $exists++;
                                            continue;
                                        }
                                    } else {
                                        continue;
                                    }
                                }
                            } else {
                                continue;
                            }

                        }

                    }
                }
                $page++;
            } else {
                $data = false;
                break;
            }
        }
        return $this->outputSuccess("Sync data from CareSoft to Get Response finished. User added: " . $created);
    }


    protected function postContactToGetResponse($apikey, $campaignid, $fullname, $email)
    {
        $data = array(
            'name' => $fullname,
            'email' => $email,
            'campaign' => array('campaignId' => $campaignid),
            'ipAddress' => $_SERVER['REMOTE_ADDR'],
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
                'X-Auth-Token: api-key ' . $apikey,
            )
        );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerifyPeer);

        $response['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response['data'] = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($response['data']);

    }

    /**
     * API Get data from Get Response
     * @param $apikey
     * @param string $email
     * @param string $campaignid
     * @return array
     */
    protected function getContactGetResponse($apikey, $email = '', $campaignid = '', $page)
    {
        $timeout = 5000;
        $connectTimeout = 5000;
        $sslVerifyPeer = false;

        $response = array();
        if (!empty($email) && !empty($campaignid)) {
            $ci = curl_init('https://api.getresponse.com/v3/contacts?query[email]=' . $email . '&query[campaignId]=' . $campaignid);
        } else {
            $ci = curl_init('https://api.getresponse.com/v3/contacts?query&createdOn[from]=2015-01-01&createdOn[to]=2019-01-01&sort[createdOn]=asc&page=' . $page);
        }
        /* Curl settings */
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Auth-Token: api-key ' . $apikey,
            )
        );
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $sslVerifyPeer);

        $response['http_code'] = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $response['data'] = curl_exec($ci);

        curl_close($ci);

        return $response;
    }

    /**
     * @param $url
     * @return array
     */
    protected function httpCareSoft($url)
    {
        $timeout = 5000;
        $connectTimeout = 5000;
        $sslVerifyPeer = false;

        $response = array();
        $ci = curl_init();

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
        $response['api_call'] = $url;
        $response['data'] = curl_exec($ci);

        curl_close($ci);

        return $response;
    }

    /**
     * @param int $rate
     * @param int $per
     * @return \Closure
     */
    protected function rateLimiter($rate = 5, $per = 8)
    {
        $last_check = microtime(True);
        $allowance = $rate;

        return function ($consumed = 1) use (
            &$last_check,
            &$allowance,
            $rate,
            $per
        ) {
            $current = microtime(True);
            $time_passed = $current - $last_check;
            $last_check = $current;

            $allowance += $time_passed * ($rate / $per);
            if ($allowance > $rate)
                $allowance = $rate;

            if ($allowance < $consumed) {
                $duration = ($consumed - $allowance) * ($per / $rate);
                $last_check += $duration;
                usleep($duration * 1000000);
                $allowance = 0;
            } else
                $allowance -= $consumed;

            return;
        };
    }


    public function http($url)
    {
        $timeout = 30;
        $connectTimeout = 30;
        $sslVerifyPeer = false;

        $response = array();
        $ci = curl_init();

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
        $response['api_call'] = $url;
        $response['data'] = curl_exec($ci);

        curl_close($ci);

        return $response;
    }

    /**
     * Get all contact and export to excel
     * @throws \yii\db\Exception
     */
    public function actionExportEmail(){
        $connection = \Yii::$app->db;
        $email = $connection->createCommand("SELECT * FROM contact WHERE email <> '' && email IS NOT NULL AND username <> '' && username IS NOT NULL and status = 0 GROUP BY email");
        $result = $email->queryAll();
        $data = [];
        $count = 0;

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
            ->setSize(10);
        $objPHPExcel->getActiveSheet()->setTitle('DS Email');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Ten');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Email');
        $objPHPExcel->setActiveSheetIndex(0);
        $i = 2;
        foreach ($result as $contact) {
            $email = strtolower(trim($contact['email']));
            if (in_array( $email, $data) )
                continue;
            $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
            if (!preg_match($pattern, $email) === 1) {
                $this->outputError('+++++++ Email ' . $email . ' is invalid');
                continue;
            }

            $data[] =  $contact['email'];
            /** Simply set value of cell */
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($i), $count);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i), ucwords($contact['username']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i), $email);

            Yii::$app->db->createCommand("UPDATE contact SET status = 1 WHERE id=:id")
                ->bindValue(':id', $contact['id'])
                ->execute();

            $count++;
            $i++;
            $this->outputInfo('Add email ' . $contact['email']);
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $a = 'danh-sach-email-' . date("Y-m-d-H-i") . '.xlsx';
        $objWriter->save(Yii::getAlias('@runtime/') . '/files/' . $a);

        $this->outputSuccess('Added emails: ' . $count);
    }


    public function exportPhoneData(){
        $connection = \Yii::$app->db;
        $email = $connection->createCommand("SELECT * from contact WHERE phone <> '' AND email IS NOT NULL");
        $result = $email->queryAll();
    }
}
