<?php

namespace PLejeune\MediaBundle\Extension;


use PLejeune\MediaBundle\Entity\File;
use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Nomenclature\ProviderNomenclature;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StreamExtension extends \Twig_Extension
{
    const TWITCH = '<div id="stream-%s" data-stream="%s" data-width="%d" data-height="%d"></div>';
    const TWITCH_CHAT = '<iframe src="//www.twitch.tv/embed/%s/chat" frameborder="0" scrolling="no" width="%s" height="%s"></iframe>';

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
            new \Twig_SimpleFunction('plejeune_stream_render_live', [$this, 'renderLive'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('plejeune_stream_render_chat', [$this, 'renderChat'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('plejeune_stream_language', [$this, 'language']),
            new \Twig_SimpleFunction('plejeune_stream_have_chat', [$this, 'haveChat']),
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

}