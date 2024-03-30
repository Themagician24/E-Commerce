<?php

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {

        /**1. Creer un faux client (navigateur) de pointer vers une URL;
         * 2. Remplir les champs de mon formulaire d'inscription;
         * 3. Est-ce que tu peux regarder si dans ma page j'ai le message (alerte)suivante:Votre compte est correctement
         * creer, veuillez vous connecter.
         */
//1 Creation du client
        $client = static::createClient();
        $client->request('GET', '/inscription');
//Remplissage du formulaire
        $client->submitForm('Validez', [
            'register_user [email]' => 'ada@gmail.com',
            'register_user [plainPassword][first]' => '123456',
            'register_user [plainPassword][second]' => '123456',
            'register_user [firstname]' => 'Julie',
            'register_user [lastname]' => 'Kubik'
        ]);

        //Follow
        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();
          //3.Verification du message flash
        $this->assertSelectorExists('div:contains("Votre compte est correctement crée,veuillez vous connecter.")');
        
    }
}
