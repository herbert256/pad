<?php


  function pCallback_before_xxx ($pCallback_type) {

    $pVars_before = [];
    foreach ($GLOBALS as $pK => $pV)
      if ( pValid_store ($pK) ) { 
        $pVars_before [] = $pK;
        $$pK = $pV;
      }

    include PAD . "callback/$pCallback_type.php";

    $pVars_after = get_defined_vars ();

    foreach ($pVars_before as $pK => $pV)
      if ( isset( $GLOBALS [$pK] ) )
        unset( $GLOBALS [$pK] );

    foreach ($pVars_after as $pK => $pV)
      if ( pValid_store ($pK) ) {
        if ( isset( $GLOBALS [$pK] ) )
          unset( $GLOBALS [$pK] );
        $GLOBALS [$pK] = $$pK;
      }

  }

  function pCallback_before_row ( &$pRow_parm ) {

    if ( isset( $GLOBALS ['row'] ) ) {
      $pRow_save = TRUE;
      $pRow_save_store = $GLOBALS ['row'];
    } else
      $pRow_save = FALSE;

    $pVars_before = [];
    foreach ($GLOBALS as $pK => $pV)
      if ( $pK <> 'row' and pValid_store ($pK) ) { 
        $pVars_before [] = $pK;
        $$pK = $pV;
      }

    $row = $pRow_parm;  
    include PAD . 'callback/row.php';
    $pRow_parm = $row;  

    $pVars_after = get_defined_vars();

    foreach ($pVars_before as $pK => $pV)
      if ( isset( $GLOBALS [$pK] ) )
        unset( $GLOBALS [$pK] );

    foreach ($pVars_after as $pK => $pV)
      if ( $pK <> 'row' and pValid_store ($pK) ) {
        if ( isset( $GLOBALS [$pK] ) )
          unset( $GLOBALS [$pK] );
        $GLOBALS [$pK] = $pV;
      }

    if ( $pRow_save ) {
      if ( isset( $GLOBALS ['row'] ) )
        unset ( $GLOBALS ['row'] );
      $GLOBALS ['row'] = $pRow_save_store;
    }

  }
  
?>