<?php

namespace Drupal\pager_for_content_type\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Configure regional settings for this site.
 */
class PagerForContentTypeSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * SavePopupConfigForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pager_for_content_type_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['pager_for_content_type.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pager_for_content_type.settings');

    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    $more_links_options = [
      '0' => 'Off',
      '4' => 4,
      '5' => 6,
      '10' => 10,
    ];

    $form['pager_for_content_type_general'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Global options'),
      '#markup' => $this->t('<p>These global options are overriden by the content type options.</p><p>Available token: [content-type]</p>'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['pager_for_content_type_general']['pager_for_content_type_previous_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('"Previous" text'),
      '#default_value' => $config->get('pager_for_content_type_previous_text'),
      '#size' => 30,
      '#maxlength' => 64,
      '#required' => TRUE,
    ];

    $form['pager_for_content_type_general']['pager_for_content_type_next_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('"Next" text'),
      '#default_value' => $config->get('pager_for_content_type_next_text'),
      '#size' => 30,
      '#maxlength' => 64,
      '#required' => TRUE,
    ];

    $form['pager_for_content_type_content_type'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content type options'),
      '#description' => $this->t('Pager will available on checked content types (only in full view mode)'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    foreach ($node_types as $node_type) {

      $form['pager_for_content_type_content_type'][$node_type->get("type")] = [
        '#type' => 'fieldset',
        '#title' => $node_type->get("name"),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
      ];

      $key = $node_type->get("type") . '_pager_for_content_type_on';
      $form['pager_for_content_type_content_type'][$node_type->get("type")][$key] = [
        '#type' => 'checkbox',
        '#title' => $this->t('On'),
        '#default_value' => $config->get($key),
      ];

      $key = $node_type->get("type") . '_pager_for_content_type_author';
      $form['pager_for_content_type_content_type'][$node_type->get("type")][$key] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Pager by node author'),
        '#default_value' => $config->get($key),
      ];

      $key = $node_type->get("type") . '_pager_for_content_type_previous_text';
      $form['pager_for_content_type_content_type'][$node_type->get("type")][$key] = [
        '#type' => 'textfield',
        '#title' => $this->t('"Previous" text'),
        '#default_value' => $config->get($key),
        '#size' => 30,
        '#maxlength' => 64,
        '#required' => FALSE,
      ];

      $key = $node_type->get("type") . '_pager_for_content_type_next_text';
      $form['pager_for_content_type_content_type'][$node_type->get("type")][$key] = [
        '#type' => 'textfield',
        '#title' => $this->t('"Next" text'),
        '#default_value' => $config->get($key),
        '#size' => 30,
        '#maxlength' => 64,
        '#required' => FALSE,
      ];

      $key = $node_type->get("type") . '_pager_for_content_type_more_links';
      $form['pager_for_content_type_content_type'][$node_type->get("type")][$key] = [
        '#type' => 'select',
        '#title' => $this->t('Show more nodes titles after the pager'),
        '#description' => $this->t('First half before pager, second half after pager'),
        '#default_value' => $config->get($key),
        '#options' => $more_links_options,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    $config = $this->config('pager_for_content_type.settings');
    $config->set('pager_for_content_type_previous_text', $form_state->getValue('pager_for_content_type_previous_text'));
    $config->set('pager_for_content_type_next_text', $form_state->getValue('pager_for_content_type_next_text'));

    foreach ($node_types as $node_type) {

      $key = $node_type->get("type") . '_pager_for_content_type_on';
      $config->set($key, $form_state->getValue($key));

      $key = $node_type->get("type") . '_pager_for_content_type_author';
      $config->set($key, $form_state->getValue($key));

      $key = $node_type->get("type") . '_pager_for_content_type_previous_text';
      $config->set($key, $form_state->getValue($key));

      $key = $node_type->get("type") . '_pager_for_content_type_next_text';
      $config->set($key, $form_state->getValue($key));

      $key = $node_type->get("type") . '_pager_for_content_type_more_links';
      $config->set($key, $form_state->getValue($key));
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
