<?php

namespace Drupal\shorten\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ShortenShortBlock' block.
 *
 * @Block(
 *   id = "shorten_short_block",
 *   admin_label = @Translation("Shorten Short Block"),
 * )
 */
class ShortenShortBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Provide a shorter version of the Shorten block, if necessary.
  }

}
