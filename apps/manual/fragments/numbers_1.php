<?php

  // An order total written for the visitor - in the request's locale, then in two others -
  // and a share of the sales.

  $total = 1234567.891;

  $en = padNumberFormat ( $total, 2 );
  $nl = padNumberFormat ( $total, 2, 'nl' );
  $de = padNumberFormat ( $total, 2, 'de_DE' );

  $share = padNumberPercentage ( 25.55, 1 );

?>
