<?php

use Drupal\event\Entity\Event;
use Drupal\user\Entity\User;

$event = Event::load(3);
$user = User::load(1);

print 'Maximum number of attendees: ' . $event->getMaximum() . "\n";
print 'Remaining number of attendees: ' . $event->getRemaining() . "\n";

$event->addAttendee($user)->save();
$event = Event::load($event->id());
print 'Remaining number of attendees: ' . $event->getRemaining() . "\n";

$event->removeAttendee($user)->save();
$event = Event::load($event->id());
print 'Remaining number of attendees: ' . $event->getRemaining() . "\n";

