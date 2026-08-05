<?php

  // Fires from pad/level/split.php and pad/build/split.php the moment an @else@ marker at
  // the right nesting depth is found and the base is cut into its true and false halves.
  //
  // Xref only: records that the page uses the 'else' construct.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'constructs', 'else' );

?>