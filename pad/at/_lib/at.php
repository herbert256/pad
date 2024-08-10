<?php


  function padAt ( $field, $cor=0 ) {

    if ( ! padAtCheck ( $field ) )
      return INF;

    return padAtValue ( $field, $cor );

  }


  function padAtValue ( $field, $cor ) {
        
    list ( $before, $after ) = padSplit ( '@', $field );
  
    $names = padExplode ( $before, '.' ); 
    $name  = reset ($names);

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

    if ( str_contains($field, '@*') )
      return padAtValueAny ( $field, $cor);

    $parts = padExplode ( $after,  '.' ); 

    if ( ! count ( $parts ) )
      return padAtSingle ( $field, $cor );

    $first  = $parts [0] ?? '';
    $second = $parts [1] ?? '';

    $check = padAtTags ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtGroups ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtType ( $first, $second, $names, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtStore ( $GLOBALS ['padSeqStore'] ?? [], $names, $parts );
    if ( $check !== INF )
      return $check;

    $check = padAtStore ( $GLOBALS ['padDataStore'] ?? [], $names, $parts );
    if ( $check !== INF )
      return $check;

    $check = padAtGlobals ( $names, $parts );
    if ( $check !== INF )
      return $check;

    $check = padAtData ( $names, $parts );
    if ( $check !== INF )
      return $check;

    return INF;

  }


  function  padAtValueAny ( $field, $cor ) {

    global $pad;

    $idx = 999;

    $check = str_replace ( '@*', '', $field );
    if ( file_exists ( pad . "at/properties/$check.php") ) {
      $idx = $pad + $cor - 1;
      $check = str_replace ( '@*', "@$idx", $field );
      $check = padAtValue ( $check, $cor );
      if ( $check !== INF )
        return $check; 
    }

    for ( $i=$pad; $i; $i-- ) {
 
      $check = str_replace ( '@*', "@$i", $field );
      $check = padAtValue ( $check, $cor );
      if ( $check !== INF )
        return $check; 

    }

    if ( str_ends_with($field, '@*') ) {

      $check = padAtSingle ( $field, $cor );
      if ( $check !== INF )
        return $check;
      
    }

    return INF;

  }
  

  function padAtSingle ( $field, $cor ) {

    $field = str_replace ( '@*', '', $field );
    $names = padExplode ( $field, '.' ); 

    $check = padAtSearch ( $GLOBALS ['padDataStore'] ?? [], $names );
    if ( $check !== INF )
      return $check;

    $check = padAtSearch ( $GLOBALS ['padSequenceStore'] ?? [], $names );
    if ( $check !== INF )
      return $check;

    $check = padAtSearch ( $GLOBALS, $names );
    if ( $check !== INF )
      return $check;

    $check = padAtSpecial ( $names, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtPad ( $names, $cor );
    if ( $check !== INF )
      return $check;

    return INF;

  }


  function padAtSpecial ( $names, $cor ) {

    $special = ['_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ;

    foreach ( $special as $field ) 

      if ( isset ( $GLOBALS [$field] ) and is_array ( $GLOBALS [$field] ) and count ( $GLOBALS [$field] )) {
        $check = padAtSearch ( $GLOBALS [$field], $names );
        if ( $check !== INF)
          return $check;
      }

    return INF;

  }


  function padAtPad ( $names, $cor ) {

    foreach ( $GLOBALS  as $key => $value ) 

      if ( str_starts_with($key, 'pad' ) and is_array ( $GLOBALS [$key] ) and count ( $GLOBALS [$key] ) ) {
        $check = padAtSearch ( $value, $names );
        if ( $check !== INF)
          return $check;
      }

    return INF;

  }
  

  function padAtStore ( $store, $names, $parts ) {

    if ( ! count ($store) ) 
      return INF;

    if ( count ($parts) == 1 ) {      
      $name = reset ($parts);
      if ( isset ( $store [$name] ) )
        return padAtSearch ( $store [$name], $names );
    }

    if ( count ($parts) == 2 ) {
      list ( $first, $second ) = $parts;
      if ( $first == 'sequence' or $first == 'sequences' )
        if ( isset ( $store [$second] ) )
          return padAtSearch ( $store [$second], $names );
    }

    if ( count ($parts) == 1 ) {      
      $name = reset ($parts);
      if ( $name == 'sequence' or $name == 'sequences' )
        return padAtSearch ( $store, $names );
    }

    return INF;

  }


  function padAtGlobals ( $names, $parts ) {

    if ( count ($parts) <> 1 )
      return INF;

    $array = reset ($parts);

    return padAtglobals2 ( $array, $names, $GLOBALS );

  }


  function padAtGlobals2 ( $array, $names, $search ) {

    if ( isset ( $search [$array] ) and is_array ( $search [$array] ) ) {
      $check = padAtSearch ( $search [$array], $names, 1 );
      if ( $check !== INF ) 
        return $check;
    }

    foreach ( $search as $k => $v )
      if ( padValidStore ($k) and is_array ($v) ) {
        $check = padAtGlobals2 ( $array, $names, $v );
        if ( $check !== INF ) 
          return $check;
      }

    return INF;

  }


  function padAtTags ( $name, $names, $first, $second, $cor ) {

                     $padIdx = padAtIsTag   ( $first, $cor );
    if ( ! $padIdx ) $padIdx = padAtIsLevel ( $first, $cor );

    if ( ! $padIdx )
      return INF;

    if ( $second )
      return padAtGroup ( $second, $name, $names, $padIdx, $cor );

    $current = padAtProperty ( $names, $padIdx, $cor );
    if ( $current !== INF ) 
      return $current;

    return include pad . 'at/any/tag.php';

  }


  function padAtGroups ( $name, $names, $first, $second, $cor ) {

    global $pad;

    if ( ! $second and file_exists ( pad . "at/groups/$first.php") )

      for ( $padLoop=$pad; $padLoop; $padLoop-- ) {

        $padIdx = $padLoop + $cor;

        $check = padAtGroup ( $first, $name, $names, $padIdx, $cor );
        if ( $check !== INF )
          return $check;

      }

    return INF;

  }


  function padAtProperty ( $names, $padIdx, $cor ) {

    $name = reset ( $names );

    if ( ! file_exists ( pad . "at/properties/$name.php") ) 
      return INF;

    if     ( count ( $names ) == 1 ) $parm = '';
    elseif ( count ( $names ) == 2 ) $parm = end ($names);
    else                             return INF;

    if ( padXapp )
      padXapp ( 'at', 'properties', $name );


    return include pad . "at/properties/$name.php";

  }


  function padAtGroup ( $group, $name, $names, $padIdx, $cor ) {

    if ( ! file_exists ( pad . "at/groups/$group.php" ) )
      return INF;

    if ( padXapp )
      padXapp ( 'at', 'groups', $group );

    return include pad . "at/groups/$group.php";

  }


  function padAtType ( $go, $type, $names, $cor ) {

    if ( ! file_exists ( pad . "at/types/$go.php" ) )
      return INF;

    if ( padXapp )
      padXapp ( 'at', 'types', $go );

    return include pad . "at/types/$go.php";

  }


  function padAtData ( $names, $parts ) {

    global $padDataStore;

    $data = implode ( '.', $parts );

    if ( isset ( $padDataStore [$data] ) )

      $check = padAtSearch ( $padDataStore [$data], $names );
    
    else {

      $store = padData ( $data );
      $check = padAtSearch ( $store, $names );

      if ( $check !== INF ) 
        $padDataStore [$data] = $store;

    }

    return $check;

  }


?>