<?php

declare(strict_types=1);

namespace Drupal\event\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\event\ValueObject\TodayDate;
use Drupal\node\NodeInterface;
use Exception;

final class DrupalEventStorage extends SqlContentEntityStorage implements DrupalEventStorageInterface
{
    /**
     * @throws Exception
     */
    public function getLatestEventByTermId(int $termId, int $nidExclude, int $lengthMax = 3): array
    {
        $current_time = TodayDate::now();

        $query = $this->database->select('node_field_data', 'n');
        $query->leftJoin('node__field_date_range', 'dr', 'dr.entity_id = n.nid');
        $query->leftJoin('node__field_event_type', 'et', 'et.entity_id = n.nid');
        $query->addExpression('CASE WHEN field_event_type_target_id = :type THEN 1 ELSE 2 END', 'term_choose_first', [':type' => $termId]);
        $query->fields('n', ['nid'])
        ->condition('type', 'event')
        ->condition('status', NodeInterface::PUBLISHED)
        ->condition('nid', $nidExclude, '<>')
        ->condition('dr.field_date_range_end_value', $current_time->formatForDatabase(), '>=')
        ->orderBy('term_choose_first')
        ->orderBy('dr.field_date_range_value')
        ->range(0, $lengthMax);

        return $query->execute()->fetchCol();
    }

    public function getExpiredEvents(): array
    {
        $current_time = TodayDate::now();

        $query = $this->getQuery()
        ->accessCheck()
        ->condition('type', 'event')
        ->condition('status', NodeInterface::PUBLISHED)
        ->condition('field_date_range', $current_time->formatForDatabase(), '<');

        return $query->execute();
    }
}
