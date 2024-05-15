<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    // Fonction pour envoyer un e-mail
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        // Récupération du contenu du template
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

        // Remplacement des variables dans le contenu du template
        if ($vars) {
            foreach($vars as $key=>$var) {
                $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

        // Initialisation du client Mailjet avec les clés API
        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);
        
        // Création du corps de l'e-mail à envoyer
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "jeffreydis10@gmail.com",
                        'Name' => "La Boutique Africaine"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 5552872, // ID du template Mailjet à utiliser
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content // Contenu du message à envoyer
                    ]
                ]
            ]
        ];

        // Envoi de l'e-mail en utilisant l'API Mailjet
        $mj->post(Resources::$Email, ['body' => $body]);
    }
}