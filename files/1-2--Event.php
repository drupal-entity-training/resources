<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   base_table = "event",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class Event extends ContentEntityBase {

}

