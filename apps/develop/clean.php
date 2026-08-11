<?php

  // Guarded twice over: reachable as ?clean&go=1 from the index, and included by the
  // build page, whose own goBuild stands in for the go.

  if ( ! isset ( $go ) and ! isset ( $goBuild ) )
    return;

  trimFiles ( $padHome );

?>