<?php

declare(strict_types=1);

namespace Drupal\event\Entity;

interface DrupalEventInterface
{
  public function getEventTermId(): int;

  /**
   * @return array<array-key, DrupalEventInterface>
   */
  public function getRelatedEventByTermIdExceptItself(int $term_id, int $nid): array;

  /**
   * @return array<array-key, DrupalEventInterface>
   */
  public function getXRelatedEventExceptItself(int $term_id, int $nid, int $length_to_reach): array;

  public function setUnpublished(): void;
}
