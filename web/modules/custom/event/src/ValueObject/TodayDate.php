<?php

declare(strict_types=1);

namespace Drupal\event\ValueObject;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

final class TodayDate
{
  public function __construct(protected \DateTime $dateTime) {
  }

  public static function now(): self
  {
    return new self(new \DateTime('now', new \DateTimezone('GMT')));
  }

  public function getDateTime(): int
  {
    return $this->dateTime->getTimestamp();
  }

  public function formatForDatabase(): string
  {
    $date = DrupalDateTime::createFromDateTime($this->dateTime);
    return $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
  }
}
