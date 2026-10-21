<?php

  // Copies request input into plain PHP globals, so a page's .php file and its template
  // can use $name directly. Called from inits/parms.php for POST, GET, COOKIE and SESSION.
  //
  // padGetParms   promotes one array; a name already set is left alone (so the first
  //               source wins) - set to NULL as well: $currentUser = NULL in the config,
  //               filled by _inits.php only for a signed-in visitor, was filled by
  //               ?currentUser=admin, as isset() called it unset; the session alone fills
  //               a NULL, being the application's own state: $rvUser = NULL is the signed-
  //               out default the stored session value restores - and padValidVar rejects
  //               empty, non-identifier and pad-prefixed names, which keeps engine state
  //               out of reach; the query key that names the page ($padPageKey,
  //               inits/page.php) is no value
  // padGetParms2  trims each request value, recursing into nested arrays; session values
  //               pass as they are
  // padRequestVar  whether a request value of that name may become a global: one the
  //               $padRequestVars setting lets through - a cookie only when listed by name
  //               - and never a $padSessionVars name

  // What a request value made a variable is noted as well, the value with its name, in
  // $padRequestGot: a visitor chooses the names, and a name that is a sequence, a PHP
  // function, a constant or a _data file in the template stays what the template wrote -
  // padTypeAsked in lib/type.php reads the note. The value is kept to see whether the page
  // has set the variable since: one the page's PHP assigned is the page's own.

  // A name that is set is never filled, and isset ( $GLOBALS [$name] ) says so cheaply. One
  // that is not set may still be there holding NULL, which only the session fills: the
  // names of the globals are copied once per call for that question, the first time it is
  // asked. It was array_key_exists ( $name, $GLOBALS ) per value, which copies the whole
  // symbol table since PHP 8.1 - a table every promoted value made larger, so the time grew
  // with the square of the number of values: 4000 posted ones took 125 ms, and a cookie
  // paid before it was turned down.

  function padGetParms ( $type, $parms ) {

    $have = NULL;

    foreach ( $parms as $field => $value ) {

      if ( $type == 'GET' and (string) $field === ( $GLOBALS ['padPageKey'] ?? '' ) )
        continue;

      if ( isset ( $GLOBALS [$field] ) or ! padValidVar ($field) )
        continue;

      if ( $type != 'SESSION' ) {

        if ( ! padRequestVar ($field, $type) )
          continue;

        $have ??= array_flip ( array_keys ( $GLOBALS ) );

        if ( isset ( $have [$field] ) )
          continue;

      }

      $GLOBALS [$field] = padGetParms2 ( $type, $value );

      if ( $type != 'SESSION' )
        $GLOBALS ['padRequestGot'] [$field] = $GLOBALS [$field];

    }

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
