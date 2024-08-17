<?php

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  include "/pad/config/output/$padOutputType.php";

  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include "/pad/config/info/$padInfoType.php";
  }
  
  if ( file_exists ( '/app/_config/config.php' ) ) 
    include '/app/_config/config.php';

  foreach ( $padSetConfig as $padK => $padV )
    $GLOBALS ["pad$padK"] = $padV;

  $padSetConfig = [];

?>