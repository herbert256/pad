<?php

  // Five years of ice cream, in thousands sold per month: every summer a peak, every year
  // a little more, and the wet summer of 2023 a dent in the pattern.

  $icecream = [];
  $months   = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
  $season   = [ 0.15, 0.2, 0.35, 0.55, 0.8, 1.0, 1.15, 1.1, 0.7, 0.4, 0.2, 0.3 ];

  foreach ( range ( 2021, 2025 ) as $y => $year )
    foreach ( $months as $m => $month ) {

      $sales = 100 * ( 1 + 0.08 * $y ) * $season [$m];

      if ( $year == 2023 and $m >= 5 and $m <= 7 )
        $sales *= 0.7;

      $icecream [] = [ 'year' => $year, 'month' => $month, 'sales' => (int) round ( $sales ) ];

    }

?>
