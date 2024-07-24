<?php


  function padAt ( $names, $parts, $cor ) {

    $name = reset ($names);

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

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

    $check = padAtData ( $names, $parts );
    if ( $check !== INF )
      return $check;

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