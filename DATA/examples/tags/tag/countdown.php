<?php

  $tagAbout   = 'The time left until a moment, in words inside a time element - ticking every second in the browser when asked.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{countdown '2026-12-31 00:00'}
{countdown $moment, units=3, past='We are live!', live}
{countdown $moment, now='2026-10-08 12:00', format='j M Y'}
PAD;

  $tagParms   = [
    'moment' => 'The moment counted down to - a date in text, a Unix timestamp or a date object.' ];

  $tagOptions = [
    'units'  => 'How many units are shown, 1 to 4 (days, hours, minutes, seconds), default 2.',
    'past'   => 'The text once the moment has gone, default <code>ended</code>.',
    'live'   => 'Bare option: a small script, once per page, counts down every second from the browser\'s clock.',
    'now'    => 'Count from this moment instead of now.',
    'format' => 'The PHP date format of the full moment in the <code>title</code>, default <code>l j F Y, H:i</code>.' ];

  $tagSee     = [ 'timeago', 'calendar' ];

?>
