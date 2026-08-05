<?php

  // First half of the negative option: prefixes every key of the tag's data set with 'x'
  // and keeps a copy of the renamed set in $padHandOld.
  //
  // Included by handling/handling.php just before a handler runs, with negative/exits.php
  // just after. The handler works on the renamed set; the copy lets exits.php tell which
  // rows it selected from the ones it dropped.

  $padHandOld     = $padData [$pad];
  $padData [$pad] = [];

  foreach ( $padHandOld as $padK => $padV )
    $padData [$pad] [ 'x' . $padK ] = $padV;

  $padHandOld = $padData [$pad];

?>