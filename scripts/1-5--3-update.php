<?php

use Drupal\event\Entity\Event;

$event = Event::load(1);

$event
  ->set('title', 'DrupalCon')
  ->set('date', '2019-04-08T09:00:00')
  ->set('description', [
    'value' => '<p>DrupalCon is a great place to meet international Drupal superstars.</p>',
    'format' => 'basic_html',
  ])
  ->set('author', 1)
  ->set('published', FALSE)
  ->save();

