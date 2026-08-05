<?php

  // Closes an occurrence: appends the row's rendered output to the level's result and
  // drops the row's variables again, restoring anything they shadowed, so the level is
  // ready for the next row.

  $padResult [$pad] .= $padOut [$pad];

  if ( $padInfo )
    include PAD . 'events/occurEnd.php';

  padResetOcc ($pad);

?>