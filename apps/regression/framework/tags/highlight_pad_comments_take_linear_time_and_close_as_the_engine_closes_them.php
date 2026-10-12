<?php

  // Fifty thousand comment openers that never close - a value piped through
  // highlight('pad'): each scanned on to the end of the text, a second or two. And a comment
  // read as the engine reads it: {#title} is the option sigil, no opener.

  $start  = hrtime ( TRUE );
  $hashes = strlen ( padHighlightTokens ( str_repeat ( '{#', 50000 ), 'pad' ) );
  $dashes = strlen ( padHighlightTokens ( str_repeat ( '{-- ', 50000 ), 'pad' ) );
  $time   = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';

  $sample = padHighlightTokens ( "<h2>{#title}</h2> {# a note #} {-- {-- one --} '{-- x'", 'pad' );

?>
