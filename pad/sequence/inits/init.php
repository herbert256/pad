<?php

  // Lets the chosen sequence type adjust the run before building starts, by including its own
  // init.php if it has one - how 'even' doubles from/to and sets increment 2, 'step' and
  // 'multiple' take their increment from the parameter, and 'loop' reads it as a row count.

  // The window as the tag gave it, before the type reshapes it for its own generation -
  // inits/check/sequence.php puts it back when the run turns out to be keep, remove or flag.

  $pqInitFrom = $pqFrom;
  $pqInitTo   = $pqTo;
  $pqInitInc  = $pqInc;

  if ( $pqSeq and file_exists ( PT . "$pqSeq/init.php" ) )
    include PT . "$pqSeq/init.php";

?>