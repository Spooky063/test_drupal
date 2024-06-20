<?php

declare(strict_types=1);

namespace Drupal\event\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\event\Entity\DrupalEventInterface;
use Drupal\event\ValueObject\TodayDate;
use Drupal\node\NodeInterface;

final class DrupalEventStorage extends SqlContentEntityStorage implements DrupalEventStorageInterface
{

  public function getLatestEventByTermId(int $termId, int $nidExclude): array
  {
    $current_time = TodayDate::now();

    $query = $this->getQuery()
      ->accessCheck()
      ->condition('type', 'event')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('nid', $nidExclude, '<>')
      ->condition('field_event_type.target_id', $termId)
      ->condition('field_date_range.end_value', $current_time->getDateTime(), '>=')
      ->sort('created', 'ASC')
      ->range(0, 3);

    return $query->execute();
  }

  public function getLatestEvent(int $range_length, int $nidExclude): array
  {
    $current_time = TodayDate::now();

    $query = $this->getQuery()
      ->accessCheck()
      ->condition('type', 'event')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('nid', $nidExclude, '<>')
      ->condition('field_date_range.end_value', $current_time->getDateTime(), '>=')
      ->sort('created', 'ASC')
      ->range(0, $range_length);

    return $query->execute();
  }

  public function getExpiredEvents(): array
  {
    return [1041];
  }
}
