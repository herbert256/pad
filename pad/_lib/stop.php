<?php


  function padStop ( $stop ) {

    padCloseSession ();  

    padEmptyBuffers ();

    if ( $GLOBALS ['padOutputType'] == 'web' )
      padWebHeaders ( $stop );

    padExit ();

  }


  function padCloseSession () {

    if ( ! isset($GLOBALS ['padSessionStarted']) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }


  function padExit ( $noInfo = FALSE ) { 

    if ( $GLOBALS ['padInfo'] and ! $noInfo )
      include 'info/end/config.php';  

    $GLOBALS ['padSkipShutdown'] = $GLOBALS ['padSkipBootShutdown'] = TRUE;

    exit;

  }

  
?>