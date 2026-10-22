<?php

  // PHP compares NULL with a number as two booleans - NULL equals 0 and lies below -1 -
  // which is no order: uasort then left the numbers themselves out of order. NULL comes
  // first now, as MySQL's ORDER BY puts it, last when descending.

  $rows = [ [ 'n' => 'a', 'v' => 0 ], [ 'n' => 'b', 'v' => NULL ], [ 'n' => 'c', 'v' => -1 ], [ 'n' => 'd', 'v' => 5 ] ];

  $r = json_encode ( [
    'ascending'  => padArrPluck ( padArrSortBy ( $rows, 'v' ), 'n' ),
    'descending' => padArrPluck ( padArrSortBy ( $rows, 'v', 'desc' ), 'n' ),
    'values'     => padArrSortBy ( [ -5, NULL, 3, -2, 0 ], NULL, TRUE ),
    'twoNulls'   => padArrSortBy ( [ 'x' => NULL, 'y' => 1, 'z' => NULL, 'w' => 0 ], NULL ),
    'emptyText'  => padArrSortBy ( [ 'b', '', NULL, 'a' ], NULL )
  ] );

?>
