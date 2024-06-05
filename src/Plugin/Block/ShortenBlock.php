<?php

namespace Drupal\shorten\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\shorten\Service\ShortenService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ShortenBlock' block.
 *
 * @Block(
 *   id = "shorten_block",
 *   admin_label = @Translation("Shorten Block"),
 * )
 */
class ShortenBlock extends BlockBase implements BlockPluginInterface {

  /**
   * The shorten service.
   *
   * @var \Drupal\shorten\Service\ShortenService
   */
  protected $shortenService;

  /**
   * Constructs a new ShortenBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\shorten\Service\ShortenService $shorten_service
   *   The shorten service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ShortenService $shorten_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->shortenService = $shorten_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('shorten.shorten_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Provide form and other block-related logic here.
    return \Drupal::formBuilder()->getForm('Drupal\shorten\Form\ShortenForm');
  }

}
