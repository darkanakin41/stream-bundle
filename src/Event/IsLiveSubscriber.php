<?php


namespace Darkanakin41\StreamBundle\Event;


use DateTime;
use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Service\StreamService;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IsLiveSubscriber implements EventSubscriberInterface
{
    /**
     * @var StreamService
     */
    private $streamService;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry, StreamService $streamService)
    {
        $this->streamService = $streamService;
        $this->registry = $registry;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            IsLiveEvent::NAME => "isLiveHandler",
        ];
    }

    public function isLiveHandler(IsLiveEvent $event)
    {
        $stream = $this->registry->getRepository(Stream::class)->findOneBy(array(
            'identifier' => $event->getIdentifier(),
            'platform' => $event->getPlatform(),
        ));

        if ($stream === null) {
            $stream = new Stream();
            $stream->setName($event->getName());
            $stream->setLogo($event->getLogo());
            $stream->setIdentifier($event->getIdentifier());
            $stream->setPlatform($event->getPlatform());
            $stream->setStatus(StatusNomenclature::ONLINE);
            $stream->setUpdated(new DateTime());
            $stream->setHighlighted(false);
            $stream->setTags([]);

            $this->registry->getManager()->persist($stream);
            $this->registry->getManager()->flush();
        }

        $this->streamService->refresh([$stream], $stream->getPlatform());
    }
}
