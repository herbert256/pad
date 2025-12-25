<?php

  include PAD    . 'config/config.php';
  include PAD    . 'config/sequence.php';
  include COMMON . '_config/config.php';

  if ( $padInfo ) {
    $padInfoList = padExplode ( $padInfo, ',' );
    foreach ( $padInfoList as $padInfoType  )
      include PAD . "config/info/$padInfoType.php";
  }

  if ( file_exists ( APP . '_config/config.php' ) )
    include APP . '_config/config.php';

  if ( php_sapi_name() == 'cli' and $padOutputType == 'web' )
    $padOutputType = 'console';

  include PAD . "config/output/$padOutputType.php";

  if ( file_exists ( APP . '_config/config.php' ) )
    include APP . '_config/config.php';

  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) )
    include_once PAD . 'inits/configSet.php';

  if ( $padErrorTry )
    include PAD . 'config/try.php';

?>