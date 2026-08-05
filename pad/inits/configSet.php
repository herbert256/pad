<?php

  // Applies settings queued in $padSetConfig, overriding what the config files decided.
  //
  // A queue entry 'OutputType' => 'web' becomes the global $padOutputType; exits/output/file.php
  // uses this to tell the page it restarts into that the result should go to the browser
  // this time. Reached from the tail of inits/config.php whenever the queue is not empty.
  //
  // It repeats the output and $padInfo selector includes and the application config, because
  // the overrides may have changed which of those apply, then re-applies the queue so it
  // still wins, and finally empties it.

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  include PAD . "config/output/$padOutputType.php";

  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include PAD . "config/info/$padInfoType.php";
  }

  if ( file_exists ( APP . '_config/config.php' ) )
    include APP . '_config/config.php';

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  $padSetConfig = [];

?>