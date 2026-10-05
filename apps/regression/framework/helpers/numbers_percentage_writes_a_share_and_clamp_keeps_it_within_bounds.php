<?php

  // padNumberPercentage: a share in hundreds, exactly precision decimals, the locale's way.
  // padNumberClamp: the number, or the bound it went past; NULL for nothing.

  $percent = json_encode ( [
    padNumberPercentage ( 25 ),
    padNumberPercentage ( 25.55, 1 ),
    padNumberPercentage ( 12.5, 2 ),
    padNumberPercentage ( 0 ),
    padNumberPercentage ( 100 ),
    padNumberPercentage ( -5.5 ),
    padNumberPercentage ( -0.2 ),
    padNumberPercentage ( '1234.5' ),
    padNumberPercentage ( NULL )
  ] );

  $clamp = json_encode ( [
    padNumberClamp ( 5, 1, 10 ),
    padNumberClamp ( -3, 0, 10 ),
    padNumberClamp ( 15, 0, 10 ),
    padNumberClamp ( 2.5, 0, 1.5 ),
    padNumberClamp ( '7', 0, 10 ),
    padNumberClamp ( 0, '1', '2' ),
    padNumberClamp ( 5, 5, 5 ),
    padNumberClamp ( NULL, 0, 1 ),
    padNumberClamp ( '', 0, 1 )
  ] );

  $padLocale = 'nl';

  $nl = padNumberPercentage ( 25.55, 1 );

?>
