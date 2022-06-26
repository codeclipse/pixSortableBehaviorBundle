<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\Controller;

use Codeclipse\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class SortableAdminController
 *
 * @package Codeclipse\SortableBehaviorBundle
 */
class SortableAdminController extends CRUDController
{
    /**
     * Move element
     *
     * @param string $position
     *
     * @return RedirectResponse|Response
     */
    public function moveAction(Request $request): Response
    {
        $object = $this->assertObjectExists($request, true);
        \assert(null !== $object);

        $this->admin->checkAccess('edit', $object);

        /** @var PositionHandler $positionHandler */
        $positionHandler = $this->get(PositionHandler::class);

        $position = $request->get('position');

        $lastPositionNumber = $positionHandler->getLastPosition($object);
        $newPositionNumber  = $positionHandler->getPosition($object, $position, $lastPositionNumber);

        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($object, $positionHandler->getPositionFieldByEntity($object), $newPositionNumber);

        $this->admin->update($object);

        if ($this->isXmlHttpRequest($request)) {
            return $this->handleXmlHttpRequestSuccessResponse($request, $object);
        }

        $this->addFlash(
            'sonata_flash_success',
            $this->trans('flash_success_position_updated', [], 'messages'),
        );

        return $this->redirectToList();
    }
}
