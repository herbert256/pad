<?php


  function pCallback_before_xxx ($padCallback_type) {

    $padVars_before = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( pValid_store ($padK) ) { 
        $padVars_before [] = $padK;
        $$padK = $padV;
      }

    include PAD . "callback/$padCallback_type.php";

    $padVars_after = get_defined_vars ();

    foreach ($padVars_before as $padK => $padV)
      if ( isset( $GLOBALS [$padK] ) )
        unset( $GLOBALS [$padK] );

    foreach ($padVars_after as $padK => $padV)
      if ( pValid_store ($padK) ) {
        if ( isset( $GLOBALS [$padK] ) )
          unset( $GLOBALS [$padK] );
        $GLOBALS [$padK] = $$padK;
      }

  }

  function pCallback_before_row ( &$padRow_parm ) {

    if ( isset( $GLOBALS ['row'] ) ) {
      $padRow_save = TRUE;
      $padRow_save_store = $GLOBALS ['row'];
    } else
      $padRow_save = FALSE;

    $padVars_before = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( $padK <> 'row' and pValid_store ($padK) ) { 
        $padVars_before [] = $padK;
        $$padK = $padV;
      }

    $row = $padRow_parm;  
    include PAD . 'callback/row.php';
    $padRow_parm = $row;  

    $padVars_after = get_defined_vars();

    foreach ($padVars_before as $padK => $padV)
      if ( isset( $GLOBALS [$padK] ) )
        unset( $GLOBALS [$padK] );

    foreach ($padVars_after as $padK => $padV)
      if ( $padK <> 'row' and pValid_store ($padK) ) {
        if ( isset( $GLOBALS [$padK] ) )
          unset( $GLOBALS [$padK] );
        $GLOBALS [$padK] = $padV;
      }

    if ( $padRow_save ) {
      if ( isset( $GLOBALS ['row'] ) )
        unset ( $GLOBALS ['row'] );
      $GLOBALS ['row'] = $padRow_save_store;
    }

  }
  
?>