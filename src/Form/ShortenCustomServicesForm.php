<?php

namespace Drupal\shorten\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ShortenCustomServicesForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['shorten.custom_services'];
  }

  public function getFormId() {
    return 'shorten_custom_services_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('shorten.custom_services');

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config->get('title'),
      '#description' => $this->t('The name of the service.'),
    ];

    $form['api_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API endpoint URL'),
      '#default_value' => $config->get('api_endpoint'),
      '#description' => $this->t('The URL of the API endpoint, with parameters, such that the long URL can be appended to the end.'),
    ];

    $form['response_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Response type'),
      '#options' => [
        'text' => $this->t('Text'),
        'xml' => $this->t('XML'),
        'json' => $this->t('JSON'),
      ],
      '#default_value' => $config->get('response_type'),
      '#description' => $this->t('The type of response the API endpoint returns.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('shorten.custom_services')
      ->set('title', $form_state->getValue('title'))
      ->set('api_endpoint', $form_state->getValue('api_endpoint'))
      ->set('response_type', $form_state->getValue('response_type'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
