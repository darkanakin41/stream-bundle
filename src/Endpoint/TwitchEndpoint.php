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
     * @param $identifier
     * @param $cursor
     *
     * @return array
     */
    public function getGameStreams($identifier, $cursor = null)
    {
        try {
            $data = $this->api->getStreamsApi()->getStreams(array(), array(), array($identifier), array(), array(), 100, null, $cursor);

            return json_decode($data, true);
        } catch (GuzzleException $e) {
            return array();
        }
    }

    /**
     * Retrieve streams from the given user_login.
     *
     * @param array  $userLogins
     * @param string $cursor     the page
     *
     * @return array
     */
    public function getStreams(array $userLogins, $cursor = null)
    {
        try {
            $data = $this->api->getStreamsApi()->getStreams($userLogins, array(), array(), array(), array(), 100, null, $cursor);

            return json_decode($data, true);
        } catch (GuzzleException $e) {
            return array();
        }
    }

    /**
     * Retrieve data for the given game.
     *
     * @param int $gameId the game id to retrieve data from
     *
     * @return array
     */
    public function getGame($gameId)
    {
        try {
            $data = $this->api->getGamesApi()->getGames(array($gameId));
            $arrayData = json_decode($data, true);
            if (!isset($arrayData['data']) || 0 === count($arrayData['data'])) {
                return array();
            }

            return $arrayData['data'][0];
        } catch (GuzzleException $e) {
            return array();
        }
    }

    /**
     * Retrieve streams from the given user_login.
     *
     * @param array  $userLogins
     * @param string $cursor     the page
     *
     * @return array
     */
    public function getUsers(array $userLogins)
    {
        try {
            $data = $this->api->getUsersApi()->getUsers($userLogins, array(), false, null);

            return json_decode($data, true);
        } catch (GuzzleException $e) {
            return array();
        }
    }

    protected function initialize()
    {
        $clientId = $this->getParameterBag()->get('darkanakin41.stream.twitch.clientId');
        $clientSecret = $this->getParameterBag()->get('darkanakin41.stream.twitch.clientSecret');
        $this->client = new HelixGuzzleClient($clientId);

        $this->api = new NewTwitchApi($this->client, $clientId, $clientSecret);
    }
}
