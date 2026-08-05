<?php

  // Re-derives the generated run's build strategy now that the play mode is known: keep,
  // remove and flag all build as 'check', while make takes the type's own make.php if it has
  // one and otherwise falls back through pqBuild()'s usual order.

  $pqBuild = pqBuild ( $pqSeq, $pqCheck );

?>