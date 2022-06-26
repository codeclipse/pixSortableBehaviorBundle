<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\Twig;

use Codeclipse\SortableBehaviorBundle\Services\PositionHandler;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Description of ObjectPositionExtension
 *
 * @author Volker von Hoesslin <volker.von.hoesslin@empora.com>
 */
class SortableBehaviorExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private ?PositionHandler $positionHandler = null;

    public static function getSubscribedServices(): array
    {
        return [
            PositionHandler::class,
        ];
    }

    public function __construct(protected ContainerInterface $container)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sort_current_object_position', [$this, 'currentPosition']),
            new TwigFunction('sort_last_position', [$this, 'lastPosition'])
        ];
    }

    public function currentPosition($entity): int
    {
        return $this->getPositionHandler()->getCurrentPosition($entity);
    }

    public function lastPosition($entity): int
    {
        return $this->getPositionHandler()->getLastPosition($entity);
    }

    protected function getPositionHandler(): PositionHandler
    {
        return $this->positionHandler
            ?? $this->positionHandler = $this->container->get(PositionHandler::class);
    }
}
