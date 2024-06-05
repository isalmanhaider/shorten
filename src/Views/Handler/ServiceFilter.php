<?php

namespace Drupal\shorten\Views\Handler;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\ResultRow;

/**
 * Filter handler to filter by service.
 *
 * @ViewsFilter("service_filter")
 */
class ServiceFilter extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $this->query->addWhere($this->options['group'], $this->realField, $this->value, 'IN');
  }
}
