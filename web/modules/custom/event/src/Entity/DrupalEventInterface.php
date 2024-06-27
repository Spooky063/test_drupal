<?php

declare(strict_types=1);

namespace Drupal\event\Entity;

use Drupal\node\NodeInterface;

interface DrupalEventInterface extends NodeInterface
{
    public function getEventTermId(): int;

  /**
   * @return array<array-key, string>
   */
    public function getXRelatedEventExceptItself(int $term_id, int $nid, int $lengthMax): array;
}
