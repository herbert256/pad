<?php

  if ( function_exists ('padInfoTrace') )
    if ( $GLOBALS ['padInfoTrace'] ) 
      if ( $GLOBALS ['padInfoTraceGet'] )
         padInfoTrace ( 'file', 'get', $in );
   
?>