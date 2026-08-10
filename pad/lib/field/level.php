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

    foreach ( $GLOBALS as $key => $value )
      if ( is_array ($value) and array_key_exists ( $field, $value)
           and substr($key, 0, 3) != 'pad' and substr($key, 0, 2) != 'pq' )  {
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