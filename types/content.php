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

    $pad_content_store_data = $pad_content_store [$pad_name];

    if ( strpos ( $pad_content_store_data, '{@content}' ) !== FALSE ) {
      $pad_content_store_data = str_replace('{@content}', $pad_content, $pad_content_store_data);
      $pad_content = '';
    }

    return $pad_content_store_data;

  }

  if ( isset ($pad_content_name [$pad_lvl]) )
    $GLOBALS [$pad_name] = $pad_content_name [$pad_lvl];
  else
    unset ( $GLOBALS [$pad_name] );

  return TRUE;

?>