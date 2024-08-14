<?php
  
  function padErrorGo ( $error, $file, $line ) {
 
    if ( $GLOBALS ['padCatch'] )
      throw new ErrorException ( $error, 0, 0, $file, $line );

    trigger_error ( "$file:$line $error", E_USER_ERROR );
      
    return FALSE;

  }

?>