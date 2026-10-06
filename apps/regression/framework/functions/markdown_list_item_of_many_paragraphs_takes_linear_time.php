<?php

  // One list item of 40000 paragraphs: every line after a blank one joined the whole item
  // so far again to ask whether it held any text - seconds for 200 kB.

  $start = hrtime ( TRUE );
  $html  = padMarkdown ( "- a\n" . str_repeat ( "\n  b\n", 40000 ) . "- c" );
  $time  = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';
  $tail  = str_replace ( "\n", " ", substr ( $html, -45 ) );

?>
