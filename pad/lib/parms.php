<?php

  // Copies request input into plain PHP globals, so a page's .php file and its template
  // can use $name directly. Called from inits/parms.php for POST, GET, COOKIE and SESSION.
  //
  // padGetParms   promotes one array; a name already set is left alone (so the first
  //               source wins) and padValidVar rejects empty, non-identifier and
  //               pad-prefixed names, which keeps engine state out of reach
  // padGetParms2  trims each value, recursing into nested arrays

  function padGetParms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        if ( padValidVar ($field) )
          $GLOBALS [$field] = padGetParms2 ( $type, $value );

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