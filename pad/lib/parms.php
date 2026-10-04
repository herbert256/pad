<?php

  // Copies request input into plain PHP globals, so a page's .php file and its template
  // can use $name directly. Called from inits/parms.php for POST, GET, COOKIE and SESSION.
  //
  // padGetParms   promotes one array; a name already set is left alone (so the first
  //               source wins) and padValidVar rejects empty, non-identifier and
  //               pad-prefixed names, which keeps engine state out of reach
  // padGetParms2  trims each value, recursing into nested arrays
  // padRequestVar  whether a request value of that name may become a global: one the
  //               $padRequestVars setting lets through, and never a $padSessionVars name

  function padGetParms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        if ( padValidVar ($field) )
          if ( $type == 'SESSION' or padRequestVar ($field) )
            $GLOBALS [$field] = padGetParms2 ( $type, $value );

  }

  function padRequestVar ( $field ) {

    global $padRequestVars, $padSessionVars;

    if ( in_array ( $field, $padSessionVars, TRUE ) )
      return FALSE;

    if ( $padRequestVars === TRUE )
      return TRUE;

    return is_array ( $padRequestVars ) and in_array ( $field, $padRequestVars, TRUE );

  }

  function padGetParms2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = padGetParms2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }

?>