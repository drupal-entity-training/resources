<?php

namespace Drupal\event\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\EntityOwnerTrait;
use Drupal\user\UserInterface;

/**
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   label_collection = @Translation("Events"),
 *   label_singular = @Translation("event"),
 *   label_plural = @Translation("events"),
 *   base_table = "event",
 *   revision_table = "event_revision",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "revision" = "revision",
 *     "label" = "title",
 *     "owner" = "author",
 *     "published" = "published",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_author",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   handlers = {
 *     "access" = "Drupal\entity\EntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "local_action_provider" = {
 *       "collection" = "Drupal\entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       "revision" = "Drupal\entity\Routing\RevisionRouteProvider",
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   links = {
 *     "canonical" = "/event/{event}",
 *     "collection" = "/admin/content/events",
 *     "add-form" = "/admin/content/events/add",
 *     "edit-form" = "/admin/content/events/manage/{event}",
 *     "delete-form" = "/admin/content/events/manage/{event}/delete",
 *     "version-history" = "/event/{event}/revisions",
 *     "revision" = "/event/{event}/revisions/{event_revision}",
 *     "revision-revert-form" = "/event/{event}/revisions/{event_revision}/revert",
 *   },
 *   admin_permission = "administer event",
 *   show_revision_ui = true,
 * )
 */
class Event extends ContentEntityBase implements EntityChangedInterface, EntityOwnerInterface, EntityPublishedInterface {

  use EntityChangedTrait, EntityOwnerTrait, EntityPublishedTrait, RevisionLogEntityTrait;

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Get the field definitions for 'id' and 'uuid' from the parent.
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', ['weight' => 0]);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
        'weight' => 0,
      ])
      ->setDisplayOptions('form', ['weight' => 10]);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', ['weight' => 20]);

    // Get the field definitions for 'owner' and 'published' from the traits.
    $fields += static::ownerBaseFieldDefinitions($entity_type);
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['published']->setDisplayOptions('form', [
      'settings' => [
        'display_label' => TRUE,
      ],
      'weight' => 30,
    ]);

    $fields['maximum'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Maximum number of attendees'))
      ->setSetting('min', 1)
      ->setRequired(TRUE)
      ->setDefaultValue(10)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', ['weight' => 23]);

    $fields['attendees'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Attendees'))
      ->setSetting('target_type', 'user')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', ['weight' => 20])
      ->setDisplayOptions('form', ['weight' => 27]);

    $fields['remaining'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Remaining number of attendees'))
      ->setComputed(TRUE)
      ->setInternal(FALSE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 30,
      ]);

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('Path'))
      ->setComputed(TRUE)
      ->setDisplayOptions('form', ['weight' => 5]);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setRevisionable(TRUE);

    $fields += static::revisionLogBaseFieldDefinitions($entity_type);

    return $fields;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * @param string $title
   *
   * @return $this
   */
  public function setTitle($title) {
    return $this->set('title', $title);
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getDate() {
    return $this->get('date')->date;
  }

  /**
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *
   * @return $this
   */
  public function setDate(DrupalDateTime $date) {
    return $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
  }

  /**
   * @return \Drupal\filter\Render\FilteredMarkup
   */
  public function getDescription() {
    return $this->get('description')->processed;
  }

  /**
   * @param string $description
   * @param string $format
   *
   * @return $this
   */
  public function setDescription($description, $format) {
    return $this->set('description', [
      'value' => $description,
      'format' => $format,
    ]);
  }

  /**
   * @return int
   */
  public function getMaximum() {
    return $this->get('maximum')->value;
  }

  /**
   * @return \Drupal\user\UserInterface[]
   */
  public function getAttendees() {
    return $this->get('attendees')->referencedEntities();
  }

  /**
   * @param \Drupal\user\UserInterface $attendee
   *
   * @return $this
   */
  public function addAttendee(UserInterface $attendee) {
    $field_items = $this->get('attendees');

    $exists = FALSE;
    foreach ($field_items as $field_item) {
      if ($field_item->target_id === $attendee->id()) {
        $exists = TRUE;
      }
    }

    if (!$exists) {
      $field_items->appendItem($attendee);
    }

    return $this;
  }

  /**
   * @param \Drupal\user\UserInterface $attendee
   *
   * @return $this
   */
  public function removeAttendee(UserInterface $attendee) {
    $field_items = $this->get('attendees');
    foreach ($field_items as $delta => $field_item) {
      if ($field_item->target_id == $attendee->id()) {
        $field_items->set($delta, NULL);
      }
    }
    $field_items->filterEmptyItems();
    return $this;
  }

  /**
   * @return int
   */
  public function getRemaining() {
    return $this->get('remaining')->value;
  }



}

