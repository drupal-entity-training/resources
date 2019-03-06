<?php

use Drupal\event\Entity\Event;

$event = Event::load(1);
$event->delete();

