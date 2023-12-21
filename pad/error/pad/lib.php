<?php
  
  
  include pad . 'error/pad/set.php';
  include pad . 'error/pad/go.php';
  include pad . 'error/pad/log.php';
  include pad . 'error/pad/file.php';
  include pad . 'error/pad/console.php';
  include pad . 'error/pad/stop.php';
  include pad . 'error/pad/exit.php';


  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    return padErrorGo ( 'PAD: ' . $error, $file, $line ); 
 
  }


  function padErrorCheck ( $type, $info ) {

    $md5 = md5 ( trim($info) );

    if ( isset ( $GLOBALS["padErrorCheck_$type"] ) and isset ( $GLOBALS["padErrorCheck_$type"] [$md5] ) )
      return FALSE;

    $GLOBALS["padErrorCheck_$type"] [$md5] = TRUE;

    return TRUE;

  }


?>