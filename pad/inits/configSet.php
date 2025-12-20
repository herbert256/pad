<?php

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
