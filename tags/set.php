<?php

  if ( $pad_pair )
    return TRUE;

  $pad_tag_parms = FALSE;
  
  foreach ( $pad_parms_app as $pad_k => $pad_v ) {
    if ( isset ( $GLOBALS [$pad_k] ) )
      unset ( $GLOBALS [$pad_k] );
    $GLOBALS [$pad_k] = $pad_v;
  }

  return NULL;

?>