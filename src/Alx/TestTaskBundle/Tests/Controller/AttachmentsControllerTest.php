<?php

namespace Alx\TestTaskBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AttachmentsControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/documents/{document_id}/attachments');
    }

    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/documents/{document_id}/attachments');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/attachments/{id}');
    }

    public function testReorder()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/attachments/{id}/reorder');
    }

}
