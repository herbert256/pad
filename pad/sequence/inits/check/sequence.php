<?php

  // Re-derives the generated run's build strategy now that the play mode is known: keep,
  // remove and flag all build as 'check', while make takes the type's own make.php if it has
  // one and otherwise falls back through pqBuild()'s usual order.
  //
  // Because that collapses the three onto one strategy, the mode is also kept in
  // $pqCheckPlay, which build/mode.php reads to tell a keep from a remove from a flag.

  $pqBuild     = pqBuild ( $pqSeq, $pqCheck );
  $pqCheckPlay = $pqCheck;

  // A membership check tests every candidate the tag asked for, so the window the type's
  // init.php reshaped - even doubles from and to and steps by 2, multiple and step take the
  // step from their parameter - goes back to what was given. Offered only its own members,
  // flag:even flagged every term and remove:odd removed them all.

  if ( $pqBuild == 'check' ) {
    $pqFrom = $pqInitFrom;
    $pqTo   = $pqInitTo;
    $pqInc  = $pqInitInc;
  }

?>
