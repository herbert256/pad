<?php

  // Scope handling for the level stack: setting template variables as real PHP globals
  // without letting them escape the tag they belong to, plus lookups up the stack.
  //
  // padSetGlobalLvl / padSetGlobalOcc  publish $var (level) and %var (occurrence) values
  //                   into $GLOBALS, first recording in $padSaveLvl/$padSaveOcc what was
  //                   there before, or in $padDeleteLvl/$padDeleteOcc that there was
  //                   nothing. padValidVar keeps engine names out
  // padResetLvl / padResetOcc  undo exactly that when the level or occurrence ends, which
  //                   is what makes nesting and shadowing work
  //
  // padFindIdx        the nearest enclosing level running a given tag
  // padChkLevel / padGetLevelArray  search $padCurrent outwards for a named array, so an
  //                   inner tag can reach an outer loop's data
  // padInsideOther    TRUE when somewhere above us is an include, get, page or example
  // padStartAndClose  TRUE for a tag written as a single self-closing {tag/}, and it
  //                   redirects the walk to $go so such a tag runs once
  // padTagParm        the value of a parameter on the current tag, or $default; it also
  //                   marks the parameter handled through padDone
  // padDone / padIsDone  that bookkeeping - which parameters and options a tag consumed,
  //                   so the engine can spot ones nobody looked at

  function padFindIdx ( $tag ) {

    global $pad, $padTag;

    for ( $i = $pad; $i >= 0 ; $i-- )
      if ( $padTag [$i] == $tag )
        return $i;

    return FALSE;

  }

  function padInsideOther () {

    global $padTag, $pad;

    // The walk starts one level up: the question is whether an *enclosing* level renders
    // another page, and starting on the tag's own level made {example}, {get} and {page}
    // drop their own xref record.

    for ( $i = $pad - 1; $i >= 0; $i-- ) {
      if ( $padTag [$i] == 'include' ) return TRUE;
      if ( $padTag [$i] == 'get'     ) return TRUE;
      if ( $padTag [$i] == 'page'    ) return TRUE;
      if ( $padTag [$i] == 'example' ) return TRUE;
    }

    return FALSE;

  }

  function padStartAndClose ( $go ) {

    global $pad, $padWalk, $padPrmType;

    if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
      $padWalk [$pad] = $go;
      return TRUE;
    }

    return FALSE;

  }

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

    // Undone, the books start empty for the next row. They were kept for the whole level,
    // and the delete list grew by every name every row set, so each row's reset walked all
    // the rows before it: 100,000 rows took half a minute.

    $padSaveOcc   [$pad] = [];
    $padDeleteOcc [$pad] = [];

  }

  function padChkLevel ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return TRUE;

    return FALSE;

  }

  function padGetLevelArray ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return $padCurrent [$search] [$tag];

  }

  // The value as the caller gave it, but for the characters that open a tag. A tag's PHP
  // reads a parameter for logic - ===, explode, urlencode, a db() lookup - and escapes it for
  // its own output with htmlspecialchars, so its quotes and backslash stay real: handed back
  // as protection stand-ins they went through htmlspecialchars unescaped and turned the
  // documented _tags/button.php into an injection. Its { } and | do not: what an application
  // or _common tag echoes, or builds $padContent from, is template source, so a value with
  // {php:...} in it ran - as stand-ins they print as themselves (exits/exits.php) and open
  // nothing. A built-in tag reads its own options whole. Off under $padProtectValues = FALSE.

  function padTagParm ($parm, $default='') {

    global $pad, $padPrm, $padProtectValues, $padType;

    padDone ($parm);

    if ( ! isset ( $padPrm [$pad] [$parm] ) )
      return $default;

    $value = $padPrm [$pad] [$parm];

    if ( $padProtectValues and is_string ( $value )
         and in_array ( $padType [$pad] ?? '', [ 'app', 'common' ], TRUE ) )
      $value = strtr ( $value, array_intersect_key ( padProtectMap (), [ '{' => 1, '}' => 1, '|' => 1 ] ) );

    return $value;

  }

  function padDone ( $var, $val=TRUE ) {

    global $pad, $padDone;

    $padDone [$pad] [$var] = $val;

  }

  // The same mark on another level's book: an option is read from anywhere below its tag -
  // {parm:x}, option.x@tag, a field falling back to an enclosing tag - and the strict
  // unread-option sweep must see those reads on the level that owns the option.

  function padDoneAt ( $idx, $var ) {

    global $padDone;

    $padDone [$idx] [$var] = TRUE;

  }

  function padIsDone ( $var ) {

    global $pad, $padDone;

    return isset ( $padDone [$pad] [$var] );

  }

?>
