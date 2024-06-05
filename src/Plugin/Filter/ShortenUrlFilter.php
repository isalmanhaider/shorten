<?php

namespace Drupal\shorten\Plugin\Filter;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\AttachmentsInterface;

/**
 * Provides a filter to shorten URLs.
 *
 * @Filter(
 *   id = "shorten_url_filter",
 *   title = @Translation("URL Shortener"),
 *   description = @Translation("Replaces URLs with a shortened version."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *   settings = {
 *     "behavior" = "display_shortened_with_expand",
 *     "max_length" = 72
 *   }
 * )
 */
class ShortenUrlFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a ShortenUrlFilter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    // Query the database for short URLs.
    $query = $this->database->select('shorten_urls', 'su')
      ->fields('su', ['original_url', 'short_url']);
    $results = $query->execute()->fetchAllKeyed();

    // Replace URLs in the text with their shortened versions.
    foreach ($results as $original_url => $short_url) {
      $text = str_replace($original_url, $short_url, $text);
    }

    // Optionally, truncate the URL text to the maximum length.
    if ($this->settings['max_length']) {
      $text = $this->truncateUrls($text, $this->settings['max_length']);
    }

    $result = new FilterProcessResult($text);
    $result->setAttachments([
      'library' => [
        'shorten/shorten',
      ],
    ]);

    return $result;
  }

  /**
   * Truncates URLs in the text to the specified maximum length.
   *
   * @param string $text
   *   The text containing URLs.
   * @param int $max_length
   *   The maximum length for URLs.
   *
   * @return string
   *   The text with URLs truncated.
   */
  protected function truncateUrls($text, $max_length) {
    $pattern = '/<a href="([^"]+)">([^<]+)<\/a>/';
    return preg_replace_callback($pattern, function ($matches) use ($max_length) {
      $url = $matches[1];
      $link_text = $matches[2];
      if (strlen($link_text) > $max_length) {
        $link_text = substr($link_text, 0, $max_length) . '...';
      }
      return '<a href="' . $url . '">' . $link_text . '</a>';
    }, $text);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['behavior'] = [
      '#type' => 'radios',
      '#title' => $this->t('Behavior'),
      '#options' => [
        'display_shortened_with_expand' => $this->t('Display the shortened URL by default, and add an "(expand)"/"(shorten)" link'),
        'display_shortened_no_expand' => $this->t('Display the shortened URL by default, and do not allow expanding it'),
        'display_full_with_shorten' => $this->t('Display the full URL by default, and add a "(shorten)"/"(expand)" link'),
      ],
      '#default_value' => $this->settings['behavior'],
    ];

    $form['max_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum link text length'),
      '#description' => $this->t('URLs longer than this number of characters will be truncated to prevent long strings that break formatting. The link itself will be retained; just the text portion of the link will be truncated.'),
      '#default_value' => $this->settings['max_length'],
    ];

    return $form;
  }

}
