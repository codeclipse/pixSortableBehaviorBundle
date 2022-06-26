<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\Services;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class PositionHandler
{
    /** From config */
    protected array $positionField = [];

    /** From config */
    private array $sortableGroups = [];

    private ?PropertyAccessor $accessor = null;

    private function getAccessor(): PropertyAccessor
    {
        return $this->accessor
            ?? $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    abstract public function getLastPosition(object $entity): int;

    public function setPositionField(array $positionField): void
    {
        $this->positionField = $positionField;
    }

    public function setSortableGroups(array $sortableGroups): void
    {
        $this->sortableGroups = $sortableGroups;
    }

    public function getPositionFieldByEntity(object|string $entity): string
    {
        if (is_object($entity)) {
            $entity = ClassUtils::getClass($entity);
        }

        return $this->positionField['entities'][$entity]
            ?? $this->positionField['default'];
    }

    public function getSortableGroupsFieldByEntity(object|string $entity): array
    {
        if (is_object($entity)) {
            $entity = ClassUtils::getClass($entity);
        }

        return $this->sortableGroups['entities'][$entity]
            ?? [];
    }

    public function getCurrentPosition(object $entity): int
    {
        return $this->getAccessor()->getValue($entity, $this->getPositionFieldByEntity($entity));
    }

    public function getPosition(object $object, string|int $movePosition, int $lastPosition): int
    {
        $currentPosition = $this->getCurrentPosition($object);

        return match($movePosition) {
            'up' => $currentPosition > 0 ? $currentPosition - 1 : 0,
            'down' => $currentPosition < $lastPosition ? $currentPosition + 1 : 0,
            'top' => 0,
            'bottom' => $lastPosition,
            default => is_numeric($movePosition) ? (int) $movePosition : 0,
        };
    }
}
