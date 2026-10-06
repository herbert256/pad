<?php

  // Resolves a plain, unprefixed field name by searching outwards from the current level.
  // This is the common case behind {$name}, and the search order is what gives PAD its
  // scoping rules:
  //
  //   1  a name like -2 is a relative level - return the first scalar of that level's row
  //   2  a numeric name indexes the current level's options
  //   3  levels $pad down to 1: the iteration row $padCurrent, then names registered by
  //      {set} as occurrence or level variables ($padSetOcc / $padSetLvl, whose values
  //      live in $GLOBALS)
  //   4  $GLOBALS itself - the variables a page's .php file left behind
  //   5  any global array that happens to carry the key, skipping pad* and pq* engine state
  //      and PHP's own arrays - but for GET and POST, which are searched only under the
  //      default $padRequestVars and never for an engine name
  //   6  down the level stack again for tag parameters, then options, then function-level
  //      variables
  //
  // $type decides which candidates count: 1/2 accept only scalars, 3/4 only arrays, 9 asks
  // whether the value is present and NULL. A candidate of the wrong shape is skipped and
  // the search continues. INF comes back when nothing matched at all.

  function padFieldLevel ( $field, $type ) {

    global $pad, $padCurrent, $padPrm, $padOpt, $padName, $padLvlFunVar;
    global $padSetOcc, $padSetLvl;

    if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
      $idx = $pad + $field;
      if ( $type == 1 and $idx and isset ($padCurrent [$idx]) )
        return TRUE;
      if ( $type == 2 and $idx and isset ($padCurrent [$idx]) and is_array ($padCurrent [$idx]) )
        foreach ($padCurrent [$idx] as $value)
          if ( is_scalar($value) )
            return $value;
    }

    if ( is_numeric($field) )
      if ( array_key_exists ( $field, $padOpt [$pad] ) )
        return $padOpt [$pad] [$field];

    for ( $i=$pad; $i; $i-- ) {

      if ( array_key_exists ( $field, $padCurrent [$i] ) ) {
        $work = $padCurrent [$i] [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

      if ( isset ( $padSetOcc [$i] [$field] ) ) return $GLOBALS [$field] ;
      if ( isset ( $padSetLvl [$i] [$field] ) ) return $GLOBALS [$field] ;

    }

    if ( array_key_exists ( $field, $GLOBALS ) ) {
      $work = $GLOBALS [$field];
      if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
      if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
      elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
    }

    // PHP's request arrays are global arrays too, and searching them hands a template any
    // request value by name - around a $padRequestVars list, which decides the names that
    // reach it. Under the default TRUE the GET and POST values stay searched, as they always
    // were, but never for an engine name, which no request may fill (padValidVar).
    //
    // PHP's other arrays are no data of the page. A cookie is a variable only when a
    // $padRequestVars list names it, and promotion made it one then; $_SERVER and $_ENV hold
    // the request's headers and the secrets padEnv reads from there; $_FILES and $_SESSION
    // have helpers of their own. Searched like the page's own arrays, a cookie isAdmin=1
    // answered {$isAdmin} on a page that never set it, {$DOCUMENT_ROOT} the server's
    // directory and {$DB_PASSWORD} a SetEnv secret.

    $skip = [ '_SERVER', '_ENV', '_COOKIE', '_FILES', '_SESSION', '_REQUEST' ];

    if ( ( $GLOBALS ['padRequestVars'] ?? TRUE ) !== TRUE or padEngineName ( $field ) or str_starts_with ( $field, '_' ) )
      $skip = array_merge ( $skip, [ '_GET', '_POST' ] );

    // The query key that names the page - ?links - is no request value (inits/page.php).

    $page = ( $field === ( $GLOBALS ['padPageKey'] ?? '' ) ) ? [ '_GET', '_REQUEST' ] : [];

    foreach ( $GLOBALS as $key => $value )
      if ( is_array ($value) and array_key_exists ( $field, $value)
           and substr($key, 0, 3) != 'pad' and substr($key, 0, 2) != 'pq'
           and ! in_array ( $key, $skip ) and ! in_array ( $key, $page ) )  {
        $work = $value [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

    for ( $i=$pad; $i >= 0; $i-- )
      if ( array_key_exists ( $field, $padPrm [$i] ) ) {
        $work = $padPrm [$i] [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) { padDoneAt ( $i, $field ); return NULL;  }
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) { padDoneAt ( $i, $field ); return $work; }
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) { padDoneAt ( $i, $field ); return $work; }
      }

    for ( $i=$pad; $i >= 0; $i-- )
      if ( array_key_exists ( $field, $padOpt [$i] ) ) {
        $work = $padOpt [$i] [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

   for ( $i=$pad; $i >= 0; $i-- )
      if ( array_key_exists ( $field, $padLvlFunVar [$i] ) ) {
        $work = $padLvlFunVar [$i] [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

    return INF;

  }

?>
