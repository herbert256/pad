<?php

  // 200 kB of backtick runs of lengths no later run repeats: the code span pattern scanned
  // from every backtick of every run to the end of the text. A run is a whole run - ``` has
  // no closing ``, as CommonMark has it - so ```a`` stays text.

  $runs = '';

  for ( $length = 1; strlen ( $runs ) < 200000; $length += 2 )
    $runs .= str_repeat ( '`', $length ) . ' a ';

  $start = hrtime ( TRUE );
  $html  = padMarkdown ( $runs . "\n\n```a`` and `b`" );
  $time  = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';
  $tail  = substr ( $html, strrpos ( $html, '<p>' ) );

?>
