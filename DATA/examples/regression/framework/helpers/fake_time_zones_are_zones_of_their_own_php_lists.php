<?php

  // padFake ( 'timezone' ) is a name DateTimeZone takes and lists among its identifiers - and
  // never one of the names tzdb keeps only as a link to another zone now: no Europe/Amsterdam,
  // Europe/Oslo or Asia/Calcutta - every one of its list coming up; the same seed the same zone.

  $listed = DateTimeZone::listIdentifiers ();
  $links  = [ 'Europe/Amsterdam', 'Europe/Copenhagen', 'Europe/Oslo', 'Europe/Stockholm', 'Asia/Calcutta', 'Europe/Kiev', 'US/Eastern' ];

  padFakeSeed ( 30 );

  $zones = $taken = 0;
  $seen  = [];

  for ( $i = 0; $i < 1000; $i++ ) {

    $zone = padFake ( 'timezone' );

    $zones += ( in_array ( $zone, $listed, TRUE ) and ! in_array ( $zone, $links, TRUE ) ) ? 1 : 0;

    try {
      $taken += ( new DateTimeZone ( $zone ) )->getName () === $zone ? 1 : 0;
    } catch ( Exception $error ) {
    }

    $seen [$zone] = TRUE;

  }

  padFakeSeed ( 'z' );  $first  = padFake ( 'timezone' );
  padFakeSeed ( 'z' );  $second = padFake ( 'timezone' );

  $r = json_encode ( [
    'listed' => $zones,
    'taken'  => $taken,
    'zones'  => count ( $seen ),
    'seed'   => $first === $second,
  ] );

?>
