<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\Services;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;

class PositionORMHandler extends PositionHandler
{
    private static array $cacheLastPosition = [];

    public function __construct(protected EntityManagerInterface $em)
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
                $reflection = new ReflectionClass($parentEntityClass);
                if($reflection->isAbstract()) {
                    break;
                }
                $entityClass = $parentEntityClass;
            }
        }

        $groups = $this->getSortableGroupsFieldByEntity($entityClass);

        $cacheKey = $this->getCacheKeyForLastPosition($entity, $groups);
        if (!isset(self::$cacheLastPosition[$cacheKey])) {
            $qb = $this->em->createQueryBuilder()
                ->select(sprintf('MAX(t.%s) as last_position', $this->getPositionFieldByEntity($entityClass)))
                ->from($entityClass, 't')
            ;

            if ($groups) {
                $i = 1;
                foreach ($groups as $groupName) {
                    $getter = 'get' . $groupName;

                    if ($entity->$getter()) {
                        $qb
                            ->andWhere(sprintf('t.%s = :group_%s', $groupName, $i))
                            ->setParameter(sprintf('group_%s', $i), $entity->$getter())
                        ;
                        $i++;
                    }
                }
            }

            self::$cacheLastPosition[$cacheKey] = (int) $qb->getQuery()->getSingleScalarResult();
        }

        return self::$cacheLastPosition[$cacheKey];
    }

    private function getCacheKeyForLastPosition(object $entity, array $groups): string
    {
        $cacheKey = ClassUtils::getClass($entity);

        foreach ($groups as $groupName) {
            $getter = 'get' . $groupName;

            if ($entity->$getter()) {
                $cacheKey .= '_' . $entity->$getter()->getId();
            }
        }

        return $cacheKey;
    }
}
