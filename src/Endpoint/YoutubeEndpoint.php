<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Endpoint;

use Google_Client;
use Google_Service_YouTube_Resource_Videos;
use Google_Service_YouTube_VideoListResponse;

class YoutubeEndpoint extends AbstractEndpoint
{
    /** @var Google_Client */
    private $client;

    /**
     * Retrieve all data from the given video ids.
     *
     * @param string[] $channel_id id of channels to update
     *
     * @return Google_Service_YouTube_VideoListResponse
     */
    public function getVideosData(array $channel_id, $maxResults = 50)
    {
        $api = $this->getYoutubeVideosAPI();

        $search = implode(',', $channel_id);

        return $api->listVideos('id,snippet,liveStreamingDetails', array(
            'maxResults' => $maxResults,
            'id' => $search,
        ));
    }

    protected function initialize()
    {
        $clientId = $this->getParameterBag()->get('darkanakin41.stream.google.clientId');
        $clientSecret = $this->getParameterBag()->get('darkanakin41.stream.twitch.clientSecret');
        $this->client = new Google_Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
    }

    /**
     * Retrieve the Youtube Videos API.
     *
     * @return Google_Service_YouTube_Resource_Videos
     */
    protected function getYoutubeVideosAPI()
    {
        $service = new \Google_Service_YouTube($this->client);

        return $service->videos;
    }
}
