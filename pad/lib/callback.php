<?php


  function pCallback_before_xxx ($pCallback_type) {

    $pad_vars_before = [];
    foreach ($GLOBALS as $pK => $pad_v)
      if ( pad_valid_store ($pK) ) { 
        $pad_vars_before [] = $pK;
        $$pK = $pad_v;
      }

    include PAD . "callback/$pCallback_type.php";

    $pad_vars_after = get_defined_vars ();

    foreach ($pad_vars_before as $pK => $pad_v)
      if ( isset( $GLOBALS [$pK] ) )
        unset( $GLOBALS [$pK] );

    foreach ($pad_vars_after as $pK => $pad_v)
      if ( pad_valid_store ($pK) ) {
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

    $pad_vars_before = [];
    foreach ($GLOBALS as $pK => $pad_v)
      if ( $pK <> 'row' and pad_valid_store ($pK) ) { 
        $pad_vars_before [] = $pK;
        $$pK = $pad_v;
      }

    $row = $pRow_parm;  
    include PAD . 'callback/row.php';
    $pRow_parm = $row;  

    $pad_vars_after = get_defined_vars();

    foreach ($pad_vars_before as $pK => $pad_v)
      if ( isset( $GLOBALS [$pK] ) )
        unset( $GLOBALS [$pK] );

    foreach ($pad_vars_after as $pK => $pad_v)
      if ( $pK <> 'row' and pad_valid_store ($pK) ) {
        if ( isset( $GLOBALS [$pK] ) )
          unset( $GLOBALS [$pK] );
        $GLOBALS [$pK] = $pad_v;
      }

    if ( $pRow_save ) {
      if ( isset( $GLOBALS ['row'] ) )
        unset ( $GLOBALS ['row'] );
      $GLOBALS ['row'] = $pRow_save_store;
    }

  }
  
?>