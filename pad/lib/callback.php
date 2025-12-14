<?php


  /**
   * Executes a callback script with isolated variable scope.
   *
   * Backs up global variables that pass validation, includes the
   * callback script, then restores/updates globals based on changes.
   *
   * @param string $padCallbackType The callback type (determines script file).
   *
   * @return void
   *
   * @global array $padOptionsCallback Callback configuration.
   * @global mixed $padResult          Current result value.
   * @global int   $pad                Current processing level.
   */
  function padCallbackBeforeXxx ($padCallbackType) {

    global $padOptionsCallback, $padResult, $pad, $padPrm;

    $padVarsBefore = [];
    foreach ($GLOBALS as $padK => $padV)
      if ( padValidStore ($padK) ) { 
        $padVarsBefore [] = $padK;
        $$padK = $padV;
      }

    include PAD . "callback/$padCallbackType.php";

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


  /**
   * Executes row callback with $row available in scope.
   *
   * Similar to padCallbackBeforeXxx but specifically handles the
   * 'row' variable - backs it up, makes it available to callback,
   * and preserves modifications.
   *
   * @param mixed &$padRowParm The row data (passed by reference).
   *
   * @return void
   *
   * @global array $padOptionsCallback Callback configuration.
   * @global mixed $padResult          Current result value.
   * @global int   $pad                Current processing level.
   */
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
    include PAD . 'callback/row.php';
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