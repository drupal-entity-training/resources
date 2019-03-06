<?php

use Drupal\event\Entity\Event;

$event = Event::create([
  'title' => 'Drupal User Group',
  'date' => (new \DateTime())->format(DATETIME_DATETIME_STORAGE_FORMAT),
  'description' => [
    'value' => '<p>The monthly meeting of Drupalists is happening today!</p>',
    'format' => 'restricted_html',
  ],
]);
$event->save();

