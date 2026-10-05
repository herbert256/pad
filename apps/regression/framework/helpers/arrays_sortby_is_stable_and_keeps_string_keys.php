<?php

  $people = [
    [ 'id' => 3, 'name' => 'Cleo', 'age' => '41' ],
    [ 'id' => 1, 'name' => 'Ann',  'age' => 30 ],
    [ 'id' => 2, 'name' => 'Bob',  'age' => NULL ]
  ];

  $teams = [ [ 'g' => 1, 'n' => 'a' ], [ 'g' => 0, 'n' => 'b' ], [ 'g' => 1, 'n' => 'c' ], [ 'g' => 0, 'n' => 'd' ] ];

  $r = json_encode ( [
    'byName'     => padArrPluck ( padArrSortBy ( $people, 'name' ), 'name' ),
    'byAge'      => padArrPluck ( padArrSortBy ( $people, 'age' ), 'name' ),
    'descending' => padArrPluck ( padArrSortBy ( $people, 'id', TRUE ), 'id' ),
    'desc'       => padArrPluck ( padArrSortBy ( $people, 'id', 'desc' ), 'id' ),
    'asc'        => padArrPluck ( padArrSortBy ( $people, 'id', 'asc' ), 'id' ),
    'numbers'    => padArrSortBy ( [ '10', '9', 9.5 ], NULL ),
    'stable'     => padArrPluck ( padArrSortBy ( $teams, 'g' ), 'n' ),
    'stableDesc' => padArrPluck ( padArrSortBy ( $teams, 'g', TRUE ), 'n' ),
    'keysKept'   => padArrSortBy ( [ 'x' => 3, 'y' => 1, 'z' => 2 ], NULL ),
    'listAgain'  => padArrSortBy ( [ 3, 1, 2 ], NULL ),
    'callback'   => padArrSortBy ( [ 'bb', 'a', 'ccc' ], fn ( $value ) => strlen ( $value ) ),
    'nothing'    => padArrSortBy ( NULL, 'id' )
  ] );

?>
