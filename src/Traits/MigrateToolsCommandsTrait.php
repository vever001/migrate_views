<?php

namespace Drupal\migrate_views\Traits;

use Drupal\migrate\Exception\RequirementsException;
use Drupal\migrate\Plugin\RequirementsInterface;

/**
 * Migrate Tools Commands trait.
 *
 * @property \Drupal\migrate\Plugin\MigrationPluginManager $migrationPluginManager
 */
trait MigrateToolsCommandsTrait {

  /**
   * Retrieve a list of active migrations.
   *
   * Verbatim copy of:
   *
   * @see \Drupal\migrate_tools\Commands\MigrateToolsCommands::migrationsList()
   *
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   */
  protected function migrationsList($migration_ids = '', array $options = []) {
    // Filter keys must match the migration configuration property name.
    $filter['migration_group'] = $options['group'] ? explode(',', $options['group']) : [];
    $filter['migration_tags'] = $options['tag'] ? explode(',', $options['tag']) : [];

    $manager = $this->migrationPluginManager;
    $plugins = $manager->createInstances([]);
    $matched_migrations = [];

    // Get the set of migrations that may be filtered.
    if (empty($migration_ids)) {
      $matched_migrations = $plugins;
    }
    else {
      // Get the requested migrations.
      $migration_ids = explode(',', mb_strtolower($migration_ids));
      foreach ($plugins as $id => $migration) {
        if (in_array(mb_strtolower($id), $migration_ids)) {
          $matched_migrations[$id] = $migration;
        }
      }
    }

    // Do not return any migrations which fail to meet requirements.
    /** @var \Drupal\migrate\Plugin\Migration $migration */
    foreach ($matched_migrations as $id => $migration) {
      if ($migration->getSourcePlugin() instanceof RequirementsInterface) {
        try {
          $migration->getSourcePlugin()->checkRequirements();
        }
        catch (RequirementsException $e) {
          unset($matched_migrations[$id]);
        }
      }
    }

    // Filters the matched migrations if a group or a tag has been input.
    if (!empty($filter['migration_group']) || !empty($filter['migration_tags'])) {
      // Get migrations in any of the specified groups and with any of the
      // specified tags.
      foreach ($filter as $property => $values) {
        if (!empty($values)) {
          $filtered_migrations = [];
          foreach ($values as $search_value) {
            foreach ($matched_migrations as $id => $migration) {
              // Cast to array because migration_tags can be an array.
              $configured_values = (array) $migration->get($property);
              $configured_id = (in_array(
                $search_value,
                $configured_values
              )) ? $search_value : 'default';
              if (empty($search_value) || $search_value == $configured_id) {
                if (empty($migration_ids) || in_array(
                    mb_strtolower($id),
                    $migration_ids
                  )) {
                  $filtered_migrations[$id] = $migration;
                }
              }
            }
          }
          $matched_migrations = $filtered_migrations;
        }
      }
    }

    // Sort the matched migrations by group.
    if (!empty($matched_migrations)) {
      foreach ($matched_migrations as $id => $migration) {
        $configured_group_id = empty($migration->migration_group) ? 'default' : $migration->migration_group;
        $migrations[$configured_group_id][$id] = $migration;
      }
    }
    return isset($migrations) ? $migrations : [];
  }

}
