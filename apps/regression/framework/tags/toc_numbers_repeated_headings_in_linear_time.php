<?php

  // A long listing whose every card has the same heading: each counted up from -2 through
  // every id its text had taken already - twenty thousand took eight seconds.

  include_once PAD . 'tags/_lib/toc.php';

  $page  = padTocMarker ( [ 2 ], '', 'ol' ) . str_repeat ( '<h2>Same</h2>', 20000 );
  $start = hrtime ( TRUE );
  $out   = padTocFill ( $page );
  $time  = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';
  $last  = substr_count ( $out, '<h2 id="same-20000">' ) . substr_count ( $out, 'href="#same-19999"' );

?>
