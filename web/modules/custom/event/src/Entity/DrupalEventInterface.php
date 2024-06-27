<?php

declare(strict_types=1);

namespace Drupal\event\Entity;

interface DrupalEventInterface
{
  public function getEventTermId(): int;

  /**
   * @return array<array-key, DrupalEventInterface>
   */
  public function getXRelatedEventExceptItself(int $term_id, int $nid, int $lengthMax): array;

  public function setUnpublished(): void;
}
