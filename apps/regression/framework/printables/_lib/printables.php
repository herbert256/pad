<?php

  // What a printable drew, read back from its markup for the cases of this group: the line of
  // its bar, then per sheet its size and, in the order they stand, its boxes - place, style
  // and text - its rectangles and lines, and what the screen alone shows, at most $most
  // things a sheet; ' // ' between them, as a case's answer is one line.

  function printablesShow ( $template, $most = 8 ) {

    $out = padUnprotect ( padUnescape ( padCode ( $template ) ) );

    return printablesRead ( $out, $most );

  }

  function printablesRead ( $out, $most = 8 ) {

    $show = [];

    if ( preg_match ( '/<span class="pp-about">([^<]*)</', $out, $m ) )
      $show [] = html_entity_decode ( $m [1] );

    foreach ( preg_split ( '/(?=<section class="pp-sheet")/', $out ) as $k => $sheet ) {

      if ( ! preg_match ( '/^<section class="pp-sheet" data-pdf-sheet="([^"]*)"/', $sheet, $m ) )
        continue;

      $show [] = "sheet {$m [1]}";
      $seen    = 0;

      preg_match_all ( '/<div class="(pp-box|pp-rect|pp-line)([^"]*)"( data-pdf-skip)?(?: data-pdf-(?:box|rect|line)="([^"]*)")?(?: data-pdf-style="([^"]*)")?[^>]*>(?:<div class="pp-in">(.*?)<\/div><\/div>)?/s',
                       $sheet, $all, PREG_SET_ORDER );

      foreach ( $all as $one ) {
        if ( ++$seen > $most ) {
          $show [] = ( count ( $all ) - $most ) . ' more';
          break;
        }
        $text = trim ( preg_replace ( '/\s+/', ' ', strip_tags ( preg_replace ( [ '/<br\s*\/?>/', '/</' ], [ ' | ', ' <' ], $one [6] ?? '' ) ) ) );
        $show [] = substr ( $one [1], 3 ) . ( $one [3] ? ' screen' : ' ' . $one [4] ) . ( ( $one [5] ?? '' ) !== '' ? " [{$one [5]}]" : '' )
                 . ( $text !== '' ? ' ' . mb_substr ( html_entity_decode ( $text ), 0, 40 ) : '' )
                 . ( str_contains ( $one [2], 'pp-turn' ) ? ' turned' : '' );
      }

    }

    return implode ( ' // ', $show );

  }

?>
