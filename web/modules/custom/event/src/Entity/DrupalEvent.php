<?php

declare(strict_types=1);

namespace Drupal\event\Entity;

use Drupal\event\Storage\DrupalEventStorageInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;

final class DrupalEvent extends Node implements DrupalEventInterface
{
  public function getEventTermId(): int
  {
    /** @var EntityReferenceFieldItemList $event_type_list */
    $event_type_list = $this->get('field_event_type');
    /** @var TermInterface[] $event_term_list */
    $event_term_list = $event_type_list->referencedEntities();

    return (int) $event_term_list[0]->id();
  }

  public function getRelatedEventByTermIdExceptItself(int $term_id, int $nid): array
  {
    /** @var DrupalEventStorageInterface $eventStorage */
    $eventStorage = $this->entityTypeManager()->getStorage('node');
    return $eventStorage->getLatestEventByTermId($term_id, $nid);
  }

  public function getXRelatedEventExceptItself(int $term_id, int $nid, int $length_to_reach): array
  {
    /** @var DrupalEventStorageInterface $eventStorage */
    $eventStorage = $this->entityTypeManager()->getStorage('node');

    $events = $this->getRelatedEventByTermIdExceptItself($term_id, $nid);

    $number_remaining = $length_to_reach - \count($events);
    if ($number_remaining < $length_to_reach) {
      $events = [...$events, ...$eventStorage->getLatestEvent($number_remaining, $nid)];
    }

    return $events;
  }

  public function setUnpublished(): void
  {
    $this->set('status', NodeInterface::NOT_PUBLISHED);
  }
}
