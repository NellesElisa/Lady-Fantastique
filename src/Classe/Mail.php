<?php
//pour utiliser mailjet
namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail{
    private $api_key = 'e43fda9285efadee0d7ec2be16b3380a';
    private $api_key_secret ='7720fdb2779b69d5ee0ba06242660040';

    public function send($to_email,$to_name,$subject,$content){
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version'=>'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "maurinecouture@gmail.com",
                        'Name' => "Lady Fantastique"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name,
                        ]
                    ],
                    'TemplateID' => 3375683,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                   'Variables' => [
                       "content" => $content,
                   ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}