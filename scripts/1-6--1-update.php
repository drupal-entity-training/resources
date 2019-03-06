<?php

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\event\Entity\Event;

$event = Event::load(1);

$event
  ->setTitle('Drupal Developer Days')
  ->setDate(new DrupalDateTime('tomorrow'))
  ->setDescription(
    '<p>The Drupal Developer Days are a great place to nerd out about all things Drupal!</p>',
    'basic_html'
  )
  ->setOwnerId(0)
  ->setPublished(FALSE)
  ->save();

