<?php

declare(strict_types=1);

namespace Drupal\content_polls\Entity;

use Drupal\content_polls\ContentPollInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the content poll entity class.
 *
 * @ContentEntityType(
 *   id = "content_poll",
 *   label = @Translation("Poll"),
 *   label_collection = @Translation("Polls"),
 *   label_singular = @Translation("poll"),
 *   label_plural = @Translation("polls"),
 *   label_count = @PluralTranslation(
 *     singular = "@count polls",
 *     plural = "@count polls",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\content_polls\ContentPollListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\content_polls\ContentPollAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\content_polls\Form\ContentPollForm",
 *       "edit" = "Drupal\content_polls\Form\ContentPollForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\content_polls\Routing\ContentPollHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "content_poll",
 *   admin_permission = "administer content_poll",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/content-poll",
 *     "add-form" = "/content-poll/add",
 *     "canonical" = "/content-poll/{content_poll}",
 *     "edit-form" = "/content-poll/{content_poll}",
 *     "delete-form" = "/content-poll/{content_poll}/delete",
 *     "delete-multiple-form" = "/admin/content/content-poll/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.content_poll.settings",
 * )
 */
final class ContentPoll extends ContentEntityBase implements ContentPollInterface
{

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void
  {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Poll name'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Open')
      ->setSetting('off_label', 'Closed')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['dates'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Poll dates'))
      ->setSetting('datetime_type', 'date')
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 10
      ])
      ->setRequired(true)
      ->setDisplayConfigurable('form', TRUE);

    $fields['poll_options'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Choices'))
      ->setSetting('target_type', 'content_poll_option')
      ->setSetting('handler', 'default')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'entity_reference_entity_view',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'settings' => [
          'label_singular' => 'Option',
          'label_plural' => 'Options',
          'allow_new' => true,
          'allow_existing' => false,
          'collapsible' => true,
          'collapsed' => true,
        ],
        'weight' => -3,
      ]);

    $fields['intro_text'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Introduction'))
      ->setDescription(t('Text to show above the poll'))
      ->setDefaultValue(t('Welcome to the poll.  Click the vote button beneath your preferred option.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['completion_text'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Completion text'))
      ->setDescription(t('Text to show when a user submits their poll choice'))
      ->setDefaultValue(t('Thanks for submitting your choice.  Check back soon to see how your choice did.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['closed_text'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Poll closed text'))
      ->setDescription(t('Text to show when the poll has closed'))
      ->setDefaultValue(t('This poll is now closed.  Check back for more on the results soon.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['unopened_text'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Poll not yet open text'))
      ->setDescription(t('Text to show when the poll has not yet opened'))
      ->setDefaultValue(t('This poll is not open yet.  Check back soon.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(self::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
        'region' => 'hidden',
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the content poll was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
        'region' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the content poll was last edited.'));

    return $fields;
  }
}
