<?php

  // Fires from pad/level/start_end/end1.php just before an @end@ marker is split off the
  // level's base into $padEndBase[$pad], the trailer rendered after the last occurrence.
  //
  // Xref only: records that the page uses the 'end' construct.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'constructs', 'end' );

?>