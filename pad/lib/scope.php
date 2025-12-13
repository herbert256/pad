<?php


  /**
   * Finds level index for a tag name.
   *
   * Searches backwards through levels for matching tag.
   *
   * @param string $tag Tag name to find.
   *
   * @return int|false Level index or FALSE if not found.
   */
  function padFindIdx ( $tag ) {

    global $pad, $padTag;

    for ( $i = $pad; $i >= 0 ; $i-- )
      if ( $padTag [$i] == $tag )
        return $i;

    return FALSE;

  }


  /**
   * Checks if currently inside include, get, page, or example tag.
   *
   * @return bool TRUE if nested inside one of these tags.
   */
  function padInsideOther () {

    global $padTag, $pad;

    for ( $i=$pad; $i; $i--) {
      if ( $padTag [$i] == 'include' ) return TRUE;
      if ( $padTag [$i] == 'get'     ) return TRUE;
      if ( $padTag [$i] == 'page'    ) return TRUE;
      if ( $padTag [$i] == 'example' ) return TRUE;
    }

    return FALSE;

  }


  /**
   * Handles start-and-close tag pattern.
   *
   * @param string $go Walk state to set.
   *
   * @return bool TRUE if pattern matched.
   */
  function padStartAndClose ( $go ) {

    global $pad, $padWalk, $padPrmType;

    if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
      $padWalk [$pad] = $go;
      return TRUE;
    }

    return FALSE;

  }


  /**
   * Sets global variable with level-scoped save/restore.
   *
   * Saves previous value to restore when level exits.
   *
   * @param string $name  Variable name.
   * @param mixed  $value Value to set.
   *
   * @return void
   */
  function padSetGlobalLvl ( $name, $value ) {

    if ( ! padValidVar($name) )
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveLvl, $padDeleteLvl;

    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveLvl [$pad]) )
      $padSaveLvl [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteLvl [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  /**
   * Sets global variable with occurrence-scoped save/restore.
   *
   * Saves previous value to restore when occurrence ends.
   *
   * @param string $name  Variable name.
   * @param mixed  $value Value to set.
   *
   * @return void
   */
  function padSetGlobalOcc ( $name, $value ) {

    if ( ! padValidVar($name) )
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveOcc, $padDeleteOcc;

    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveOcc [$pad]) )
      $padSaveOcc [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteOcc [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  /**
   * Restores globals saved at level start.
   *
   * @return void
   */
  function padResetLvl () {

    global $pad, $padSaveLvl, $padDeleteLvl;

    foreach ( $padSaveLvl [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) )
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteLvl [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  /**
   * Restores globals saved at occurrence start.
   *
   * @return void
   */
  function padResetOcc () {

    global $pad, $padSaveOcc, $padDeleteOcc;

    foreach ( $padSaveOcc [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) )
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteOcc [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  /**
   * Checks if tag has array data at any level.
   *
   * @param string $tag Tag name to check.
   *
   * @return bool TRUE if array exists.
   */
  function padChkLevel ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return TRUE;

    return FALSE;

  }


  /**
   * Gets array data for tag from any level.
   *
   * @param string $tag Tag name to find.
   *
   * @return array|null Array data or null.
   */
  function padGetLevelArray ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return $padCurrent [$search] [$tag];

  }


  /**
   * Gets tag parameter value with default.
   *
   * @param string $parm    Parameter name.
   * @param mixed  $default Default value.
   *
   * @return mixed Parameter value or default.
   */
  function padTagParm ($parm, $default='') {

    global $pad, $padPrm;

    padDone ($parm);

    if ( isset ( $padPrm [$pad] [$parm] ) )
      return $padPrm [$pad] [$parm];
    else
      return $default;

  }


  /**
   * Marks a parameter/option as processed.
   *
   * @param string $var Parameter name.
   * @param mixed  $val Value to store.
   *
   * @return void
   */
  function padDone ( $var, $val=TRUE ) {

    $GLOBALS ['padDone'] [$GLOBALS ['pad']] [$var] = $val;

  }


  /**
   * Checks if parameter was already processed.
   *
   * @param string $var Parameter name.
   *
   * @return bool TRUE if processed.
   */
  function padIsDone ( $var ) {

    return isset ( $GLOBALS ['padDone'] [$GLOBALS ['pad']] [$var] );

  }


?>
