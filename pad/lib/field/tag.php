<?php

  function padTag ( $field, $padIdx ) {

    if ( substr($field, 0, 1) == '^' ) {
      $temp  = padExplode ($field, '^', 2);
      $field = $temp[0];
      $parm  = $temp[1];
    } else {
      $parm  = '';
    }
    
    if ( file_exists ( PAD . "pad/tag/".$field.".php" ) )
      return include PAD . "pad/tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $padIdx and isset($GLOBALS ['padCurrent'] ) ) 
      if ( isset  ($GLOBALS ['padCurrent'] [$padIdx] ) ) {
        $pados = 1;
        foreach( $GLOBALS ['padCurrent'] [$padIdx] as $key => $value )
          if ( $pados++ == $field )
            return ( $parm == 'name') ? $key : $value;
      }

    return INF;

  }

?>