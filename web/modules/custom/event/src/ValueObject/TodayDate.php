<?php

declare(strict_types=1);

namespace Drupal\event\ValueObject;

use Drupal\Core\Datetime\DrupalDateTime;

final class TodayDate
{
  public function __construct(protected \DateTimeImmutable $dateTime) {
  }

  public static function now(): self
  {
    return new self(new \DateTimeImmutable());
  }

  public function getDateTime(): int
  {
    return $this->dateTime->getTimestamp();
  }
}
