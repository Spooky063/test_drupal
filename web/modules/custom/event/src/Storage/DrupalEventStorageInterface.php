<?php

declare(strict_types=1);

namespace Drupal\event\Storage;

interface DrupalEventStorageInterface
{
  /**
   * @return array<array-key, string>
   */
  public function getLatestEventByTermId(int $termId, int $nidExclude): array;

  /**
   * @return array<array-key, string>
   */
  public function getLatestEvent(int $range_length, int $nidExclude): array;
}
