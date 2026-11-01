<?php

  // A calendar month read back from its markup for the cases of this group: per sheet the
  // month, the weekdays over the columns, the week numbers, the days - the first and the
  // marked ones with where they stand - the other months' days, and the lines of the grid.

  function calendarShow ( $template ) {

    $out  = padUnprotect ( padUnescape ( padCode ( $template ) ) );
    $show = [];

    if ( preg_match ( '/<span class="pp-about">([^<]*)</', $out, $m ) )
      $show [] = html_entity_decode ( $m [1] );

    foreach ( preg_split ( '/(?=<section class="pp-sheet")/', $out ) as $sheet ) {

      if ( ! preg_match ( '/^<section class="pp-sheet" data-pdf-sheet="([^"]*)"/', $sheet, $m ) )
        continue;

      preg_match_all ( '/data-pdf-box="([^"]*)" data-pdf-style="([^"]*)"><div class="pp-in">(.*?)<\/div><\/div>/s', $sheet, $boxes, PREG_SET_ORDER );

      $text  = fn ( $b ) => trim ( preg_replace ( '/\s+/', ' ', html_entity_decode ( strip_tags ( $b [3] ) ) ) );
      $size  = fn ( $b ) => preg_match ( '/size=([0-9.]+)/', $b [2], $s ) ? (float) $s [1] : 0;
      $color = fn ( $b ) => preg_match ( '/color=(#[0-9a-f]+)/', $b [2], $c ) ? $c [1] : '';
      $days  = array_values ( array_filter ( $boxes, fn ( $b ) => preg_match ( '/^\d+$/D', $text ( $b ) ) and $color ( $b ) !== '#8a8984' ) );
      $weeks = array_values ( array_filter ( $boxes, fn ( $b ) => $color ( $b ) === '#8a8984' ) );
      $names = array_values ( array_filter ( $boxes, fn ( $b ) => $size ( $b ) > 0 and $size ( $b ) < 7 and ! in_array ( $color ( $b ), [ '#555555', '#8a8984' ] ) ) );
      $heads = array_values ( array_filter ( $boxes, fn ( $b ) => $color ( $b ) === '#555555' ) );
      $title = array_values ( array_filter ( $boxes, fn ( $b ) => $size ( $b ) >= 14 ) );
      $other = preg_match_all ( '/data-pdf-rect="[^"]*" data-pdf-style="fill=#f1f0eb/', $sheet );
      $lines = preg_match_all ( '/data-pdf-line="/', $sheet );
      $marks = array_filter ( $days, fn ( $b ) => $color ( $b ) !== '#1f1f1d' );

      $show [] = "sheet {$m [1]}: " . implode ( ' | ', array_map ( $text, $title ) )
               . ' // ' . implode ( ' ', array_map ( $text, $heads ) )
               . ( $weeks ? ' // weeks ' . implode ( ' ', array_map ( $text, $weeks ) ) : '' )
               . ' // ' . count ( $days ) . ' days, the 1st at ' . implode ( ' ', array_slice ( explode ( ' ', $days [0] [1] ?? '' ), 0, 2 ) )
               . ', marked ' . implode ( ', ', array_map ( fn ( $b ) => $text ( $b ) . ' ' . $color ( $b ), $marks ) )
               . ( $names ? ' // ' . implode ( ', ', array_map ( fn ( $b ) => $text ( $b ) . ' ' . $color ( $b ), $names ) ) : '' )
               . " // $other of other months, $lines lines";

    }

    return implode ( ' ## ', $show );

  }

?>
