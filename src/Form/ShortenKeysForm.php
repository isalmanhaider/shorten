<?php

namespace Drupal\shorten\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ShortenKeysForm.
 */
class ShortenKeysForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['shorten.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shorten_keys_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('shorten.settings');

    $form['bitly_login'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bit.ly Login'),
      '#default_value' => $config->get('bitly_login'),
    ];

    $form['bitly_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bit.ly API Key'),
      '#default_value' => $config->get('bitly_api_key'),
    ];

    $form['tinyurl_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TinyURL API Key'),
      '#default_value' => $config->get('tinyurl_api_key'),
    ];

    $form['isgd_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('is.gd API Key'),
      '#default_value' => $config->get('isgd_api_key'),
    ];

    $form['fwd4me_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('fwd4.me API Key'),
      '#default_value' => $config->get('fwd4me_api_key'),
    ];

    $form['redir_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('redir.ec API Key'),
      '#default_value' => $config->get('redir_api_key'),
    ];

    $form['dubco_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('dub.co API Key'),
      '#default_value' => $config->get('dubco_api_key'),
      '#description' => $this->t('Enter your dub.co API key here.'),
    ];
    $form['dubco_workspace_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('dub.co Workspace ID'),
      '#default_value' => $config->get('dubco_workspace_id'),
      '#description' => $this->t('Enter your dub.co Workspace ID here.'),
  ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('shorten.settings')
      ->set('bitly_login', $form_state->getValue('bitly_login'))
      ->set('bitly_api_key', $form_state->getValue('bitly_api_key'))
      ->set('tinyurl_api_key', $form_state->getValue('tinyurl_api_key'))
      ->set('isgd_api_key', $form_state->getValue('isgd_api_key'))
      ->set('fwd4me_api_key', $form_state->getValue('fwd4me_api_key'))
      ->set('redir_api_key', $form_state->getValue('redir_api_key'))
      ->set('dubco_api_key', $form_state->getValue('dubco_api_key'))
      ->set('dubco_workspace_id', $form_state->getValue('dubco_workspace_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
