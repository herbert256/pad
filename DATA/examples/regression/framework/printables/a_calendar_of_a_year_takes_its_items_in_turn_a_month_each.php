<?php

  // year= makes the twelve months of the year, a sheet each: the items of the data are taken in
  // turn - three rows, Ann, Bob and Cy, over January, February, March, then again - and a
  // single tag gives every month the same empty top. The rows a month needs: February 2027
  // begins on a Monday and has four, May and August 2027 six (cal 2027). copies= makes every
  // month that many times, in a row.

  $rows = [ [ 'name' => 'Ann' ], [ 'name' => 'Bob' ], [ 'name' => 'Cy' ] ];

  function calendarYear ( $template ) {

    $out = padUnprotect ( padUnescape ( padCode ( $template ) ) );

    preg_match_all ( '/<section class="pp-sheet".*?<\/section>/s', $out, $sheets );

    return implode ( ', ', array_map ( function ( $sheet ) {
      preg_match ( '/size=12;align=center;valign=middle"><div class="pp-in">(.*?)<\/div>/s', $sheet, $top );
      preg_match ( '/size=22[^"]*"><div class="pp-in"><b>(.*?)<\/b>/', $sheet, $month );
      return trim ( $month [1] ?? '?' ) . ( isset ( $top [1] ) ? ' ' . trim ( $top [1] ) : '' ) . ' ' . ( preg_match_all ( '/data-pdf-line="[0-9.]+ ([0-9.]+) [0-9.]+ \1"/', $sheet ) - 1 ) . ' rows';
    }, $sheets [0] ) );

  }

  $answer = calendarYear ( "{printable:calendar year=2027, data='rows'}{\$name}{/printable:calendar}" )
          . ' // ' . calendarYear ( "{printable:calendar month='2027-05', months=2, copies=2/}" );

?>
