<?php


  function padCallbackBeforeXxx ($padCallbackType) {

    global $padOptionsCallback, $padResult, $pad, $padPrm;

    $padVarsBefore = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( padValidStore ($padK) ) { 
        $padVarsBefore [] = $padK;
        $$padK = $padV;
      }

    include "/pad/callback/$padCallbackType.php";

    $padVarsAfter = get_defined_vars ();

    foreach ($padVarsBefore as $padK => $padV)
      if ( isset( $GLOBALS [$padK] ) )
        unset( $GLOBALS [$padK] );

    foreach ($padVarsAfter as $padK => $padV)
      if ( padValidStore ($padK) ) {
        if ( isset( $GLOBALS [$padK] ) )
          unset( $GLOBALS [$padK] );
        $GLOBALS [$padK] = $$padK;
      }

  }


  function padCallbackBeforeRow ( &$padRowParm ) {

    global $padOptionsCallback, $padResult, $pad, $padPrm;

    if ( isset( $GLOBALS ['row'] ) ) {
      $padRowSave = TRUE;
      $padRowSaveStore = $GLOBALS ['row'];
    } else
      $padRowSave = FALSE;

    $padVarsBefore = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( $padK <> 'row' and padValidStore ($padK) ) { 
        $padVarsBefore [] = $padK;
        $$padK = $padV;
      }

    $row = $padRowParm;  
    include '/pad/callback/row.php';
    $padRowParm = $row;  

    $padVarsAfter = get_defined_vars();

    foreach ($padVarsBefore as $padK => $padV)
      if ( isset( $GLOBALS [$padK] ) )
        unset( $GLOBALS [$padK] );

    foreach ($padVarsAfter as $padK => $padV)
      if ( $padK <> 'row' and padValidStore ($padK) ) {
        if ( isset( $GLOBALS [$padK] ) )
          unset( $GLOBALS [$padK] );
        $GLOBALS [$padK] = $padV;
      }

    if ( $padRowSave ) {
      if ( isset( $GLOBALS ['row'] ) )
        unset ( $GLOBALS ['row'] );
      $GLOBALS ['row'] = $padRowSaveStore;
    }

  }
  
?>