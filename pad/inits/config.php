<?php

  include 'config/config.php';
  include 'config/sequence.php';
 
  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include "config/info/$padInfoType.php";
  }

  if ( file_exists ( APP . '_config/config.php' ) ) 
    include APP . '_config/config.php';

  if ( php_sapi_name() == 'cli' and $padOutputType == 'web' )
    $padOutputType = 'console';

  include "config/output/$padOutputType.php";
  
  if ( file_exists ( APP . '_config/config.php' ) ) 
    include APP . '_config/config.php';
  
  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) ) 
    include_once 'inits/configSet.php';

?>