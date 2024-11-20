<?php

  include 'config/config.php';
 
  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include "config/info/$padInfoType.php";
  }

  if ( file_exists ( APP . '_config/config.php' ) ) 
    include APP . '_config/config.php';

  include "config/output/$padOutputType.php";
  
  if ( file_exists ( APP . '_config/config.php' ) ) 
    include APP . '_config/config.php';
  
  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) ) 
    include_once PAD . 'inits/configSet.php';

?>