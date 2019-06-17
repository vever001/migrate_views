<?php

namespace Drupal\migrate_views\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Defines a filter handler for migrate message level.
 *
 * @ViewsFilter("migrate_message_level")
 */
class MigrateMessageLevelFilter extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $this->valueOptions = _migrate_views_migrate_message_level_options();
    }
    return $this->valueOptions;
  }

}
