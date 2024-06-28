<?php

declare(strict_types=1);

namespace Drupal\event\Storage;

interface DrupalEventStorageInterface
{
    /**
     * Return the X latest event prioritize by the term id.
     *
     * @param int $termId The id of the taxonomy term related to the content.
     * @param int $nidExclude The node id to exclude to not repeat itself.
     * @param int $lengthMax The number of record to return.
     * @return array<array-key, string>
     */
    public function getLatestEventByTermId(int $termId, int $nidExclude, int $lengthMax = 3): array;

    /**
     * Return all node of type event expired.
     *
     * @return array<array-key, string>
     */
    public function getExpiredEvents(): array;
}
