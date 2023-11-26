<?php

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  include pad . "config/output/$padOutputType.php";

  if ( $padTail ) 
    include pad . "config/tail/$padTail.php";

  if ( file_exists ( padApp . '_config/config.php' ) ) 
    include padApp . '_config/config.php';

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  $padSetConfig = [];

?>