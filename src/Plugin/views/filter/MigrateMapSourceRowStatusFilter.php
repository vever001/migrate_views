<?php

namespace Drupal\migrate_views\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Defines a filter handler for migrate map source row status.
 *
 * @ViewsFilter("migrate_map_source_row_status")
 */
class MigrateMapSourceRowStatusFilter extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $this->valueOptions = _migrate_views_migrate_map_source_row_status_options();
    }
    return $this->valueOptions;
  }

}
