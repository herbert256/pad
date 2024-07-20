<?php


  function padIsTag ($name, $cor) {

    global $pad, $padName;

    for ( $i=$pad; $i; $i-- ) 
      if ( $padName [$i] == $name )
        return $i;

    return FALSE;

  }


  function padIsLevel ( $field, $cor ) {

    global $pad;

    if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
      $idx = $pad + $field;
      if ( $cor )
        $idx = $idx + $cor;
      if ( $idx > 0 and $idx <= $pad )
        return $idx;
    }

    if ( is_numeric($field) ) 
      if ($field > 0 and $field <= $pad )
        return $field;

    return FALSE;

  }


  function padAtKey ( $search, $index ) {

    if ( ! ctype_digit ( $index )   )            return '';
    if ( array_is_list ( $current ) )            return '';
    if ( array_key_exists ( $index, $current ) ) return '';

    $keys = array_keys ( $search );

    return $keys [ $index - 1 ] ?? '';

  }


?>