<?php

  function padFieldLevel ( $field, $type ) {

    global $pad, $padCurrent, $padPrm, $padOpt, $padName, $padTable, $padLvlFunVar;

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

      foreach ( $padTable [$i] as $table => $value) 
        if ( array_key_exists ( $field, $value) ) {
          $work = $value [$field];
          if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
          if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
          elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
        }

    }
 
    if ( array_key_exists ( $field, $GLOBALS ) ) {
      $work = $GLOBALS [$field];
      if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
      if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
      elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
    }

    foreach ( $GLOBALS as $key => $value ) 
      if ( is_array ($value) and array_key_exists ( $field, $value) and substr($key, 0, 3) <> 'pad' )  {
        $work = $value [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

    for ( $i=$pad; $i >= 0; $i-- )
      if ( array_key_exists ( $field, $padPrm [$i] ) ) {
        $work = $padPrm [$i] [$field];
        if     ($type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
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