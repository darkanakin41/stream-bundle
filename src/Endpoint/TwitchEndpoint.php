<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Endpoint;

use GuzzleHttp\Exception\GuzzleException;
use NewTwitchApi\HelixGuzzleClient;
use NewTwitchApi\NewTwitchApi;

class TwitchEndpoint extends AbstractEndpoint
{
    /** @var HelixGuzzleClient */
    private $client;

    /**
     * @var NewTwitchApi
     */
    private $api;

    /**
     * Retrieve streams from the choosen category.
     *
     * @param int $identifier
     * @param     $cursor
     *
     * @return array
     *
     * @throws GuzzleException
     */
    public function getGameStreams($identifier, $cursor = null)
    {
        $data = $this->api->getStreamsApi()->getStreams(array(), array(), array($identifier), array(), array(), 100, $cursor, null);

        return json_decode($data->getBody()->getContents(), true);
    }

    /**
     * Retrieve streams from the given user_login.
     *
     * @param string $cursor the page
     *
     * @return array
     *
     * @throws GuzzleException
     */
    public function getStreams(array $userIds, $cursor = null)
    {
        $data = $this->api->getStreamsApi()->getStreams($userIds, array(), array(), array(), array(), 100, null, $cursor);

        return json_decode($data->getBody()->getContents(), true);
    }

    /**
     * Retrieve data for the given game.
     *
     * @param int $gameId the game id to retrieve data from
     *
     * @return array
     *
     * @throws GuzzleException
     */
    public function getGame($gameId)
    {
        $data = $this->api->getGamesApi()->getGames(array($gameId));
        $arrayData = json_decode($data->getBody()->getContents(), true);
        if (!isset($arrayData['data']) || 0 === count($arrayData['data'])) {
            return array();
        }

        return $arrayData['data'][0];
    }

    /**
     * Retrieve streams from the given user_login.
     *
     * @return array
     *
     * @throws GuzzleException
     */
    public function getUsers(array $userIds)
    {
        $data = $this->api->getUsersApi()->getUsers($userIds, array(), false, null);

        return json_decode($data->getBody()->getContents(), true);
    }

    /**
     * Retrieve the user_id from username.
     *
     * @return array|null
     *
     * @throws GuzzleException
     */
    public function getUserDataFromUsername(string $userName)
    {
        $userData = null;
        $rawData = $this->api->getUsersApi()->getUsers(array(), array($userName), false, null);
        $data = json_decode($rawData->getBody()->getContents(), true);
        if (isset($data['data']) && isset($data['data'][0])) {
            $userData = $data['data'][0];
        }

        return $userData;
    }

    protected function initialize()
    {
        $config = $this->getConfig();
        $clientId = $config['platform']['twitch']['client_id'];
        $clientSecret = $config['platform']['twitch']['client_secret'];
        $this->client = new HelixGuzzleClient($clientId);

        $this->api = new NewTwitchApi($this->client, $clientId, $clientSecret);
    }
}
