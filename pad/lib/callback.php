<?php

  // Runs an application's _callbacks/ script over a tag's data, called from
  // callback/before.php once for 'init', once per row, once for 'exit'.
  //
  // Both functions do the same trick: lift every application-visible global (those
  // padValidStore accepts) into local scope, include the callback dispatcher, then push
  // the locals back out as globals. That is what lets a callback read and write template
  // variables as ordinary PHP variables.
  //
  // padCallbackBeforeXxx  the init and exit callbacks, which see the whole data set
  // padCallbackBeforeRow  one row, passed in and taken back out through the local $row;
  //                       any pre-existing global $row is saved and restored around it

  function padCallbackBeforeXxx ($padCallbackType) {

    global $padResult, $pad, $padPrm;

    $padVarsBefore = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( padValidStore ($padK) ) {
        $padVarsBefore [] = $padK;
        $$padK = $padV;
      }

    include PAD . "callback/$padCallbackType.php";

    $padVarsAfter = get_defined_vars ();

    // The callback's variables are also published to $padLvlFunVar, so the function group
    // of the @ subsystem can name the level they came from - {$sum@function} beside the
    // plain global read.

    foreach ( $padVarsAfter as $padK => $padV )
      if ( padValidStore ( $padK ) )
        $GLOBALS ['padLvlFunVar'] [ $GLOBALS ['pad'] ] [$padK] = $padV;

    foreach ($padVarsBefore as $padK => $padV)
      if ( isset( $GLOBALS [$padV] ) )
        unset( $GLOBALS [$padV] );

    foreach ($padVarsAfter as $padK => $padV)
      if ( padValidStore ($padK) ) {
        if ( isset( $GLOBALS [$padK] ) )
          unset( $GLOBALS [$padK] );
        $GLOBALS [$padK] = $$padK;
      }

  }

  function padCallbackBeforeRow ( &$padRowParm ) {

    global $padResult, $pad, $padPrm;

    if ( isset( $GLOBALS ['row'] ) ) {
      $padRowSave = TRUE;
      $padRowSaveStore = $GLOBALS ['row'];
    } else
      $padRowSave = FALSE;

    $padVarsBefore = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( $padK != 'row' and padValidStore ($padK) ) {
        $padVarsBefore [] = $padK;
        $$padK = $padV;
      }

    $row = $padRowParm;
    include PAD . 'callback/row.php';
    $padRowParm = $row;

    $padVarsAfter = get_defined_vars();

    foreach ( $padVarsAfter as $padK => $padV )
      if ( $padK != 'row' and padValidStore ( $padK ) )
        $GLOBALS ['padLvlFunVar'] [ $GLOBALS ['pad'] ] [$padK] = $padV;

    foreach ($padVarsBefore as $padK => $padV)
      if ( isset( $GLOBALS [$padV] ) )
        unset( $GLOBALS [$padV] );

    foreach ($padVarsAfter as $padK => $padV)
      if ( $padK != 'row' and padValidStore ($padK) ) {
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