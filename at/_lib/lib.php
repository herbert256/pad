<?php


   function padAtDataNew ( $name, $names ) {

    global $padDataStore;

    $padDataStore [$name] = padData ($name);

    $check = padAtSearch ( $padDataStore [$name], $names ); 

    if ( $check === INF ) 
      unset ( $padDataStore [$name] );

    return $check;

  }


  function padAtIdx ( $level, $cor ) {

    global $pad, $padName;

    for ( $i=$pad; $i>-1; $i-- ) 
      if ( $padName [$i] == $level )
        return $i;

    if ( strlen($level) > 1 and substr($level,0,1) == '-' and is_numeric(substr($level,1)) ) {
      $idx = $pad + $level;
      if ( $cor )
        $idx = $idx + $cor;
      if ( $idx > 0 and $idx <= $pad )
        return $idx;

    }

    if ( is_numeric ($level) ) 
      if ($level >= 0 and $level <= $pad )
        return $level;

    return FALSE;
    
  }


  function padAtKey ( $search, $index ) {

    if ( ! ctype_digit ( $index ) )             return '';
    if ( array_is_list ( $search ) )            return '';
    if ( array_key_exists ( $index, $search ) ) return '';

    $keys = array_keys ( $search );

    return $keys [ $index - 1 ] ?? '';

  }


  function padAtSetTag () {

    global $pad, $padType;

    $padType [$pad] = 'tag' ;

  }


?>