<?php

/**
 * @file
 * Contains \Drupal\flood_control\Form\FloodControlSettingsForm.
 */

namespace Drupal\flood_control\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Administration settings form.
 */
class FloodControlSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'flood_control_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['user.flood'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $flood_config = \Drupal::config('user.flood');
    $flood_settings = $flood_config->get();

    $counter_options = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 75, 100, 125, 150, 200, 250, 500];
    $time_options = [60, 180, 300, 600, 900, 1800, 2700, 3600, 10800, 21600, 32400, 43200, 86400];

    // User module flood events.
    $form['login'] = array(
      '#type' => 'fieldset',
      '#title' => t('Login'),
    );

    $form['login']['ip_limit'] = array(
      '#type' => 'select',
      '#title' => t('Failed login (IP) limit'),
      '#options' => array_combine($counter_options, $counter_options),
      '#default_value' => $flood_settings['ip_limit'],
    );

    $form['login']['ip_window'] = array(
      '#type' => 'select',
      '#title' => t('Failed login (IP) window'),
      '#options' => array(0 => t('None (disabled)')) + array_map(array(\Drupal::service('date.formatter'), 'formatInterval'), array_combine($time_options, $time_options)),
      '#default_value' => $flood_settings['ip_window'],
    );
    $form['login']['user_limit'] = array(
      '#type' => 'select',
      '#title' => t('Failed login (username) limit'),
      '#options' => array_combine($counter_options, $counter_options),
      '#default_value' => $flood_settings['user_limit'],
    );
    $form['login']['user_window'] = array(
      '#type' => 'select',
      '#title' => t('Failed login (username) window'),
      '#options' => array(0 => t('None (disabled)')) + array_map(array(\Drupal::service('date.formatter'), 'formatInterval'), array_combine($time_options, $time_options)),
      '#default_value' => $flood_settings['user_window'],
    );

    // Contact module flood events.
    $contact_config = \Drupal::config('contact.settings');
    $contact_settings = $contact_config->get();
    $form['contact'] = array(
      '#type' => 'fieldset',
      '#title' => t('Contact forms'),
    );

    $form['contact']['contact_threshold_limit'] = array(
      '#type' => 'select',
      '#title' => t('Sending e-mails limit'),
      '#options' => array_combine($counter_options, $counter_options),
      '#default_value' => $contact_settings['flood']['limit'],
    );
    $form['contact']['contact_threshold_window'] = array(
      '#type' => 'select',
      '#title' => t('Sending e-mails window'),
      '#options' => array(0 => t('None (disabled)')) + array_map(array(\Drupal::service('date.formatter'), 'formatInterval'), array_combine($time_options, $time_options)),
      '#default_value' => $contact_settings['flood']['interval'],
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $flood_config = \Drupal::configFactory()->getEditable('user.flood');
    $flood_config
      ->set('ip_limit', $form_state->getValue('ip_limit'))
      ->set('ip_window', $form_state->getValue('ip_window'))
      ->set('user_limit', $form_state->getValue('user_limit'))
      ->set('user_window', $form_state->getValue('user_window'))
      ->save();

    $contact_config = \Drupal::configFactory()->getEditable('contact.settings');
    $contact_config
      ->set('flood.limit', $form_state->getValue('contact_threshold_limit'))
      ->set('flood.interval', $form_state->getValue('contact_threshold_window'))
      ->save();

    parent::submitForm($form, $form_state);

  }

}
