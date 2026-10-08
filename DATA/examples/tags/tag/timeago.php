<?php

  $tagAbout   = 'How long ago a moment was, in words, inside a time element that keeps the exact moment as its datetime and title.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{timeago $moment}
{timeago $moment, now='2026-10-08 12:00', format='j M Y'}
PAD;

  $tagParms   = [
    'moment' => 'A Unix timestamp, a date in text or a date object. An empty moment writes nothing.' ];

  $tagOptions = [
    'now'    => 'Count from this moment instead of now.',
    'format' => 'The PHP date format of the full moment in the <code>title</code>, default <code>l j F Y, H:i</code>.' ];

  $tagSee     = [ 'countdown', 'calendar', 'echo' ];

?>
