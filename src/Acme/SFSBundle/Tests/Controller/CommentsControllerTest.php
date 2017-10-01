<?php

namespace Acme\SFSBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentsControllerTest extends WebTestCase
{
    public function testComment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/comment');
    }

}
