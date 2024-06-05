<?php

namespace Drupal\shorten\Views\Handler;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display the hostname of the URL.
 *
 * @ViewsField("hostname_field")
 */
class HostnameField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $url = $this->getValue($values);
    return parse_url($url, PHP_URL_HOST);
  }
}
