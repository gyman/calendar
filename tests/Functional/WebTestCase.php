<?php

namespace Test\Functional;

use App\Table;
use Doctrine\ORM\EntityManagerInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\SchemaTool;

class WebTestCase extends BaseWebTestCase
{
    /** @var Client */
    protected $client;

    public function setUp()
    {
        if($this->client === null) {
            $this->client = $this->createClient();
        }

        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine')->getManager();

        $metaData = $em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($em);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);

        $inMemoryEventStore = $this->get(InMemoryEventStore::class);

        $inMemoryEventStore->create(new Stream(new StreamName(Table::EVENT_STREAM), new \ArrayIterator([])));
    }

    public function get(string $service) : object
    {
        return $this->client->getContainer()->get('test.service_container')->get($service);
    }


    public function getParameter(string $parameter) : string
    {
        return $this->client->getContainer()->get('test.service_container')->getParameter($parameter);
    }

    protected function assertJsonResponse(Response $response)
    {
        $this->assertSame('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }
}