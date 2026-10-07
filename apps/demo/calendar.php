<?php

  $title = 'Calendar';

  // The agenda of the month on show - the one the calendar's links ask for, else this
  // one: a stand-up every Monday, payday on the 25th, drinks on the last Friday and a
  // release on the second Tuesday.

  $shown = padCalendarMonth ( $_GET ['month'] ?? '' ) ?? padNow ( 'Y-m' );
  $first = new DateTimeImmutable ( "$shown-01" );

  $agenda = [];

  for ( $day = $first; $day->format ( 'Y-m' ) == $shown; $day = $day->modify ( '+1 day' ) )
    if ( $day->format ( 'N' ) == 1 )
      $agenda [] = [ 'when' => $day->format ( 'Y-m-d' ), 'what' => 'Stand-up' ];

  $agenda [] = [ 'when' => $first->modify ( 'second tuesday of this month' )->format ( 'Y-m-d' ), 'what' => 'Release' ];
  $agenda [] = [ 'when' => "$shown-25",                                                         'what' => 'Payday' ];
  $agenda [] = [ 'when' => $first->modify ( 'last friday of this month' )->format ( 'Y-m-d' ),   'what' => 'Drinks' ];

?>
