<?php

namespace Drupal\shorten\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ShortenSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['shorten.settings'];
  }

  public function getFormId() {
    return 'shorten_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('shorten.settings');

    $form['use_www'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use "www." instead of "http://"'),
      '#default_value' => $config->get('use_www'),
      '#description' => $this->t('"www." is shorter, but "http://" is automatically link-ified by more services.'),
    ];

    $form['method'] = [
      '#type' => 'select',
      '#title' => $this->t('Method'),
      '#options' => [
        'php' => $this->t('PHP'),
        'curl' => $this->t('cURL'),
      ],
      '#default_value' => $config->get('method'),
      '#description' => $this->t('The method to use to retrieve the abbreviated URL. cURL is much faster, if available.'),
    ];

    $form['service'] = [
      '#type' => 'select',
      '#title' => $this->t('Service'),
      '#options' => [
        'bitly' => $this->t('bit.ly'),
        'tinyurl' => $this->t('TinyURL'),
        'isgd' => $this->t('is.gd'),
        'googl' => $this->t('goo.gl'),
        'cli.gs' => $this->t('cli.gs'),
        'fwd4.me' => $this->t('fwd4.me'),
        'migre.me' => $this->t('migre.me'),
        'peekURL' => $this->t('peekURL'),
        'qr.cx' => $this->t('qr.cx'),
        'redir.ec' => $this->t('redir.ec'),
        'ri.ms' => $this->t('ri.ms'),
      ],
      '#default_value' => $config->get('service'),
      '#description' => $this->t('The default service to use to create the abbreviated URL.'),
    ];

    $form['backup_service'] = [
      '#type' => 'select',
      '#title' => $this->t('Backup Service'),
      '#options' => [
        'bitly' => $this->t('bit.ly'),
        'tinyurl' => $this->t('TinyURL'),
        'isgd' => $this->t('is.gd'),
        'googl' => $this->t('goo.gl'),
        'cli.gs' => $this->t('cli.gs'),
        'fwd4.me' => $this->t('fwd4.me'),
        'migre.me' => $this->t('migre.me'),
        'peekURL' => $this->t('peekURL'),
        'qr.cx' => $this->t('qr.cx'),
        'redir.ec' => $this->t('redir.ec'),
        'ri.ms' => $this->t('ri.ms'),
      ],
      '#default_value' => $config->get('backup_service'),
      '#description' => $this->t('The service to use to create the abbreviated URL if the primary or requested service is down.'),
    ];

    $form['show_services'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the list of URL shortening services in the user interface'),
      '#default_value' => $config->get('show_services'),
      '#description' => $this->t('Allow users to choose which service to use in the Shorten URLs block and page.'),
    ];

    $form['timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Time out URL shortening requests after (seconds)'),
      '#default_value' => $config->get('timeout'),
      '#description' => $this->t('Cancel retrieving a shortened URL if the URL shortening service takes longer than this amount of time to respond.'),
    ];

    $form['cache_duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Cache shortened URLs for (seconds)'),
      '#default_value' => $config->get('cache_duration'),
      '#description' => $this->t('Shortened URLs are stored after retrieval to improve performance.'),
    ];

    $form['cache_failure_duration'] = [
      '#type' => 'number',
      '#title' => $this->t('On failure, cache full URLs for (seconds)'),
      '#default_value' => $config->get('cache_failure_duration'),
      '#description' => $this->t('When a shortener service is unavailable, the full URL will be cached temporarily to prevent more requests from overloading the server.'),
    ];

    $form['clear_cache_on_drupal_cache_clear'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Clear Shorten URLs cache when all Drupal caches are cleared'),
      '#default_value' => $config->get('clear_cache_on_drupal_cache_clear'),
      '#description' => $this->t('Sometimes Drupal automatically clears all caches, such as after running database updates.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('shorten.settings')
      ->set('use_www', $form_state->getValue('use_www'))
      ->set('method', $form_state->getValue('method'))
      ->set('service', $form_state->getValue('service'))
      ->set('backup_service', $form_state->getValue('backup_service'))
      ->set('show_services', $form_state->getValue('show_services'))
      ->set('timeout', $form_state->getValue('timeout'))
      ->set('cache_duration', $form_state->getValue('cache_duration'))
      ->set('cache_failure_duration', $form_state->getValue('cache_failure_duration'))
      ->set('clear_cache_on_drupal_cache_clear', $form_state->getValue('clear_cache_on_drupal_cache_clear'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
