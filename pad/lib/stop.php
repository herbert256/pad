<?php


  function padStop ( $stop ) {

    padCloseSession ();  

    $output = padEmptyBuffers ();

    if ( trim($output) )
      return padError ( "Illegal output: '$output'" );

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

    flush();

    if ( padTail )
      include pad . 'tail/events/exit.php';  

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;
    
    exit;

  }

  
?>