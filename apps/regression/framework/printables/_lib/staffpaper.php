<?php

  // The staves of staff paper read back from its lines: per sheet how many staves of how many
  // lines a space apart, how high, from where to where, and the lines that join a grand
  // staff.

  function staffShow ( $template ) {

    $out  = padUnprotect ( padUnescape ( padCode ( $template ) ) );
    $show = [];

    foreach ( preg_split ( '/(?=<section class="pp-sheet")/', $out ) as $sheet ) {

      if ( ! preg_match ( '/^<section class="pp-sheet" data-pdf-sheet="([^"]*)"/', $sheet, $m ) )
        continue;

      preg_match_all ( '/data-pdf-line="([0-9.]+) ([0-9.]+) ([0-9.]+) ([0-9.]+)"/', $sheet, $lines, PREG_SET_ORDER );

      $across = array_values ( array_filter ( $lines, fn ( $l ) => $l [2] == $l [4] ) );
      $joins  = array_values ( array_filter ( $lines, fn ( $l ) => $l [1] == $l [3] ) );
      $staves = [];

      foreach ( $across as $i => $l )
        if ( $i == 0 or (float) $l [2] - (float) $across [ $i - 1 ] [2] > 3 )
          $staves [] = [ (float) $l [2] ];
        else
          $staves [ count ( $staves ) - 1 ] [] = (float) $l [2];

      $sizes = array_count_values ( array_map ( fn ( $s ) => count ( $s ) . ' lines ' . padPrintableMm ( end ( $s ) - $s [0] ) . ' mm', $staves ) );

      $show [] = "sheet {$m [1]}: " . count ( $staves ) . ( count ( $staves ) == 1 ? ' staff of ' : ' staves of ' ) . implode ( ', ', array_keys ( $sizes ) )
               . ', from ' . ( $across [0] [2] ?? '?' ) . ' to ' . ( end ( $across ) [2] ?? '?' ) . ' mm, ' . ( $across [0] [1] ?? '?' ) . ' to ' . ( $across [0] [3] ?? '?' )
               . ( $joins ? ', ' . count ( $joins ) . ' joined, the first ' . $joins [0] [2] . ' to ' . $joins [0] [4] : '' );

    }

    return implode ( ' // ', $show );

  }

?>
