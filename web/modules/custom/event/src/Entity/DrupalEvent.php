<?php

declare(strict_types=1);

namespace Drupal\event\Entity;

use Drupal\event\Storage\DrupalEventStorageInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;

final class DrupalEvent extends Node implements DrupalEventInterface
{
    public const BUNDLE = 'event';

    public function getEventTermId(): int
    {
        /** @var EntityReferenceFieldItemList $event_type_list */
        $event_type_list = $this->get('field_event_type');
        /** @var TermInterface[] $event_term_list */
        $event_term_list = $event_type_list->referencedEntities();

        return (int) $event_term_list[0]->id();
    }

    public function getXRelatedEventExceptItself(int $term_id, int $nid, int $lengthMax): array
    {
        /** @var DrupalEventStorageInterface $eventStorage */
        $eventStorage = $this->entityTypeManager()->getStorage('node');
        return $eventStorage->getLatestEventByTermId($term_id, $nid, 3);
    }

    public function getCacheTagsToInvalidate()
    {
        $tagToInvalidate = ['node_list:event', 'event_related'];
        return [...parent::getCacheTagsToInvalidate(), ...$tagToInvalidate];
    }
}
