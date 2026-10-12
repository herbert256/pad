<?php

  // One list item of 40000 paragraphs: every line after a blank one joined the whole item
  // so far again to ask whether it held any text - five seconds for 200 kB. It takes a
  // tenth of one now; the budget leaves room for a loaded machine, where one second failed
  // the case now and then.

  $start = hrtime ( TRUE );
  $html  = padMarkdown ( "- a\n" . str_repeat ( "\n  b\n", 40000 ) . "- c" );
  $time  = ( hrtime ( TRUE ) - $start < 2.5e9 ) ? 'in time' : 'too slow';
  $tail  = str_replace ( "\n", " ", substr ( $html, -45 ) );

?>
