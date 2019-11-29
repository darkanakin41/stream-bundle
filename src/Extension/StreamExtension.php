<?php

namespace Darkanakin41\StreamBundle\Extension;


use Darkanakin41\MediaBundle\Entity\File;
use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Nomenclature\ProviderNomenclature;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StreamExtension extends \Twig_Extension
{
    const TWITCH = '<div id="stream-%s" data-stream="%s" data-width="%d" data-height="%d"></div>';
    const TWITCH_CHAT = '<iframe src="//www.twitch.tv/embed/%s/chat" frameborder="0" scrolling="no" width="%s" height="%s"></iframe>';

    const YOUTUBE = '<iframe src="//www.youtube.com/embed/%s" id="live_embed_player_flash" allowfullscreen width="%d" height="%d" frameborder="0"></iframe>';

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->twig = $container->get('twig');
    }

    public function getFilters()
    {
        return array();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('darkanakin41_stream_render_live', [$this, 'renderLive'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('darkanakin41_stream_render_chat', [$this, 'renderChat'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('darkanakin41_stream_language', [$this, 'language']),
            new \Twig_SimpleFunction('darkanakin41_stream_have_chat', [$this, 'haveChat']),
            new \Twig_SimpleFunction('darkanakin41_stream_preview', [$this, 'preview']),
        );
    }

    /**
     * Get the HTML code to display the stream
     *
     * @param Stream $stream
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function renderLive(Stream $stream, $width = 620, $height = 380)
    {
        switch ($stream->getPlatform()) {
            case ProviderNomenclature::TWITCH :
                return sprintf(self::TWITCH, $stream->getIdentifier(), $stream->getIdentifier(), $width, $height);
            case ProviderNomenclature::YOUTUBE :
                return sprintf(self::YOUTUBE, $stream->getIdentifier(), $width, $height);
        }
    }


    /**
     * Get the HTML code to render the chat
     *
     * @param Stream $stream
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function renderChat(Stream $stream, $width = 340, $height = 380)
    {
        switch ($stream->getPlatform()) {
            case ProviderNomenclature::TWITCH :
                printf(self::TWITCH_CHAT, $stream->getIdentifier(), $width, $height);
                break;
        }
    }

    /**
     * Check if the stream have chat
     *
     * @param Stream $stream
     *
     * @return boolean
     */
    public function haveChat(Stream $stream)
    {
        switch ($stream->getPlatform()) {
            case ProviderNomenclature::TWITCH :
                return true;
        }
        return false;
    }

    /**
     * Get the lang of the stream
     *
     * @param Stream $stream
     *
     * @return string
     */
    public function language(Stream $stream)
    {
        switch ($stream->getLanguage()) {
            default :
                return strtolower($stream->getLanguage());
            case "en" :
                return "gb";
            case "ko" :
                return "kr";
            case "zh" :
                return "cn";
        }
    }

    /**
     * Get the lang of the stream
     *
     * @param Stream $stream
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function preview(Stream $stream, $width = 620, $height = 380)
    {
        return str_ireplace(['{width}','{height}'], [$width, $height], $stream->getPreview());
    }

}