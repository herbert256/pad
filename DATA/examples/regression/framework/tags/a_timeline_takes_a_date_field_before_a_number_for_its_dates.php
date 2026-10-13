<?php

  // Rows as a database gives them - an id first - put their events on the date field when
  // no date= names it: a bare small number reads as a year only when no field reads as a
  // date, where the id was taken and the events were drawn in the years 0001 and 0002.

  $ev    = [ [ 'id' => 1, 'when' => '2026-10-01', 'what' => 'Launch' ], [ 'id' => 2, 'when' => '2026-11-15', 'what' => 'Review' ] ];
  $years = [ [ 'year' => 1969, 'what' => 'Moon' ], [ 'year' => 1989, 'what' => 'Wall' ] ];

?>
