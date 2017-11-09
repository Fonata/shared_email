<?php
/**
 * @file
 * Contains \Drupal\shared_email\Form\SharedEmailForm.
 */


namespace Drupal\shared_email\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SharedEmailForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'shared_email_form';
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'shared_email.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor
    $form = parent::buildForm($form, $form_state);
    // Default settings
    $config = $this->config('shared_email.settings');


    // shared email message text field
    $form['sharedemail_msg'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Shared E-mail message'),
      '#default_value' => $config->get('sharedemail_msg'),
      '#description' => $this->t('Warning message that is only displayed to users with appropriate permission, when they choose to save an e-mail address already used by another user.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //save the preference
    $config = $this->config('shared_email.settings');
    $config->set('sharedemail_msg', $form_state->getValue('sharedemail_msg'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }
}