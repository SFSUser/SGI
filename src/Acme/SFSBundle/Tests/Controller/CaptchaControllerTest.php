<?php

namespace Acme\SFSBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CaptchaControllerTest extends WebTestCase
{
    public function testGenerate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'generatecaptcha');
    }

}
