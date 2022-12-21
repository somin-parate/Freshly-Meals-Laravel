<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    private static $API_ACCESS_KEY = 'AAAAHYwZhrk:APA91bEzHWFb03s1kMhPHb6WWnjyMJ6ydCXzVUc_0bgzS6dECa61QJEmQiM48vr6H9MHb9jko0LIJPVPsZSRfPAPfrUh98lWW_hj8xrXIttHu5VBDLmE_0pQUq-y5XbfsV2sCERJ_Wy_';

    private static $API_ACCESS_KEY_1 = 'AAAAmFvrQ0U:APA91bEuCU45pEP9eHBNJVwJcXfjxt3RzP3Yqnk-D7D53Okah35nLcv19RQFXrqG2bKKSdEvtyKMTruTtx5wJZknrlBr17uAlhL0KiEqBzZqvMhuTOUQdAppl68Z20wJJoyyxCaRulZD';

// Sends Push notification for Android users

     public static function android($data, $reg_id) {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $message = array(
                'title' => $data['msg_title'],
                'body' => $data['msg_body'],
                'type' => $data['msg_type'],
                'msgcnt' => 1,
                'vibrate' => 1,
                "sound" => "default"
            );
            $headers = array(
                 'Content-Type:application/json',
                 'Authorization:key='.self::$API_ACCESS_KEY
            );

            $fields = array(
                 'notification' => $message,
                 'registration_ids' => $reg_id,                  
                //  'to' => $reg_id,
            );
           //print_r($fields);
            return self::useCurl($url, $headers, $fields);
        }

        // Sends Push notification for iOS users
    //  function iOS($data, $reg_id) {
    //        $url = 'https://fcm.googleapis.com/fcm/send';
    //         $message = array(
    //             'title' => $data['msg_title'],
    //             'body' => $data['msg_body'],
    //             'type' => $data['msg_type'],
    //             'subtitle' => '',
    //             'tickerText' => '',
    //             'msgcnt' => 1,
    //             'vibrate' => 1
    //         );
    //         $headers = array(
    //              'Content-Type:application/json',
    //              'Authorization:key=AAAA_SySX4Y:APA91bGmyHPwrD5Gr2Ve2qvhoe27ZD-D15afgWMa_7KtiUz15NJbkMrbQVhKNgEgucL6_luyIyW3YCXkqrztGsss6XsS2JG-X7MMtPPit3llCY1WfN07t9RL7ewL6yg098gEEpFvO4CZ'
    //         );

    //         $fields = array(
    //              'notification' => $message,
    //              'to' => $reg_id,
    //         );
    //        //print_r($fields);
    //         return self::useCurl($url, $headers, $fields);
    //   }

    public static function useCurl($url, $headers, $fields) {
            $ch = curl_init();
            if ($url) {
                // Set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Disabling SSL Certificate support temporarly
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                if ($fields) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                }
                // Execute post
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($result === FALSE) {
                    die('Curl failed: ' . curl_error($ch));
                }
                // Close connection
                curl_close($ch);
                return $result;
            }
        }
}
