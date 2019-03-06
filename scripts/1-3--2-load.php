<?php

use Drupal\event\Entity\Event;

$event = Event::load(1);
print 'ID: ' . $event->id() . PHP_EOL;
print 'UUID: ' . $event->uuid() . PHP_EOL;

