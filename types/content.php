<?php

  if ( $pad_walk == 'start') { 

    if ( isset ($pad_content_name [$pad_lvl]) )
      unset ( $pad_content_name [$pad_lvl] );

    if ( isset($pad_parms_val[0]) ) {

      if ( isset ( $GLOBALS [$pad_name] ) )
        $pad_content_name [$pad_lvl] = $GLOBALS [$pad_name];

      $GLOBALS [$pad_name] = $pad_parms_val[0];

      $pad_walk = 'end';
   
    }

    return $pad_content_store [$pad_name];

  }

  if ( isset ($pad_content_name [$pad_lvl]) )
    $GLOBALS [$pad_name] = $pad_content_name [$pad_lvl];
  else
    unset ( $GLOBALS [$pad_name] );

  return TRUE;

?>