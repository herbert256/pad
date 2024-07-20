<?php


  function padAt ( $names, $parts, $cor ) {

    $name = end ($names);

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

    $first  = $parts [0] ?? '';
    $second = $parts [1] ?? '';
    $third  = $parts [2] ?? '';

    global $debug;
    $debug [] = ['at', $first, $second, $third];

    $padIdx = 0;
    
    if ( $first == 'tag' and $second and ! $third ) { 
      if     ( padIsTag   ( $second, $cor ) ) $padIdx = padIsTag   ( $second, $cor );
      elseif ( padIsLevel ( $second, $cor ) ) $padIdx = padIsLevel ( $second, $cor );
    } elseif ( $first and ! $second ) {
      if     ( padIsTag   ( $first, $cor ) ) $padIdx = padIsTag   ( $first, $cor );
      elseif ( padIsLevel ( $first, $cor ) ) $padIdx = padIsLevel ( $first, $cor ); 
    }

    if ( $padIdx )
      return include pad . 'at/any/tag.php';

    if ( $first == 'tag' and $second ) { 
      if     ( padIsTag   ( $second, $cor ) ) return padAtTag ( $names, padIsTag   ( $second, $cor ), $third, $cor );
      elseif ( padIsLevel ( $second, $cor ) ) return padAtTag ( $names, padIsLevel ( $second, $cor ), $third, $cor );
    }
    elseif ( $first ) {
      if     ( padIsTag   ( $first, $cor ) ) return padAtTag ( $names, padIsTag   ( $first, $cor ), $second, $cor );
      elseif ( padIsLevel ( $first, $cor ) ) return padAtTag ( $names, padIsLevel ( $first, $cor ), $second, $cor ); 
    }

    if ( count ($parts) == 1 and file_exists ( pad . "at/groups/$first.php") )
      return padAtFindGroup ( $first, $names, $cor );

    if ( file_exists ( pad . "at/types/$first.php") )
      return pad . "at/types/$first.php";

    return include pad . 'at/types/any.php';

  }


  function padAtTag ( $names, $padIdx, $parm, $cor ) {

    $name = end ($names);

    global $debug;
    $debug [] = ['tag', $names, $padIdx, $parm, $cor];

    if ( $parm and file_exists ( pad . "at/groups/$parm.php") )
      return include pad . "at/groups/$parm.php";

    if ( ! $parm and count ( $names ) == 1 ) {
      $property = $names [0];
      if ( file_exists ( pad . "at/properties/$property.php") )
        return include pad . "at/properties/$property.php";
    } 

    if ( ! $parm ) 
      return include pad . 'at/groups/any.php';

    return INF;

  }


  function padAtFindGroup ( $group, $names, $cor ) {

    $field = implode ( '.', $names) . '@*' . '.' . $group;
    $at    = padAtValue ( $field, $cor );

    return $at;

  }


  function padFindNames ( $current, $names ) {

    $check = padAtSearch ( $current, $names );
    if ( $check !== INF)
      return $check;

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padFindNames ( $value, $names );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

  }


  function padAtOneFind ( $current, $one ) {

    if ( array_key_exists ( $one, $current) )
      return $current [$one];

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padAtOneFind ( $value, $one );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

  }


  function padIsTag ($name, $cor) {

    global $pad, $padName;

    for ( $i=$pad; $i; $i-- ) 
      if ( $padName [$i] == $name )
        return $i;

    return FALSE;

  }


  function padIsLevel ( $field, $cor ) {

    global $debug;
    $debug [] = ['is', $field, $cor];

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


  function padAtIdxNoName ( $start=0 ) {

    global $pad, $padName, $padType, $padTag;

    for ( $i=$pad-$start; $i; $i-- ) {

      if ( $padTag [$i] == 'if' or $padTag [$i] == 'case' )
        continue;

      if ( $padType[$i] == 'tag' )
        continue;

      if ( $padType[$i] == 'at' and file_exists ( pad . 'tag/' .  $padName[$i] . '.php' ) )
        continue;
      
      return $i;
    
    }
    
    return $pad;

  }


  function padAtIndex ( $search, $index ) {

    $keys = array_keys ( $search );
    $key  = $keys [ $index - 1 ] ?? '';

    if ( $key ) 
      return $search [ $key ];
    else
      return INF;

  }


  function padAtKey ( $search, $index ) {

    $keys = array_keys ( $search );

    return $keys [ $index - 1 ] ?? '';

  }


?>