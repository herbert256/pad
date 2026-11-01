<?php

  // isbn - An ISBN-13 whose check digit holds: 978, nine digits and the check digit the ISBN
  // Users' Manual sets - the first twelve digits weighed 1, 3, 1, 3 ..., the check what brings
  // their sum to a multiple of ten - written without hyphens, 9784198514389, as where the
  // hyphens go hangs on the ranges the ISBN agencies hand out. A number of the right shape
  // for a catalogue or a form that checks it, made up: it may be a real book's or no one's.

  $digits = '978';

  for ( $i = 0; $i < 9; $i++ )
    $digits .= padFakeNumber ( 0, 9 );

  $sum = 0;

  foreach ( str_split ( $digits ) as $at => $digit )
    $sum += (int) $digit * ( $at % 2 ? 3 : 1 );

  return $digits . ( ( 10 - $sum % 10 ) % 10 );

?>
