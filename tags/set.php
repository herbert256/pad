<?php

  if ( $pad_single) {

    foreach ( $pad_parms_set as $pad_k => $pad_v ) {
 
      if ( isset ( $GLOBALS [$pad_k] ) )
        unset ( $GLOBALS [$pad_k] );
 
      $GLOBALS [$pad_k] = $pad_v;
 
    }

    return NULL;
 
  } 

  return TRUE;

?>