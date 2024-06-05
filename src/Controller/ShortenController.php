<?php

namespace Drupal\shorten\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Class ShortenController.
 */
class ShortenController extends ControllerBase {

  protected $database;
  protected $messenger;

  /**
   * Constructs a new ShortenController.
   */
  public function __construct(Connection $database, MessengerInterface $messenger) {
    $this->database = $database;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('messenger')
    );
  }

  /**
   * Display the list of shortened URLs.
   */
  public function report() {
    $header = [
      ['data' => $this->t('Original URL'), 'field' => 'original_url', 'sort' => 'asc'],
      ['data' => $this->t('Shortened URL'), 'field' => 'short_url'],
      ['data' => $this->t('Service'), 'field' => 'service'],
      ['data' => $this->t('Created'), 'field' => 'created'],
    ];

    $query = $this->database->select('shorten_urls', 'su')
      ->fields('su', ['original_url', 'short_url', 'service', 'created'])
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(100)
      ->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);

    $results = $query->execute()->fetchAll();

    $rows = [];
    foreach ($results as $result) {
      $rows[] = [
        'original_url' => Link::fromTextAndUrl($result->original_url, Url::fromUri($result->original_url))->toString(),
        'short_url' => Link::fromTextAndUrl($result->short_url, Url::fromUri($result->short_url))->toString(),
        'service' => $result->service,
        'created' => date('Y-m-d H:i:s', $result->created),
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No shortened URLs found.'),
    ];

    $build['pager'] = ['#type' => 'pager'];

    // Add the clear button.
    $build['clear_button'] = [
      '#type' => 'link',
      '#title' => $this->t('Clear All Records'),
      '#url' => Url::fromRoute('shorten.admin_clear'),
      '#attributes' => [
        'class' => ['button'],
        'onclick' => "if(!confirm('Warning: there is no confirmation page. Cleared records are permanently deleted. Note: clearing records does not clear the Shorten URLs cache. Also, URLs already in the cache are not recorded again.')){return false;}"
      ],
    ];

    return $build;
  }

  /**
   * Clear the shorten_urls table.
   */
  public function clear() {
    $this->database->truncate('shorten_urls')->execute();
    $this->messenger->addMessage($this->t('All shortened URLs have been cleared.'));
    return $this->redirect('shorten.admin_report');
  }

}
