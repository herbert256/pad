<?php

  function padErrorGo ( $error, $file, $line ) {
   
    if ( $GLOBALS ['padCatch'] )
      throw new ErrorException ( $error, 0, 0, $file, $line );
    
    error_log ( "$file:$line $error", 4 );
 
  }

?>