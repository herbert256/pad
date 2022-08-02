<?php


  function pad_callback_before_xxx ($pad_callback_type) {

    $pad_vars_before = [];
    foreach ($GLOBALS as $pad_k => $pad_v)
      if ( pad_valid_store ($pad_k) ) { 
        $pad_vars_before [] = $pad_k;
        $$pad_k = $pad_v;
      }

    include PAD . "callback/$pad_callback_type.php";

    $pad_vars_after = get_defined_vars ();

    foreach ($pad_vars_before as $pad_k => $pad_v)
      if ( isset( $GLOBALS [$pad_k] ) )
        unset( $GLOBALS [$pad_k] );

    foreach ($pad_vars_after as $pad_k => $pad_v)
      if ( pad_valid_store ($pad_k) ) {
        if ( isset( $GLOBALS [$pad_k] ) )
          unset( $GLOBALS [$pad_k] );
        $GLOBALS [$pad_k] = $$pad_k;
      }

  }

  function pad_callback_before_row ( &$pad_row_parm ) {

    if ( isset( $GLOBALS ['row'] ) ) {
      $pad_row_save = TRUE;
      $pad_row_save_store = $GLOBALS ['row'];
    } else
      $pad_row_save = FALSE;

    $pad_vars_before = [];
    foreach ($GLOBALS as $pad_k => $pad_v)
      if ( $pad_k <> 'row' and pad_valid_store ($pad_k) ) { 
        $pad_vars_before [] = $pad_k;
        $$pad_k = $pad_v;
      }

    $row = $pad_row_parm;  
    include PAD . 'callback/row.php';
    $pad_row_parm = $row;  

    $pad_vars_after = get_defined_vars();

    foreach ($pad_vars_before as $pad_k => $pad_v)
      if ( isset( $GLOBALS [$pad_k] ) )
        unset( $GLOBALS [$pad_k] );

    foreach ($pad_vars_after as $pad_k => $pad_v)
      if ( $pad_k <> 'row' and pad_valid_store ($pad_k) ) {
        if ( isset( $GLOBALS [$pad_k] ) )
          unset( $GLOBALS [$pad_k] );
        $GLOBALS [$pad_k] = $pad_v;
      }

    if ( $pad_row_save ) {
      if ( isset( $GLOBALS ['row'] ) )
        unset ( $GLOBALS ['row'] );
      $GLOBALS ['row'] = $pad_row_save_store;
    }

  }
  
?>