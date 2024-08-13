<?php

  set_error_handler ( 
    function ( $errno, $errstr, $errfile, $errline ) {
      throw new ErrorException ( $errstr, $errno, 0, $errfile, $errline);
    }
  );

  try {

    include pad . 'config/config.php';
    
    if ( file_exists ( padApp . '_config/config.php' ) ) 
      include padApp . '_config/config.php';

    include pad . "config/output/$padOutputType.php";
    
    if ( $padInfo ) 
      include pad . "config/info/$padInfo.php";

    if ( file_exists ( padApp . '_config/config.php' ) ) 
      include padApp . '_config/config.php';
    
    if ( isset ( $padSetConfig ) and count ( $padSetConfig ) ) 
      include_once pad . 'inits/configSet.php';

  } catch ( Throwable $e ) {

    echo $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

    exit;

  }

  restore_error_handler ();

?>