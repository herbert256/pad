<?php

  // glasbey, a categorical palette of 32: without steps its 32 colours - the first 32 of
  // colorcet's glasbey_bw_minc_20, their md5 made from colorcet's table apart from PAD - its
  // last, and the 33rd of 33 steps its first again.

  $all   = padPalette ( 'glasbey' );
  $count = count ( $all );
  $last  = $all [31];
  $again = padPalette ( 'glasbey', 33 ) [32];
  $md5   = md5 ( implode ( ' ', $all ) );

?>
