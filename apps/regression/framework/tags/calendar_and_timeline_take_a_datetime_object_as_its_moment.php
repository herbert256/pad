<?php

  // The most exact date a page can hand over - a DateTimeImmutable - was no text, and its
  // event was left out without a word.

  $agenda = [ [ 'when' => new DateTimeImmutable ( '2026-10-05 10:00', new DateTimeZone ( 'UTC' ) ), 'what' => 'Review' ],
              [ 'when' => '2026-10-07', 'what' => 'Text date' ] ];

?>
