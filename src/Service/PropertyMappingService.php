<?php

namespace CustomPropertyMapper\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Psr\Log\LoggerInterface;

class PropertyMappingService
{
    private const PROPERTY_MAPPING = [
        '0197B18B4AB670E7AD3B7E5A3E28B5F0' => '019970C3DAF17C9AA2DC3F47F2DBA89C',
        '0197B18B8943738BAA8ACAB4FFE29C69' => '019970C39394717DA6B46273AB5FB95A',
        '0197B18B4AAD7041B06AB34A4F8929C6' => '019970C310EA7C349A9396770F702DD3',
        '0197B18B4AB272FC980C3C926A1E8A79' => '0197B18B4A7D72E1BF91331FC856FFDA',
        '0197B18B4AA5704681B2C0F7A6511F0B' => '0197B18B46AA70A794EF2108A8CBCC6A',
        '0197B18B4AA9704BB9A4ED90B1134998' => '0197B18B17DA70969DA3D89CF193E3DC',
    ];

    public function __construct(
        private readonly EntityRepository $productRepository,
        private readonly LoggerInterface $logger
    ) {}

    public function runFullSync(Context $context): int
    {
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addAssociation('properties');

        $products = $this->productRepository->search($criteria, $context);
        $count = 0;

        foreach ($products as $product) {
            $optionIds = $product->getProperties()->getIds();
            $currentTargetOptions = array_intersect($optionIds, array_values(self::PROPERTY_MAPPING));
            $matchedTargetOptions = [];

            foreach (self::PROPERTY_MAPPING as $sourceId => $targetId) {
                if (in_array($sourceId, $optionIds, true)) {
                    $matchedTargetOptions[] = $targetId;
                }
            }

            $updatedOptionIds = array_unique(array_merge(
                array_diff($optionIds, $currentTargetOptions),
                $matchedTargetOptions
            ));

            if ($updatedOptionIds !== $optionIds) {
                $this->productRepository->update([
                    [
                        'id' => $product->getId(),
                        'properties' => array_map(fn($id) => ['id' => $id], $updatedOptionIds),
                    ]
                ], $context);
                $count++;
            }
        }

        return $count;
    }
}
