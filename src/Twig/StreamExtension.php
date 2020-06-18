<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Twig;

use Darkanakin41\StreamBundle\Model\Stream;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StreamExtension extends AbstractExtension
{
    const LANGUAGE_MAPPING = array(
        'en' => 'gb',
        'ko' => 'kr',
        'zh' => 'cn',
    );

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ContainerInterface $container, Environment $twig, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    public function getFilters()
    {
        return array();
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('darkanakin41_stream_render_video', array($this, 'renderVideo'), array('is_safe' => array('html'))),
            new TwigFunction('darkanakin41_stream_render_chat', array($this, 'renderChat'), array('is_safe' => array('html'))),
            new TwigFunction('darkanakin41_stream_language', array($this, 'language')),
            new TwigFunction('darkanakin41_stream_has_chat', array($this, 'hasChat')),
            new TwigFunction('darkanakin41_stream_preview', array($this, 'preview')),
        );
    }

    /**
     * Get the HTML code to display the stream.
     *
     * @return string
     *
     * @throws Throwable
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderVideo(Stream $stream, array $vars = array())
    {
        try {
            $template = $this->twig->load(sprintf('@Darkanakin41Stream/%s.html.twig', $stream->getPlatform()));

            return $template->renderBlock('stream', array_merge($vars, array('stream' => $stream, 'width' => 620, 'height' => 380, 'volume' => '0')));
        } catch (LoaderError $e) {
        }

        return '';
    }

    /**
     * Get the HTML code to render the chat.
     *
     * @return string
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Throwable
     */
    public function renderChat(Stream $stream, array $vars = array())
    {
        try {
            $template = $this->twig->load(sprintf('@Darkanakin41Stream/%s.html.twig', $stream->getPlatform()));
            if ($template->hasBlock('chat')) {
                return $template->renderBlock('chat', array_merge($vars, array('stream' => $stream, 'width' => 340, 'height' => 380, 'referer' => $this->requestStack->getCurrentRequest()->getHttpHost())));
            }
        } catch (LoaderError $e) {
        }

        return '';
    }

    /**
     * Check if the stream have chat.
     *
     * @return bool
     *
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function hasChat(Stream $stream)
    {
        try {
            $template = $this->twig->load(sprintf('@Darkanakin41Stream/%s.html.twig', $stream->getPlatform()));

            return $template->hasBlock('chat');
        } catch (LoaderError $e) {
        }

        return false;
    }

    /**
     * Get the lang of the stream.
     *
     * @return string
     */
    public function language(Stream $stream)
    {
        if (isset(self::LANGUAGE_MAPPING[strtolower($stream->getLanguage())])) {
            return self::LANGUAGE_MAPPING[strtolower($stream->getLanguage())];
        }

        return strtolower($stream->getLanguage());
    }

    /**
     * Get the lang of the stream.
     *
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function preview(Stream $stream, $width = 620, $height = 380)
    {
        return str_ireplace(array('{width}', '{height}'), array($width, $height), $stream->getPreview());
    }
}
