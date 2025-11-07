<?php

namespace CustomPropertyMapper\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use CustomPropertyMapper\Service\PropertyMappingService;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class PropertyMappingController extends AbstractController
{
    public function __construct(
        private readonly PropertyMappingService $mappingService
    ) {}

    /**
     * @Route("/api/_action/property-mapper/run", name="api.custom_property_mapper.run", methods={"POST"})
     */
    public function runMapping(Request $request): JsonResponse
    {
        $context = Context::createDefaultContext();
        $updated = $this->mappingService->runFullSync($context);

        return new JsonResponse([
            'status' => 'success',
            'updated' => $updated
        ]);
    }
}
