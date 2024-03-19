<?php

declare(strict_types=1);

namespace Drupal\content_polls\Entity;

use Drupal\content_polls\ContentPollResponseInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the content poll response entity class.
 *
 * @ContentEntityType(
 *   id = "content_poll_response",
 *   label = @Translation("Poll response"),
 *   label_collection = @Translation("Poll responses"),
 *   label_singular = @Translation("poll response"),
 *   label_plural = @Translation("poll responses"),
 *   label_count = @PluralTranslation(
 *     singular = "@count poll responses",
 *     plural = "@count poll responses",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\content_polls\ContentPollResponseListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\content_polls\Form\ContentPollResponseForm",
 *       "edit" = "Drupal\content_polls\Form\ContentPollResponseForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\content_polls\Routing\ContentPollResponseHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "content_poll_response",
 *   admin_permission = "administer content_poll_response",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/content-poll-response",
 *     "add-form" = "/content-poll-response/add",
 *     "canonical" = "/content-poll-response/{content_poll_response}",
 *     "edit-form" = "/content-poll-response/{content_poll_response}",
 *     "delete-form" = "/content-poll-response/{content_poll_response}/delete",
 *     "delete-multiple-form" = "/admin/content/content-poll-response/delete-multiple",
 *   },
 * )
 */
final class ContentPollResponse extends ContentEntityBase implements ContentPollResponseInterface
{

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['poll'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'content_poll')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('view', TRUE);

    $fields['response'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'content_poll_option')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the content poll response was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}
