<?php

  // Membership predicate and generation path for smith: pqBoolSmith($n) is TRUE for a
  // composite n whose digits add up to as much as the digits of its prime factors, each
  // counted as often as it divides n - 4, 22, 27, 58, 85, 94, 121, 166, 202, 265, ...: 22 is
  // 2 * 11, and 2 + 2 = 2 + 1 + 1. A prime is none. The factors come from pqFactors() in
  // _lib/factors.php, the digit sums from digitSum/function.php. The type's only build, so
  // the whole range is filtered through it.

  include_once PQ . '_lib/factors.php';
  include_once PT . 'digitSum/function.php';

  function pqBoolSmith ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 4 )
      return FALSE;

    $n       = (int) $n;
    $factors = pqFactors ( $n );

    if ( array_sum ( $factors ) < 2 )
      return FALSE;

    $sum = 0;

    foreach ( $factors as $prime => $e )
      $sum += $e * pqDigitSum ( $prime );

    return $sum == pqDigitSum ( $n );

  }

?>
