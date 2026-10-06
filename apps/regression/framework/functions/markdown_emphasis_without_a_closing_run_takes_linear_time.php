<?php

  // 160 kB of opening runs that nothing closes: each emphasis pattern scanned from every
  // run to the end of the text, seconds of work for a comment a visitor can post.

  $start = hrtime ( TRUE );
  $html  = padMarkdown ( str_repeat ( '**a ', 40000 ) . "\n\n*b* __c__" );
  $time  = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';
  $tail  = substr ( $html, strrpos ( $html, '<p>' ) );

?>
