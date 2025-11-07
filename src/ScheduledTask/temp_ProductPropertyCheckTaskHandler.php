<?php
namespace CustomPropertyMapper\ScheduledTask;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class tempProductPropertyCheckTaskHandler extends ScheduledTaskHandler
{
public static function getHandledMessages(): iterable
{
return [
FrequentPropertyCheckTask::class,
FullPropertyCheckTask::class
];
}

private const LAST_RUN_CONFIG_KEY_FREQUENT = 'CustomPropertyMapper.config.lastRunFrequent';

private const PROPERTY_MAPPING = [
'0197B18B4AB670E7AD3B7E5A3E28B5F0' => '019970C3DAF17C9AA2DC3F47F2DBA89C',
'0197B18B8943738BAA8ACAB4FFE29C69' => '019970C39394717DA6B46273AB5FB95A',
'0197B18B4AAD7041B06AB34A4F8929C6' => '019970C310EA7C349A9396770F702DD3',
'0197B18B4AB272FC980C3C926A1E8A79' => '0197B18B4A7D72E1BF91331FC856FFDA',
'0197B18B4AA5704681B2C0F7A6511F0B' => '0197B18B46AA70A794EF2108A8CBCC6A',
'0197B18B4AA9704BB9A4ED90B1134998' => '0197B18B17DA70969DA3D89CF193E3DC',
];

public function __construct(
#[Autowire(service: 'product.repository')]
private readonly EntityRepository $productRepository,
#[Autowire(service: 'system_config.service')]
private readonly SystemConfigService $systemConfigService
) {
parent::__construct();
}

public function run(): void
{
$context = Context::createDefaultContext();
$task = $this->getTask();

$criteria = new Criteria();
$criteria->addAssociation('properties');

if ($task instanceof FrequentPropertyCheckTask) {
$lastRun = $this->getLastRunTimestamp();
$criteria->addFilter(new OrFilter([
new RangeFilter('createdAt', [RangeFilter::GT => $lastRun]),
new RangeFilter('updatedAt', [RangeFilter::GT => $lastRun]),
]));
$this->storeLastRunTimestamp();
}
// Full check → keine Filter nötig

$products = $this->productRepository->search($criteria, $context);

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
}
}
}

private function getLastRunTimestamp(): string
{
return $this->systemConfigService->get(self::LAST_RUN_CONFIG_KEY_FREQUENT)
?? (new \DateTime('-1 day'))->format(DATE_ATOM);
}

private function storeLastRunTimestamp(): void
{
$this->systemConfigService->set(self::LAST_RUN_CONFIG_KEY_FREQUENT, (new \DateTime())->format(DATE_ATOM));
}
}
