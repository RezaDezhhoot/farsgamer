<?php


namespace App\Sends;


use App\Models\Notification;
use GuzzleHttp\Client;

class SendMessages
{
    private $baseUri = 'https://ws.sms.ir/api/';
    private $apiKey = 'V-qyDXKy60ZVeni8h-HAl6Qtz2vXP4Keenc0EN6k3LQ=';
    private $username = '09931788937';
    private $password = 'faraz0670834696';
    private $lineNumber = '3000505';

    public function sends($text , $model , $subject = Notification::ALL , $model_id = null)
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
                            $this->sendEMAIL($item,$model->email , $subject);
                        break;
                    }
                    case 'notification':{
                        foreach ($value as $item)
                            $this->sendNOTIFICATION($item,$model->id , $subject , $model_id);
                        break;
                    }
                    case 'sms_email':{
                        foreach ($value as $item){
                            $this->sendSMS($item,$model->phone);
                            $this->sendEMAIL($item,$model->email , $subject);
                        }
                        break;
                    }
                    case 'sms_notification':{
                        foreach ($value as $item){
                            $this->sendSMS($item,$model->phone);
                            $this->sendNOTIFICATION($item,$model->id , $subject , $model_id);
                        }
                        break;
                    }
                    case 'email_notification':{
                        foreach ($value as $item){
                            $this->sendEMAIL($item,$model->email , $subject);
                            $this->sendNOTIFICATION($item,$model->id , $subject , $model_id);
                        }
                        break;
                    }
                    case 'sms_email_notification':{
                        foreach ($value as $item){
                            $this->sendSMS($item,$model->phone);
                            $this->sendEMAIL($item,$model->email , $subject);
                            $this->sendNOTIFICATION($item,$model->id , $subject , $model_id);
                        }
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

    public function sendEMAIL($text , $email , $subject = '')
    {
        mail($email,$subject,$text);
    }

    public function sendNOTIFICATION($text , $id , $subject , $model_id)
    {
        $notification = new Notification();
        $notification->subject = $subject;
        $notification->content = $text;
        $notification->user_id = $id;
        $notification->model = $subject;
        $notification->model_id = $model_id;
        $notification->type = Notification::PRIVATE;
        $notification->save();
    }
}
