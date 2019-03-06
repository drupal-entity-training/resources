<?php

use Drupal\event\Entity\Event;

$event = Event::load(1);

print 'Title: ' . $event->get('title')->value . "\n\n";

print 'Date value: ' . $event->get('date')->value . "\n";
print 'Date object: ' . var_export($event->get('date')->date, TRUE) . "\n\n";

print 'Description value: ' . $event->get('description')->value . "\n";
print 'Description format: ' . $event->get('description')->format . "\n";
print 'Processed description: ' . var_export($event->get('description')->processed, TRUE) . "\n\n";

print 'Author: ' . $event->get('author')->entity->getDisplayName() . "\n\n";

print 'Published: ' . $event->get('published')->value . "\n";

