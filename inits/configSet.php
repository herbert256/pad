<?php

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  include "/pad/config/output/$padOutputType.php";

  if ( $padInfo ) 
    include "/pad/config/info/$padInfo.php";

  if ( file_exists ( '/app/_config/config.php' ) ) 
    include '/app/_config/config.php';

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  $padSetConfig = [];

?>