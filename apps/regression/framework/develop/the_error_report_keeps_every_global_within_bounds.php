<?php

  // The JSON error report of a local request carries every global, and a page that died
  // on PHP's time limit answered 31 MB with it: a text, a list and a nesting of any size went
  // in whole. Each now has its bound - a text 4 KB, a list 100 entries, 6 levels - and the
  // page's own small values stay as they are. The token in the long text stands across the
  // cut: redacted before it, nothing of it shows.

  $fwBig    = str_repeat ( 'x', 4090 ) . ' ' . ( $padCsrfIssued = str_repeat ( 'k', 40 ) ) . str_repeat ( 'y', 3000000 );
  $fwList   = range ( 1, 50000 );
  $fwDeep   = [ [ [ [ [ [ [ [ 'end' ] ] ] ] ] ] ] ];
  $fwSmall  = 'hello';

  padClaudeFields ( $fwApp, $fwPad, $fwPhp, $fwSeq );

  $result = strlen ( $fwApp ['fwBig'] ) . ' ' . ( str_contains ( $fwApp ['fwBig'], 'kkkk' ) ? 'token' : 'no-token' ) . ' '
          . count ( $fwApp ['fwList'] ) . ' ' . $fwApp ['fwList'] ['*more*'] . ' '
          . json_encode ( $fwApp ['fwDeep'] ) . ' ' . $fwApp ['fwSmall'] . ' '
          . ( strlen ( json_encode ( [ $fwApp, $fwPad, $fwPhp, $fwSeq ], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR ) ) < 5000000 ? 'bounded' : 'unbounded' );

?>
