<?php

declare(strict_types=1);

namespace Drupal\event\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\event\Entity\DrupalEventInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provide a 'Header' block.
 *
 * @Block(
 *      id = "event_related",
 *      admin_label = @Translation("Rendered event node related to an other"),
 *      category = @Translation("Adimeo"),
 *      context_definitions = {
 *          "event" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *      }
 * )
 */
final class EventBlock extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface
{
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_display.repository'),
      $container->get('entity_type.manager'),
    );
  }

  public function build(): array
  {
    $event = $this->getContextValue('event');

    if (
      !$event instanceof DrupalEventInterface ||
      !$event->hasField('field_event_type') ||
      !$this->isTeaserDisplayModeAvailable('event')
    ) {
      return [];
    }

    $event_term_id = $event->getEventTermId();
    $event_ids_related = $event->getXRelatedEventExceptItself($event_term_id, (int) $event->id(), 3);
    if (count($event_ids_related) === 0) {
      return [];
    }

    $events_related = $this->entityTypeManager->getStorage('node')->loadMultiple($event_ids_related);
    return array_map(fn(DrupalEventInterface $event) => $this->renderEventTeaser($event), $events_related);
  }

  public function getCacheContexts(): array
  {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

  public function getCacheTags(): array
  {
    return Cache::mergeTags(parent::getCacheTags(), ['event_related']);
  }

  private function isTeaserDisplayModeAvailable($bundle) {
    $view_modes = $this->entityDisplayRepository->getViewModes('node');
    return isset($view_modes['teaser']);
  }

  private function renderEventTeaser(DrupalEventInterface $event) {
    return $this->entityTypeManager->getViewBuilder('node')->view($event, 'teaser');
  }
}
