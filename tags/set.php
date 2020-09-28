<?php

  if ( $pad_single ) {

    foreach ( $pad_parms_tag as $pad_k => $pad_v ) {
      if ( isset ( $GLOBALS [$pad_k] ) )
        unset ( $GLOBALS [$pad_k] );
      $GLOBALS [$pad_k] = $pad_v;
  
    return NULL;

  } else {

    if ( $pad_walk == 'start' ) {

      $pad_walk = 'end';

    } else {

    }

    return TRUE;
    
  }
  
  
  


  $pad_tag_parms = FALSE;
  
  foreach ( $pad_parms_tag as $pad_k => $pad_v ) {
    if ( isset ( $GLOBALS [$pad_k] ) )
      unset ( $GLOBALS [$pad_k] );
    $GLOBALS [$pad_k] = $pad_v;
  }


?>