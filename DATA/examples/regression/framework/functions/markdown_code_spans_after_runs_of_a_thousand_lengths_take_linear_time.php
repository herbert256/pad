<?php

  // Backtick runs of every length from 1 to 1000 that nothing closes, then 480 kB of text:
  // the pattern scanned from each run to the end of the text, over a second of work, where
  // the runs listed once by length pair in one pass. A span after them still closes.

  $runs = '';

  for ( $length = 1; $length <= 1000; $length++ )
    $runs .= str_repeat ( '`', $length ) . ' ';

  $start = hrtime ( TRUE );
  $html  = padMarkdown ( $runs . str_repeat ( 'b ', 240000 ) . "\n\n`x` and ``y``" );
  $time  = ( hrtime ( TRUE ) - $start < 5e8 ) ? 'in time' : 'too slow';
  $tail  = substr ( $html, strrpos ( $html, '<p>' ) );

?>
