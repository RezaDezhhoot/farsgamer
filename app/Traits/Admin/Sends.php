<?php

namespace App\Traits\Admin;
use GuzzleHttp\Client;
use App\Models\Notification;

trait Sends{
    private $baseUri = 'https://ws.sms.ir/api/';
    private $apiKey = 'V-qyDXKy60ZVeni8h-HAl6Qtz2vXP4Keenc0EN6k3LQ=';
    private $username = '09931788937';
    private $password = 'faraz0670834696';
    private $lineNumber = '3000505';

    public function sends($text , $model)
    {
        if (!is_null($text)) {
            foreach ($text as $key => $value) {
                switch ($key)
                {
                    case 'sms':{
                        foreach ($value as $item)
                            $this->sendSMS($item,$model->phone);
                        break;
                    }
                    case 'email':{
                        foreach ($value as $item)
                            $this->sendEMAIL($item,$model->email);
                        break;
                    }
                    case 'notification':{
                        foreach ($value as $item)
                            $this->sendNOTIFICATION($item,$model->id);
                        break;
                    }
                }
            }
        }
    }

    public function sendSMS($message , $number)
    {

        if (app()->environment('local')) {
            return(false);
        }

        try {
            $client = new Client();
            $query = ['from' => $this->lineNumber, 'to' => $number, 'msg' => $message,
                'uname' => $this->username, 'pass' => $this->password];
            $result = $client->get('http://ippanel.com/class/sms/webservice/send_url.php', [
                'query' => $query,
            ]);
            return json_decode($result->getBody(), true);
        } catch (\Exception $exception) {
            echo "ERROR";
        }
    }

    public function sendEMAIL($text , $email)
    {

    }

    public function sendNOTIFICATION($text , $id)
    {
        $notification = new Notification();
        $notification->content = $text;
        $notification->user_id = $id;
        $notification->type = Notification::PRIVATE;
        $notification->save();
    }
}
