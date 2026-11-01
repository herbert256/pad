<?php

  // price - An amount from from= to to= - 1 to 100 unless said otherwise - with two
  // decimals always written, 12.50, the way a price list shows it: a text, so the trailing
  // zero stays.

  $price = padFakeFloat ( padFakeOpt ( 'from', 1 ), padFakeOpt ( 'to', 100 ), 2 );

  return number_format ( $price, 2, '.', '' );

?>
