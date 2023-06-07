<?php


  function padAny ( $field, $names ) {

    if     ( count ($names) == 0 ) return INF;
    elseif ( count ($names) == 1 ) return padAnyOne   ( $field );
    else                           return padAnyNames ( $names );

  }


  function padAnyOne ( $one ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName, $padOpt, $padPrm, $padSetLvl;

    for ( $i=$pad; $i >=0; $i-- ) {

      if ( in_array ( $one, $padCurrent [$i] ) )
        return $padCurrent [$i] [$one];

      foreach ( $padTable [$i] as $value )
        if ( in_array ( $one, $value ) )
          return $value [$one];

      if ( in_array ( $one, $padSetLvl [$i] ) )
        return $padSetLvl [$i] [$one];

      if ( in_array ( $one, $padOpt [$i] ) )
        return $padOpt [$i] [$one];

      if ( in_array ( $one, $padPrm [$i] ) )
        return $padSetPrm [$i] [$one];

      $check = padAnyOneFind ( $padCurrent [$i], $one );
      if ( $check !== INF )
        return $check;

      $check = padAnyOneFind ( $padData [$i], $one );
      if ( $check !== INF )
        return $check;

    }

    $check = padAnyOneFind ( $GLOBALS, $one );
    if ( $check !== INF )
      return $check;

    return INF;   

  }


  function padAnyOneFind( $current, $one ) {

    if ( in_array ( $one, $current) )
      return $current [$one];

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padAnyOneFind ( $value, $one );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

  }


  function padAnyNames ( $names ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName, $padOpt, $padPrm, $padSetLvl;

    for ( $i=$pad; $i >=0; $i-- ) {

      $check = padAnyNamesFind  ( $padCurrent [$i], $one );
      if ( $check !== INF )
        return $check;

      $check = padAnyNamesFind  ( $padData [$i], $one );
      if ( $check !== INF )
        return $check;

    }

    $check = padAnyNamesFind ( $GLOBALS, $names );
    if ( $check !== INF )
      return $check;

    return INF;   

  }


  function padAnyNamesFind ( $current, $names ) {

    $check = padAtSearch ( $current, $names );
    if ( $check !== INF)
      return $check;

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padAnyNamesFind ( $value, $names );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

  }


?>