<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\Services;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\MongoDB\DocumentManager;

class PositionODMHandler extends PositionHandler
{
    public function __construct(protected DocumentManager $dm)
    {
    }

    public function getLastPosition(object $entity): int
    {
        $entityClass = ClassUtils::getClass($entity);
        $parentEntityClass = true;
        while ($parentEntityClass)
        {
            $parentEntityClass = ClassUtils::getParentClass($entityClass);
            if ($parentEntityClass) {
                $reflection = new \ReflectionClass($parentEntityClass);
                if($reflection->isAbstract()) {
                    break;
                }
                $entityClass = $parentEntityClass;
            }
        }

        $positionFields = $this->getPositionFieldByEntity($entityClass);
        $result = $this->dm
            ->createQueryBuilder($entityClass)
            ->hydrate(false)
            ->select($positionFields)
            ->sort($positionFields, 'desc')
            ->limit(1)
            ->getQuery()
            ->getSingleResult();

        if (is_array($result) && isset($result[$positionFields])) {
            return $result[$positionFields];
        }

        return 0;
    }
}
