<?php

declare(strict_types=1);

namespace Drupal\event\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Queue\QueueWorkerInterface;
use Drupal\event\Entity\DrupalEventInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Queue Worker that unpublishes events with expired end dates.
 *
 * @QueueWorker(
 *   id = "event_unpublish_queue_worker",
 *   title = @Translation("Event Unpublish Worker"),
 *   cron = {"time" = 60}
 * )
 */
final class EventUnpublishQueueWorker extends QueueWorkerBase implements QueueWorkerInterface, ContainerFactoryPluginInterface
{
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        protected EntityTypeManagerInterface $entityTypeManager,
        protected LoggerInterface $logger,
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ): self {
        return new self(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('entity_type.manager'),
            $container->get('event.channel.event'),
        );
    }

    public function processItem($data): void
    {
        $event = $this->entityTypeManager->getStorage('node')->load($data);
        assert(
            $event instanceof DrupalEventInterface,
            sprintf('This entity %s is not an event', $event->id())
        );

        $event->setUnpublished();
        $event->save();

        $this->logger->info(sprintf('Unpublished event #%d.', $event->id()));
    }
}
