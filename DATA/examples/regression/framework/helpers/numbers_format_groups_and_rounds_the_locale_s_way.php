<?php

  // padNumberFormat: exactly so many decimals, half rounded up, grouped the way of the
  // request's locale or the one given; nothing to write - NULL, FALSE, empty text - is ''.

  $r = json_encode ( [
    padNumberFormat ( 1234567.891, 2 ),
    padNumberFormat ( 1234567.891, 2, 'nl' ),
    padNumberFormat ( 1234567.891, 2, 'de_DE' ),
    padNumberFormat ( 0.5 ),
    padNumberFormat ( 2.5 ),
    padNumberFormat ( 1.005, 2 ),
    padNumberFormat ( -0.001, 2 ),
    padNumberFormat ( -1234.5, 1 ),
    padNumberFormat ( '1234' ),
    padNumberFormat ( 12, '2' ),
    padNumberFormat ( 0 ),
    padNumberFormat ( '0', 1 ),
    padNumberFormat ( PHP_INT_MAX ),
    padNumberFormat ( NULL ),
    padNumberFormat ( '' ),
    padNumberFormat ( FALSE )
  ] );

  $padLocale = 'nl';

  $nl = padNumberFormat ( 1234.5, 2 );

?>
