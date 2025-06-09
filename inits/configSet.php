<?php

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  include "config/output/$padOutputType.php";

  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include "config/info/$padInfoType.php";
  }
  
  if ( file_exists ( APP . '_config/config.php' ) ) 
    include APP . '_config/config.php';

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  $padSetConfig = [];

?>