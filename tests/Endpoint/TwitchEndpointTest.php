<?php


namespace Darkanakin41\StreamBundle\Tests\Endpoint;


use Darkanakin41\StreamBundle\Endpoint\TwitchEndpoint;
use GuzzleHttp\Exception\GuzzleException as GuzzleExceptionAlias;

/**
 * Class TwitchEndpointTest
 * @package Darkanakin41\StreamBundle\Tests\Endpoint
 */
class TwitchEndpointTest extends AbstractEndpointTest
{

    public function testGetGame()
    {
        $streamCategory = 506103;

        $resultats = $this->getEndpoint()->getGame($streamCategory);

        $this->assertIsArray($resultats);
        $this->assertArrayHasKey('id', $resultats);
        $this->assertEquals($streamCategory, $resultats['id']);
    }

    /**
     * @return TwitchEndpoint
     */
    protected function getEndpoint()
    {
        /** @var TwitchEndpoint $service */
        $service = self::$container->get(TwitchEndpoint::class);
        return $service;
    }

    /**
     * @depends testGetGame
     */
    public function testGetGamesEmpty()
    {
        $resultats = $this->getEndpoint()->getGame(12365461);

        $this->assertIsArray($resultats);
        $this->assertEmpty($resultats);
    }

    public function testGetGameStreams()
    {
        $streamCategory = 506103;

        $resultats = $this->getEndpoint()->getGameStreams($streamCategory);

        $this->assertIsArray($resultats);
        $this->assertArrayHasKey('data', $resultats);
        $this->assertNotEmpty($resultats['data']);
    }

    /**
     * @depends testGetGameStreams
     */
    public function testGetStreams()
    {
        $streamCategory = 506103;
        $resultats = $this->getEndpoint()->getGameStreams($streamCategory);

        $this->assertIsArray($resultats);
        $this->assertArrayHasKey('data', $resultats);
        $this->assertNotEmpty($resultats['data']);

        $testUser = $resultats['data'][0]['user_id'];

        $resultats = $this->getEndpoint()->getStreams([$testUser]);

        $this->assertIsArray($resultats);
        $this->assertArrayHasKey('data', $resultats);
        $this->assertCount(1, $resultats['data']);
    }

    /**
     * @depends testGetUserDataFromUsername
     * @throws GuzzleExceptionAlias
     */
    public function testGetUsers()
    {
        $resultats = $this->getEndpoint()->getUserDataFromUsername('darkanakin41');

        $resultats = $this->getEndpoint()->getUsers([$resultats['id']]);

        $this->assertIsArray($resultats);
        $this->assertArrayHasKey('data', $resultats);
        $this->assertCount(1, $resultats['data']);
        $this->assertEquals('darkanakin41', $resultats['data']['0']['login']);
    }

    public function testGetUserDataFromUsername()
    {
        $resultats = $this->getEndpoint()->getUserDataFromUsername('darkanakin41');

        $this->assertEquals(38721257, $resultats['id']);
        $this->assertEquals('darkanakin41', $resultats['login']);
    }
}
