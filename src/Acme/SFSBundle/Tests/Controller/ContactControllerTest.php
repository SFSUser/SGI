<?php

namespace Acme\SFSBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'contacts');
    }

    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'addcontact');
    }

}
