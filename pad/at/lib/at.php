<?php


  function padAt ( $names, $parts, $cor ) {

    $name = end ($names);

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

    $first  = $parts [0] ?? '';
    $second = $parts [1] ?? '';

    $check = padAtTag ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtGroup ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    return include pad . 'at/any/tags.php';

  }


  function padAtTag ( $name, $names, $first, $second, $cor ) {

    $padIdx = 0;

    if     ( padIsTag   ( $first, $cor ) ) $padIdx = padIsTag   ( $first, $cor );
    elseif ( padIsLevel ( $first, $cor ) ) $padIdx = padIsLevel ( $first, $cor ); 

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


  function padAtGroup ( $name, $names, $first, $second, $cor ) {

    global $pad;

    if ( $second or ! file_exists ( pad . "at/groups/$first.php") )
      return INF;

    if ( file_exists ( pad . "at/groups/$first.php") ) 

      for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

        $padIdx = $padIdx + $cor;

        $check = includep ad . "at/groups/$first.php";
        if ( $check !== INF )
          return $check;

      }

    return INF;

  }


?>