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


  function padExit () { 

    if ( padInfo )
      include pad . 'info/events/end.php';  

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

    exit;

  }

  
?>