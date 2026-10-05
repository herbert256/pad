<?php

  // Copies request input into plain PHP globals, so a page's .php file and its template
  // can use $name directly. Called from inits/parms.php for POST, GET, COOKIE and SESSION.
  //
  // padGetParms   promotes one array; a name already set is left alone (so the first
  //               source wins) and padValidVar rejects empty, non-identifier and
  //               pad-prefixed names, which keeps engine state out of reach; the query
  //               key that names the page ($padPageKey, inits/page.php) is no value
  // padGetParms2  trims each request value, recursing into nested arrays; session values
  //               pass as they are
  // padRequestVar  whether a request value of that name may become a global: one the
  //               $padRequestVars setting lets through - a cookie only when listed by name
  //               - and never a $padSessionVars name

  function padGetParms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( $type == 'GET' and (string) $field === ( $GLOBALS ['padPageKey'] ?? '' ) )
        continue;
      elseif ( (!isset($GLOBALS[$field])) )
        if ( padValidVar ($field) )
          if ( $type == 'SESSION' or padRequestVar ($field, $type) )
            $GLOBALS [$field] = padGetParms2 ( $type, $value );

  }

  // A cookie becomes a variable only when a $padRequestVars list names it: a cookie set by
  // a sibling subdomain, or left by another application on the host, preset any name the
  // application only sets now and then - $isAdmin - on every request that carried it.

  function padRequestVar ( $field, $type = 'GET' ) {

    global $padRequestVars, $padSessionVars;

    if ( in_array ( $field, $padSessionVars, TRUE ) )
      return FALSE;

    if ( $padRequestVars === TRUE )
      return $type != 'COOKIE';

    return is_array ( $padRequestVars ) and in_array ( $field, $padRequestVars, TRUE );

  }

  // Request values arrive as strings and are trimmed. Session values are what the
  // application stored - a NULL, a number, a boolean, an object - and are taken as they
  // are: trimming them turned an int into a string, raised a deprecation (a 500) on NULL
  // and threw on an object.

  function padGetParms2 ( $type, $field ) {

    if ( $type == 'SESSION' )
      return $field;

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = padGetParms2 ( $type, $value );
    elseif ( is_string ( $field ) )
      $field = trim ($field);

    return $field;

  }

?>