<?php
namespace CustomPropertyMapper\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class FrequentPropertyCheckTask extends ScheduledTask
{
public static function getTaskName(): string
{
return 'custom_property_mapper.frequent_check';
}

public static function getDefaultInterval(): int
{
return 360; // alle 2 Stunden
}
}
