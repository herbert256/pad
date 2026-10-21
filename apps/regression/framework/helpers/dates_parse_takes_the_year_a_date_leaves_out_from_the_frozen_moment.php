<?php

  // A date written without a year - Dec 25, february, last day of february - is in the
  // year of now, and under a frozen clock in the frozen year: PHP's own reading filled it
  // from the real clock, so a test with padNowFreeze answered differently every year and a
  // frozen 2028 had no 29 February. A date without a time is midnight, as it is unfrozen.

  $padTimezone = 'UTC';

  padNowFreeze ( '2028-06-15 12:00:00' );

  $dates = [];

  foreach ( [ 'Dec 25', '25 december', 'february', 'last day of february', 'first monday of january',
              'Dec 25 10:30', 'monday', '2027-01-01' ] as $text )
    $dates [$text] = padDateParse ( $text )->format ( 'Y-m-d H:i' );

  $dates ['ago'] = padAgo ( 'Dec 25' );

  $dates = json_encode ( $dates );

?>
