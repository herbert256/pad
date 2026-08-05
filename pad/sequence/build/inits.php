<?php

  // Picks the build strategy when the tag did not fix one: 'pull' for a stored sequence,
  // otherwise whatever pqBuild() infers from the files in the type's directory, preferring
  // its loop.php. Included by sequence/inits/inits.php, after $pqSeq and $pqPull are known.

      if ( $pqPull    ) $pqBuild = 'pull';
  elseif ( ! $pqBuild ) $pqBuild = pqBuild ( $pqSeq, 'loop' );

?>