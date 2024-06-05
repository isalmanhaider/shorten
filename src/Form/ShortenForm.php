<?php

namespace Drupal\shorten\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\shorten\Service\ShortenService;

/**
 * Class ShortenForm.
 */
class ShortenForm extends FormBase {

  protected $shortenService;

  /**
   * Constructs a new ShortenForm.
   */
  public function __construct(ShortenService $shorten_service) {
    $this->shortenService = $shorten_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('shorten.shorten_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shorten_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL to shorten'),
      '#required' => TRUE,
    ];

    $form['service'] = [
      '#type' => 'select',
      '#title' => $this->t('Service'),
      '#options' => [
        'bitly' => $this->t('bit.ly'),
        'tinyurl' => $this->t('TinyURL'),
        'isgd' => $this->t('is.gd'),
        'googl' => $this->t('goo.gl'),
        'cligs' => $this->t('cli.gs'),
        'fwd4me' => $this->t('fwd4.me'),
        'migreme' => $this->t('migre.me'),
        'peew' => $this->t('peew.pw'),
        'qr' => $this->t('qr.cx'),
        'redir' => $this->t('redir.ec'),
        'ri' => $this->t('ri.ms'),
        'dubco' => $this->t('dub.co'),
      ],
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Shorten'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $url = $form_state->getValue('url');
    $service = $form_state->getValue('service');

    $shortened_url = $this->shortenService->shorten($url, $service);

    \Drupal::messenger()->addMessage($this->t('Shortened URL: @url', ['@url' => $shortened_url]));
  }
}
