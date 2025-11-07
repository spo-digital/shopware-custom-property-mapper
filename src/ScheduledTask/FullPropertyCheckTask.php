<?php
namespace CustomPropertyMapper\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class FullPropertyCheckTask extends ScheduledTask
{
public static function getTaskName(): string
{
return 'custom_property_mapper.full_check';
}

public static function getDefaultInterval(): int
{
return 3600; // alle 24 Stunden
}
}
