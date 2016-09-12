<?php

namespace Drupal\radaman_maksim_lesson8\Forms;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheCacheBackendInterface;

/**
 * Form with examples on how to use cache.
 */
class CacheLoggerForm extends FormBase {
  public function getFormId() {
    return 'cache-logger-form';
  }

  public function cid() {
    return 'radaman-maksim-lesson8:' . \Drupal::languageManager()
      ->getCurrentLanguage()
      ->getId();
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $message_status = $this->CacheMessage();
    drupal_set_message($message_status);
    $form['msg'] = [
      '#type' => 'textfield',
      '#title' => t('Type a message'),
      '#size' => 60,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'][] = [
      '#type' => 'submit',
      '#submit' => ['::SaveMsgInLogAndCache'],
      '#value' => $this->t('Save message in log & cache'),
    ];
    $form['actions']['submit'][] = [
      '#type' => 'submit',
      '#submit' => ['::InvalidateCache'],
      '#value' => $this->t('Invalidate cache'),
    ];
    $form['actions']['submit'][] = [
      '#type' => 'submit',
      '#submit' => ['::DeleteCache'],
      '#value' => $this->t('Delete cache'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $message = $form_state->getValue('msg');
    $handlers = $form_state->getSubmitHandlers();
    if (in_array('::SaveMsgInLogAndCache', $handlers) && empty($message)) {
      $form_state->setErrorByName('msg', $this->t("Provide text message"));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  public function SaveMsgInLogAndCache(array &$form, FormStateInterface $form_state) {
    $message = $form_state->getValue('msg');
    \Drupal::service('radaman_maksim_lesson8.multiple_channels_logger')
      ->logToOtherChannels($message);
    \Drupal::cache()->set($this->cid(), $message);
    drupal_get_messages();
  }

  public function InvalidateCache(array &$form, FormStateInterface $form_state) {
    \Drupal::cache()->invalidate($this->cid());
    drupal_get_messages();
  }

  public function DeleteCache(array &$form, FormStateInterface $form_state) {
    \Drupal::cache()->delete($this->cid());
    drupal_get_messages();
  }

  public function CacheMessage() {
    $cache = \Drupal::cache()->get($this->cid(), TRUE);
    if (!$cache) {
      return $this->t('There are no any cache items.');

    }
    if (!$cache->valid) {
      $message = $this->t('Cache Item: @cache_data  - Invalid', ['@cache_data' => $cache->data]);
    }
    else {
      $message = $this->t('Cache Item: @cache_data valid', ['@cache_data' => $cache->data]);
    }
    return $message;
  }
}
