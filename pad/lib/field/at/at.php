<?php


  function padAtTag ( $names, $padIdx, $tag, $type ) {

    if ( $tag and file_exists ( pad . "at/groups/$tag.php") )
      return include pad . "at/groups/$tag.php";

    if ( ! $tag and count ( $names ) == 1 ) {
      $property = $names [0];
      if ( file_exists ( pad . "at/properties/$property.php") )
        return include pad . "at/properties/$property.php";
    } 

    if ( ! $tag ) 
      return include pad . 'at/groups/any.php';

    return INF;

  }


  function padAt ( $names, $parts, $type ) {

    $name = end ($names);

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

    $first  = $parts [0] ?? '';
    $second = $parts [1] ?? '';
    $third  = $parts [2] ?? '';

    if ( $first == 'tag' and $second ) { 
      if     ( padIsTag   ( $second ) ) return padAtTag ( $names, padIsTag   ( $second ), $third, $type );
      elseif ( padIsLevel ( $second ) ) return padAtTag ( $names, padIsLevel ( $second ), $third, $type );
    }
    elseif ( $first ) {
      if     ( padIsTag   ( $first ) ) return padAtTag ( $names, padIsTag   ( $first ), $second, $type );
      elseif ( padIsLevel ( $first ) ) return padAtTag ( $names, padIsLevel ( $first ), $second, $type ); 
    }


    return INF;

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


  function padIsTag ($name) {

    global $pad, $padName;

    for ( $i=$pad; $i; $i-- ) 
      if ( $padName [$i] == $name )
        return $i;

    return FALSE;

  }


  function padIsLevel ( $field ) {

    global $pad;

    if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
      $idx = $pad + $field;
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


?>