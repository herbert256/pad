<?php


  function padCloseSession () {

    if ( ! isset($GLOBALS ['padSessionStarted']) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }


  function padExit ( $stop = 200 ) { 

    padCloseSession ();  

    padEmptyBuffers ( $padIgnored );

    if ( $GLOBALS ['padOutputType'] == 'web' )
      padWebHeaders ( $stop );

    if ( isset ( $GLOBALS ['padInfoStarted'] ) )
      include 'info/end/config.php';  

    include 'exits/exit.php'; 

  }

  
?>