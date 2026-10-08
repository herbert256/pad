<?php

  padFakeSeed ( 1 );

  $numbers = $floats = $dates = [];
  $trues   = 0;

  for ( $i = 0; $i < 500; $i++ ) {
    $numbers [] = padFakeNumber ( -3, 3 );
    $floats  [] = padFakeFloat ( 1.5, 2.5, 1 );
    $dates   [] = padFakeDate ( '2026-01-01', '2026-01-31' );
    $trues     += padFakeBool ( 20 ) ? 1 : 0;
  }

  padNowFreeze ( '2026-06-15 12:00:00' );

  $relative = padFakeDate ( '-1 week', 'now', 'Y-m-d H:i' );

  padNowFreeze ();

  $r = json_encode ( [
    'numbers'   => [ min ( $numbers ), max ( $numbers ), count ( array_unique ( $numbers ) ) ],
    'floats'    => [ min ( $floats ), max ( $floats ) ],
    'dates'     => [ min ( $dates ), max ( $dates ) ],
    'aFifth'    => $trues > 70 and $trues < 130,
    'never'     => padFakeBool ( 0 ),
    'always'    => padFakeBool ( 100 ),
    'relative'  => $relative >= '2026-06-08 12:00' and $relative <= '2026-06-15 12:00',
    'oneValue'  => padFakeNumber ( 9, 9 ),
    'wide'      => padFakeNumber ( 0, PHP_INT_MAX ) >= 0,
  ] );

?>
