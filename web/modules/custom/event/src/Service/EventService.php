<?php

declare(strict_types=1);

namespace Drupal\event\Service;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\event\Storage\DrupalEventStorageInterface;
use Drupal\event\ValueObject\TodayDate;
use Drupal\node\NodeInterface;

final class EventService
{
  public const EVENT_WORKER_UNPUBLISHED_NAME = 'event_unpublish_queue_worker';

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected QueueFactory $queue,
  ) {
  }

  public function addExpiredEventsToQueue(): void
  {
    $queue = $this->queue->get(self::EVENT_WORKER_UNPUBLISHED_NAME);

    /** @var DrupalEventStorageInterface $eventStorage */
    $eventStorage = $this->entityTypeManager->getStorage('node');
    $expiredEvents = $eventStorage->getExpiredEvents();

    array_map(fn(int $nid) => $queue->createItem($nid), $expiredEvents);
  }
}
