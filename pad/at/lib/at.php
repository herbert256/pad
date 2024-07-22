<?php


  function padAt ( $names, $parts, $cor ) {

    $name = end ($names);

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

    return padAtTypes ( $name, $names, $first, $second, $cor );
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
      if ( file_exists ( pad . "at/groups/$second.php") )
        return include pad . "at/groups/$second.php";
      else
        return INF;

    if ( count ( $names ) == 1 and file_exists ( pad . "at/properties/$name.php") ) 
      return include pad . "at/properties/$name.php";

    return include pad . 'at/any/tag.php';

  }


  function padAtGroups ( $name, $names, $first, $second, $cor ) {

    global $pad;

    if ( ! $second and file_exists ( pad . "at/groups/$first.php") ) {


      for ( $padLoop=$pad; $padLoop; $padLoop-- ) {

        $padIdx = $padLoop + $cor;

        $check = include pad . "at/groups/$first.php";
        if ( $check !== INF )
          return $check;

      }

    }

    return INF;

  }


  function padAtTypes ( $name, $names, $first, $second, $cor ) {

    global $pad;

    $type = $second;

    if ( file_exists ( pad . "at/types/$first.php") )  
      return include pad . "at/types/$first.php";

    if ( $first == 'any' ) 
      return include pad . 'at/any/type.php';

    return INF;

  }  


?>