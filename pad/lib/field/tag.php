<?php

  function padTag ( $field, $padIdx, $type ) {

    if ( substr($field, 0, 1) == '^' )
      list ( $field, $parm ) = explode ('#', $field, 2);
    else
      $parm  = '';
  
    if ( file_exists ( PAD . "pad/tag/".$field.".php" ) )
      if ( $type == 7 )
        return TRUE;
      else
        return include PAD . "pad/tag/$field.php";
 
    if ( in_array ( $parm, ['name','value'] ) and $padIdx and isset($GLOBALS ['padCurrent'] ) ) 
      if ( isset  ($GLOBALS ['padCurrent'] [$padIdx] ) ) {
        $pados = 1;
        foreach( $GLOBALS ['padCurrent'] [$padIdx] as $key => $value )
          if ( $pados++ == $field )
            if ( $type == 7 )
              return TRUE;
            else
              return ( $parm == 'name') ? $key : $value;
      }

    return INF;

  }

?>